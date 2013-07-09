<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 6/5/13
 * Time: 12:41 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Test\Crossref;


use Doi\Crossref\BatchArticleDepositResultEvent;
use Doi\Crossref\Depositor;
use Doi\Crossref\DepositResultEvent;
use Doi\Entity\Author;
use Doi\Entity\Batch;
use Doi\Entity\BatchArticle;
use Doi\Entity\Doi;
use Doi\Entity\User;
use Doi\JournalConfig;
use Doi\Test\WebTestCase;
use Guzzle\Http\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DepositorTest extends WebTestCase
{
	/**
	 * @var Depositor
	 */
	protected $depositor;

	public function setUp()
	{
		parent::setUp();

		$client = new Client($this->app['crossref.baseUrl']);
		$this->depositor = new Depositor(
			$client,
			$this->app['crossref.username'],
			$this->app['crossref.password']
		);

		/** @var EventDispatcher $app['dispatcher'] */
		$this->depositor->setDispatcher($this->app['dispatcher']);
	}

	/**
	 * @return Batch
	 */
	protected function getBatch()
	{
		$batch = new Batch();
		$batch->setBatchId('0666');
		$batch->setType(Batch::TYPE_JOURNAL);
		$batch->setVolume(12);
		$batch->setYear(2012);
		$batch->setIssue(4);
		$batch->setIsn('00380288');

		$batchArticle = new BatchArticle();
		$batchArticle->setTitle('The ‘Hilton’ as a Fecal Court: The Socio-spatial Aspects of Homelessness');
		$batchArticle->setUrl('http://sreview.soc.cas.cz/en/issue/164-sociologicky-casopis-czech-sociological-review-2-2013/3319');
		$batchArticle->setPage(23);

		$user = new User();
		$user->setUsername('honza.trtik@gmail.com');

		$doi = new Doi();
		$doi->setDoi('10.13060/issn.00380288.49.1');
		$doi->setUser($user);

		$batchArticle->setDoi($doi);

		$author = new Author();
		$author->setName('Ondřej');
		$author->setSurname('Hejnal');

		$batchArticle->addAuthor($author);

		$batch->addBatchArticle($batchArticle);

		return $batch;
	}


	public function testCreateDepositXml()
	{
		$config = new JournalConfig();
		$config->setIssn('00380288');
		$config->setName('Czech Sociological Review');
		$config->setLanguage('en');

		$name = tempnam(sys_get_temp_dir(), __FUNCTION__);
		$xml = $this->depositor->createDepositXml($this->getBatch(), $config, 'honza.trtik@gmail.com');

		$this->assertInstanceOf('\XmlWriter', $xml);

		$string = $xml->outputMemory(true);

		$name = tempnam(sys_get_temp_dir(),  __CLASS__);

		file_put_contents($name, $string);

		$dom = new \DOMDocument();
		$dom->load($name);

		$valid = $dom->schemaValidate(__DIR__ . '/../../../../schema/crossref4.3.1.xsd');
		$this->assertTrue($valid);

	}

	public function testDeposit()
	{
		$config = new JournalConfig();
		$config->setIssn('00380288');
		$config->setName('Czech Sociological Review');
		$config->setLanguage('en');

		/** @var  \Guzzle\Http\Message\Response $result */
		$result = $this->depositor->deposit($this->getBatch(), $config, 'honza.trtik@gmail.com');
		$this->assertInstanceOf('\Guzzle\Http\Message\Response', $result);

		$this->assertEquals(200, $result->getStatusCode());
		$this->assertContains('SUCCESS', $result->getBody(true));
	}

	public function testCheckDepositResult()
	{
		/** @var EventDispatcher $dispatcher */
		$dispatcher = $this->app['dispatcher'];


		$test = $this;
		$called = 0;
		$dispatcher->addListener(BatchArticleDepositResultEvent::EVENT_NAME, function(BatchArticleDepositResultEvent $event) use (& $called, $test) {

			$called++;

			$test->assertEquals('10.13060/issn.00380288.49.1', $event->getDoi());
			$test->assertEquals('success', strtolower($event->getStatus()));

		});

		$count = $this->depositor->checkDepositResult($this->getBatch());


		$this->assertGreaterThan(0, $count);
		$this->assertGreaterThan(0, $called);
	}



}
