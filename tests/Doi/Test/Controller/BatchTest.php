<?php

namespace Doi\Test\Controller;



use Doctrine\ORM\EntityManager;
use Doi\Controller\Batch;
use Doi\Entity\DoiRequest;
use Doi\Entity\User;
use Doi\Model\DoiFactory;
use Doi\Security\Authenticator;
use Doi\Test\WebTestCase;
use Respect\Validation\Validator;

class BatchTest extends WebTestCase
{


	public function createApplication()
	{
		$app =  parent::createApplication();

		$app['session.test'] = TRUE;
		$app['controller'] = $controller = new Batch($app['orm.em'], $app['crossref.depositor'], $app['journalConfig']);

		$app->post('/batch', 'controller:post');

		$app->get('/batch/{batch}', 'controller:get')
			->bind('batch.get');

		return $app;
	}


	public function getPostValidatorDataProvider()
	{
		$valid = json_decode(file_get_contents(__DIR__ . '/data/batchControllerPostDataValid.json'), TRUE);
		$validData = array_map(function($item) {
			return array($item, TRUE);
		}, $valid);

		$invalid = json_decode(file_get_contents(__DIR__ . '/data/batchControllerPostDataInvalid.json'), TRUE);
		$invalidData = array_map(function($item) {
			return array($item, FALSE);
		}, $invalid);

		return array_merge($invalidData, $validData);
	}



	/**
	 * @dataProvider getPostValidatorDataProvider
	 */
	public function testPostValidator($data, $valid)
	{

		$this->doctrineOrmDropCreate();
		$app = $this->createApplication();

		/** @var $v Validator **/
		$v = $app['controller']->getPostBatchValidator();

		$result = $v->validate($data);

		if ($valid != $result)
		{
			echo $v->reportError($data)->getFullMessage();
		}


		$this->assertEquals($valid, $result, var_export($data, TRUE));


	}


	/**
	 * @dataProvider getPostValidatorDataProvider
	 */
	public function testPost($data, $valid)
	{
		$this->doctrineOrmDropCreate();
		$this->setUser('honza@trtik.cz', crypt('heslicko', Authenticator::DEFAULT_SALT));

		// Musime si pripravit doi, ktere mame v json
		$request = new DoiRequest();
		$request->setType('journal');
		$request->setIsn('12144029');
		$request->setIssue(1);
		$request->setVolume(4);

		/** @var DoiFactory $factory */
		$factory = $this->app['doi.factory'];
		$doi = $factory->createFromRequest($request);

		/** @var EntityManager $em */
		$em = $this->app['orm.em'];

		$em->persist($request);
		$em->persist($doi);
		$em->flush();


		$response = $this->request('POST', '/batch', $data);

		$this->assertEquals($valid ? 201 : 400, $response->getStatusCode());
		if ($valid)
		{
			$json = $response->getJson();
			$this->assertArrayHasKey('url', $json);

			return $json['url'];
		}
	}

	/**
	 * @dataProvider getPostValidatorDataProvider
	 * @depends testPost
	 */
	public function testGet($data, $valid)
	{
		if ($valid)
		{
			$this->doctrineOrmDropCreate();
			$this->setUser('honza@trtik.cz', crypt('heslicko', Authenticator::DEFAULT_SALT));

			// Musime si pripravit doi, ktere mame v json
			$request = new DoiRequest();
			$request->setType('journal');
			$request->setIsn('12144029');
			$request->setIssue(1);
			$request->setVolume(4);

			/** @var DoiFactory $factory */
			$factory = $this->app['doi.factory'];
			$doi = $factory->createFromRequest($request);

			/** @var EntityManager $em */
			$em = $this->app['orm.em'];

			$em->persist($request);
			$em->persist($doi);
			$em->flush();

			$response = $this->request('POST', '/batch', $data);
			$json = $response->getJson();

			$url = $json['url'];
			$response = $this->request('GET', $url);
			$this->assertEquals(200, $response->getStatusCode());

			$json = $response->getJson();
			$keys = array(
				'batchId',
				'url',
    			'submittedBy',
				'submitted',
				'resultRetrieved',
    			'result',
			);

			foreach($keys as $key)
			{
				$this->assertArrayHasKey($key, $json);
			}
		}
	}

}


