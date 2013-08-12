<?php

namespace Doi\Controller;


use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doi\Crossref\Depositor;
use Doi\Entity;
use Doi\JournalConfigProvider;
use Doi\Model\DoiFactory;
use Doi\Repository\DoiRepository;
use Doi\Validator\Isbn;
use Doi\Validator\Issn;
use Doi\Validator\Url;
use Silex\Application;
use Respect\Validation\Validator as V;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Batch
{


	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var Depositor
	 */
	protected $depositor;


	/**
	 * @var JournalConfigProvider
	 */
	protected $journalConfigProvider;

	function __construct(EntityManager $em, Depositor $depositor, JournalConfigProvider $journalConfigProvider)
	{
		$this->em = $em;
		$this->depositor = $depositor;
		$this->journalConfigProvider = $journalConfigProvider;
	}

	/**
	 * @param $batchId
	 * @return Entity\Batch
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	protected function findBatchByBatchId($batchId)
	{
		if (!$batch = $this->em->getRepository('\Doi\Entity\Batch')->findFull($batchId))
		{
			throw new HttpException(404, 'Batch not found: ' . $batchId);
		}
		return $batch;
	}

	public function getPostBatchValidator()
	{
		$urlValidator = new Url();

		$notEmptyStringValidator = V::allOf(V::string(), V::notEmpty());

		$authorsValidator = V::arr()
			->key('name', $notEmptyStringValidator)
			->key('surname', $notEmptyStringValidator);

		$articlesValidator = V::arr()
			->key('doi',  $notEmptyStringValidator)
			->key('title',  $notEmptyStringValidator)
			->key('authors', V::arr()->each($authorsValidator))
			->key('page', V::regex('/^[0-9]+$/'))
			->key('url', $urlValidator);

		$journalValidator = V::arr()
			->key('type', V::equals('journal'))
			->key('isn', new Issn())
			->key('issue', V::regex('/^[0-9]+$/'))
			->key('volume', V::regex('/^[0-9]+$/'))
			->key('year', V::regex('/^[0-9]{4}$/'))
			->key('articles', V::arr()->each($articlesValidator));

		$bookValidator = V::arr()
			->key('type', V::equals('book'))
			->key('isn', new Isbn())
			->key('year', V::regex('/^[0-9]{4}$/'))
			->key('publisher', V::allOf(V::string(), V::notEmpty()))
			->key('doi',  $notEmptyStringValidator)
			->key('url',  $urlValidator);

		return V::oneOf($journalValidator, $bookValidator);

	}


	public function post(Application $app, Request $request)
	{
		/** @var Entity\User $user */
		$user = $app['user'];

		$post = $request->request->all();
		$validator = $this->getPostBatchValidator();
		if (!$validator->validate($post))
		{
			throw new HttpException(400, 'Invalid parameters.');
		}

		$batch = new Entity\Batch();
		$batch->setType($post['type']);
		$batch->setIsn($post['isn']);
		$batch->setIssue($post['issue']);
		$batch->setVolume($post['volume']);
		$batch->setYear($post['year']);
		$batch->setSubmittedBy($user);

		/** @var DoiRepository $doiRepository */
		$doiRepository = $this->em->getRepository('Doi\Entity\Doi');
		foreach($post['articles'] as $articleData)
		{
			if (!$doi = $doiRepository->findByDoi($articleData['doi']))
			{
				throw new HttpException(400, sprintf('Invalid doi: "%s"', $articleData['doi']));
			}

			$article = new Entity\BatchArticle();
			$article->setDoi($doi);
			$article->setTitle($articleData['title']);
			$article->setUrl($articleData['url']);
			$article->setPage($articleData['page']);

			$batch->addBatchArticle($article);

			foreach($articleData['authors'] as $authorData)
			{
				$author = new Entity\Author();
				$author->setName($authorData['name']);
				$author->setSurname($authorData['surname']);

				$article->addAuthor($author);
				$this->em->persist($author);
			}

			$this->em->persist($article);

		}

		$this->em->persist($batch);


		$issn = $batch->getIsnAsNumber();
		$journalConfig = $this->journalConfigProvider->get($issn);
		$email = $user->getUsername();

		$this->depositor->deposit($batch, $journalConfig, $email);

		$this->em->flush();

		return $app->json(array(
			'doi' => $batch->getBatchId(),
			'url' => $app['url_generator']->generate('batch.get', array(
				'batchId' => $batch->getBatchId(),
			))
		), 201);

	}

	public function get(Application $app, Request $request, $batchId)
	{

		$batch = $this->findBatchByBatchId($batchId);

		$result = array();
		foreach($batch->getBatchArticles() as $batchArticle)
		{
			/** @var Entity\BatchArticle $batchArticle */
			$doi = $batchArticle->getDoi()->getDoi();
			$result[$doi] = array(
				'status' => $batchArticle->getResult(),
				'message' => $batchArticle->getResultMessage(),
			);
		}

		return $app->json(array(
			'batchId' => $batch->getBatchId(),
			'url' => $app['url_generator']->generate('batch.get', array(
				'batchId' => $batch->getBatchId(),
			)),
			'submittedBy' => $batch->getSubmittedBy()->getUsername(),
			'submitted'=> $batch->getCreated(),
			'deposited'=> $batch->getDeposited(),
			'resultRetrieved' => $batch->getResultRetrieved(),
			'result' => $result,
		), 200);
	}

	public function getAllByDoi(Application $app, Request $request, $doi)
	{
		/** @var DoiRepository $doiRepository */
		$doiRepository = $this->em->getRepository('Doi\Entity\Doi');
		if (!$doi = $doiRepository->findByDoi($doi))
		{
			throw new \HttpException(sprintf('Doi not found: "%s"', $doi));
		}

		$repository = $this->em->getRepository('Doi\Entity\Batch');
		$batches = array();
		foreach($repository->findByDoi($doi->getDoi()) as $batch)
		{
			$batches[] = array(
				'url' => $app['url_generator']->generate('batch.get', array(
					'batch' => $batch->getBatchId(),
				)),
				'submittedBy' => $batch->getSubmittedBy()->getUsername(),
				'submitted'=> $batch->getCreated(),
				'deposited'=> $batch->getDeposited(),
				'resultRetrieved' => $batch->getResultRetrieved(),
			);
		}

		return $app->json(array(
			'doi' => $doi->getDoi(),
			'url' => $app['url_generator']->generate('batch.getAllByDoi', array(
				'doi' => $doi->getDoi(),
			)),
			'batches' => $batches,
		), 200);
	}

}
