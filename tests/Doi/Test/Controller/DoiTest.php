<?php

namespace Doi\Test\Controller;



use Doi\Controller\Doi;
use Doi\Entity;
use Doi\Test\WebTestCase;

class DoiTest extends WebTestCase
{


	public function createApplication()
	{
		$app =  parent::createApplication();

		$app['session.test'] = TRUE;
		$app['controller'] = $controller = new Doi($app['orm.em'], $app['doi.factory']);

		$app->post('/doi', 'controller:post');
		$app->get('/doi/{doi}', 'controller:getDoi')
			->bind('doi_get_doi')
			->assert('doi', $controller::DOI_PATTERN_REGEX)
			->convert('doi', array($controller, 'findDoiByDoi'));

		$app->delete('/doi/{doi}', 'controller:deleteDoi')
			->assert('doi', $controller::DOI_PATTERN_REGEX)
			->convert('doi', array($controller, 'findDoiByDoi'));

		$app->get('/doi', 'controller:get')
			->bind('doi_get')
			->assert('doi', $controller::DOI_PATTERN_REGEX)
			->convert('doi', array($controller, 'findDoiByDoi'));

		$app->get('/batch/{doi}/batches', function() {})
			->bind('batch_get_batches_by_doi');

		return $app;
	}


	public function getPostValidatorDataProvider()
	{
		return array(
			array(
				array(
					'type' => 'journal',
					'isn' => '12144029',
					'issue' => '1',
					'volume' => '4',
				),
				TRUE,
			),
			array(
				array(
					'type' => 'journal',
					'isn' => '12144029',
					'issue' => '1',
				),
				FALSE,
			),
			array(
				array(
					'type' => 'book',
					'isn' => '',
					'issue' => '1',
				),
				FALSE,
			),
			array(
				array(
					'type' => 'book',
					'isn' => 'ISBN 978-9930943106',
				),
				TRUE,
			),
		);
	}

	/**
	 * @dataProvider getPostValidatorDataProvider
	 */
	public function testPostValidator($data, $valid)
	{
		$app = $this->createApplication();
		$v = $app['controller']->getPostDoiValidator();
		$this->assertEquals($valid, $v->validate($data), var_export($data, TRUE));
	}


	/**
	 * @dataProvider getPostValidatorDataProvider
	 */
	public function testPost($data, $valid)
	{
		$this->doctrineOrmDropCreate();
		$response = $this->request('POST', '/doi', $data);
		$this->assertEquals($valid ? 201 : 400, $response->getStatusCode());
		if ($valid)
		{
			$json = $response->getJson();
			$this->assertArrayHasKey('url', $json);
			return $json['url'];
		}
	}

	public function testGet()
	{
		$this->doctrineOrmDropCreate();

	}


	public function testPostDuplicate()
	{
		$this->doctrineOrmDropCreate();

		$data = array(
			'type' => 'book',
			'isn' => 'ISBN 978-9930943106',
		);

		$response1 = $this->request('POST', '/doi', $data);
		$response2 = $this->request('POST', '/doi', $data);

		$this->assertEquals(201, $response1->getStatusCode());
		$this->assertEquals(400, $response2->getStatusCode());


	}

	/**
	 * @dataProvider getPostValidatorDataProvider
	 * @depends testPost
	 */
	public function testGetDoi($data, $valid)
	{
		$this->doctrineOrmDropCreate();
		if (!$valid)
		{
			return;
		}

		$response = $this->request('POST', '/doi', $data);
		$json = $response->getJson();

		$response = $this->request('GET', $json['url']);
		$json = $response->getJson();var_dump($json);
		$this->assertEquals(200, $response->getStatusCode());

		$keys = array(
			"status",
			"type", 
			"isn",
			"issue",
			"volume",
			"reservedBy",
			"reserved",
			"batchesUrl"
		);

		foreach($keys as $key)
		{
			$this->assertArrayHasKey($key, $json);
		}
	}

	public function testDeleteDoi()
	{
		$this->doctrineOrmDropCreate();

		$data = array(
			'type' => 'book',
			'isn' => 'ISBN 978-9930943106',
		);

		$response = $this->request('POST', '/doi', $data);
		$json = $response->getJson();

		$response = $this->request('DELETE', $json['url']);
		$this->assertEquals(204, $response->getStatusCode());

	}
}
