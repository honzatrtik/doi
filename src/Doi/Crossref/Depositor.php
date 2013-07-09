<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 6/5/13
 * Time: 12:33 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Crossref;


use Doi\Entity\Author;
use Doi\Entity\Batch;
use Doi\Entity\BatchArticle;
use Doi\JournalConfig;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Depositor
{
	const DEPOSIT_URL = '/servlet/deposit{?queryParams*}';
	const DEPOSIT_GET_RESULT_URL = '/servlet/submissionDownload{?queryParams*}';

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var EventDispatcher
	 */
	protected $dispatcher;

	function __construct(Client $client, $username, $password)
	{
		if (!$client->getBaseUrl())
		{
			throw new \InvalidArgumentException('Client must have baseUrl set.');
		}

		$this->client = $client;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
	 */
	public function setDispatcher($dispatcher)
	{
		$this->dispatcher = $dispatcher;
		return $this;
	}

	/**
	 * @return \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}


	/**
	 * @param Batch $batch
	 * @param JournalConfig $config
	 * @param $email
	 * @return array
	 */
	public function deposit(Batch $batch, JournalConfig $config, $email)
	{

		/** @var EntityEnclosingRequest $message */
		$message = $this->client->post(array(
			self::DEPOSIT_URL,
			array(
				'queryParams' => array(
					'login_id' => $this->username,
					'login_passwd' => $this->password,
				),
			)
		), array(
			'Expect' => null // @see https://github.com/guzzle/guzzle/issues/185
		));

		$file = tempnam(sys_get_temp_dir(), 'deposit');
		$xml = $this->createDepositXml($batch, $config, $email);
		file_put_contents($file, $xml->outputMemory(true));

		$message->addPostFiles(array('fname' => $file));
		$result = $message->send();
		$batch->setDeposited(new \DateTime('now'));

		return $result;
	}


	public function checkDepositResult(Batch $batch)
	{
		$message = $this->client->get(array(self::DEPOSIT_GET_RESULT_URL, array(
			'queryParams' => array(
				'usr' => $this->username,
				'pwd' => $this->password,
				'doi_batch_id' => $batch->getBatchIdPadded(),
				'type' => 'result',
			)
		)));

		$result = $message->send();
		$xml = new \SimpleXMLElement($result->getBody(true));
		if ($elements = $xml->xpath('/doi_batch_diagnostic[@status="completed"]'))
		{
			if ($this->dispatcher)
			{
				$this->dispatcher->dispatch(
					BatchDepositResultEvent::EVENT_NAME,
					new BatchDepositResultEvent($batch)
				);
			}

			/** @var \SimpleXMLElement $element */
			$root = current($elements);

			$records = $root->xpath('record_diagnostic');

			if ($this->dispatcher)
			{
				foreach($records as $record)
				{
					/** @var \SimpleXMLElement $record */

					$doi = (string) $record->doi[0];
					$status = (string) $record['status'];
					$message = (string) $record->msg[0];

					$this->dispatcher->dispatch(
						BatchArticleDepositResultEvent::EVENT_NAME,
						new BatchArticleDepositResultEvent($batch, $doi, $status, $message)
					);

				}

			}

			return count($records);
		}

		return false;
	}

	public function createDepositXml(Batch $batch, JournalConfig $config, $email)
	{
		if ($batch->getType() !== Batch::TYPE_JOURNAL)
		{
			throw new \InvalidArgumentException(sprintf('Only "%s" type is supperted.', Batch::TYPE_JOURNAL));
		}

		$xml = new \XMLWriter();
		$xml->openMemory();
		$xml->setIndent(TRUE);

		$xml->startDocument();

		$xml->startElement('doi_batch');
		$xml->writeAttribute('version', '4.3.1');
		$xml->writeAttribute('xmlns', 'http://www.crossref.org/schema/4.3.1');

		$xml->startElement('head');
		$xml->writeElement('doi_batch_id', $batch->getBatchIdPadded());
		$xml->writeElement('timestamp', time());

		$xml->startElement('depositor');
		$xml->writeElement('name', 'Institute of Sociology of the Academy of Sciences of the Czech Republic');
		$xml->writeElement('email_address', $email);
		$xml->endElement(); // depositor

		$xml->writeElement('registrant', $config->getName());

		$xml->endElement(); // head

		$xml->startElement('body');
		$xml->startElement('journal');


		$xml->startElement('journal_metadata');
		$xml->writeAttribute('language', $config->getLanguage());

		$xml->writeElement('full_title', $config->getName());
		$xml->startElement('issn');
		$xml->writeAttribute('media_type', 'print');
		$xml->text($config->getIssn());
		$xml->endElement();

		$xml->endElement(); // journal_metadata

		$xml->startElement('journal_issue');

		$xml->startElement('publication_date');
		$xml->writeAttribute('media_type', 'print');
		$xml->writeElement('year', $batch->getYear());
		$xml->endElement();

		$xml->startElement('journal_volume');
		$xml->writeElement('volume', $batch->getVolume());
		$xml->endElement();

		$xml->writeElement('issue', $batch->getIssue());

		$xml->endElement(); // journal_issue


		foreach($batch->getBatchArticles() as $article)
		{
			/** @var BatchArticle $article */

			$xml->startElement('journal_article');
			$xml->writeAttribute('publication_type', 'full_text');
			$xml->startElement('titles');
			$xml->writeElement('title', $article->getTitle());
			$xml->endElement();

			$xml->startElement('contributors');
			foreach($article->getAuthors() as $author)
			{
				/** @var Author $author */

				$xml->startElement('person_name');

				$xml->writeAttribute('sequence', 'first');
				$xml->writeAttribute('contributor_role', 'author');

				$xml->writeElement('given_name', $author->getName());
				$xml->writeElement('surname', $author->getSurname());

				$xml->endElement();
			}
			$xml->endElement(); // contributors

			$xml->startElement('publication_date');
			$xml->writeAttribute('media_type', 'print');
			$xml->writeElement('year', $batch->getYear()); /* TODO */
			$xml->endElement();

			$xml->startElement('pages');
			$xml->writeElement('first_page', $article->getPage());
			$xml->endElement();


			$xml->startElement('doi_data');
			$xml->writeElement('doi', $article->getDoi()->getDoi());
			$xml->writeElement('timestamp', time());
			$xml->writeElement('resource', $article->getUrl());
			$xml->endElement();

			$xml->endElement(); // journal_article
		}

		$xml->endElement(); // journal
		$xml->endElement(); // body

		$xml->endElement(); // doi_batch

		$xml->endDocument();

		return $xml;
	}


}