<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 6/4/13
 * Time: 10:49 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Test;



use Doi\HttpExceptionHandlerServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionHandlerServiceProviderTest extends WebTestCase
{
	public function setUp()
	{
		parent::setUp();


		$this->app->register(new HttpExceptionHandlerServiceProvider());

		$this->app->get('/error400', function() {
			throw new HttpException(400, 'Bad request');
		});

		$this->app->get('/error404', function() {
			throw new HttpException(404, 'Not found');
		});

		$this->app->get('/error500', function() {
			throw new HttpException(500, 'Server error');
		});
	}


	public function testErrorResponses()
	{
		$response = $this->request('GET', '/error400');
		$this->assertEquals($response->getStatusCode(), 400);
		$json = $response->getJson();
		$this->assertEquals('Bad request', $json['message']);

		$response = $this->request('GET', '/error404');
		$this->assertEquals($response->getStatusCode(), 404);
		$json = $response->getJson();
		$this->assertEquals('Not found', $json['message']);

		$response = $this->request('GET', '/error500');
		$this->assertEquals($response->getStatusCode(), 500);
		$json = $response->getJson();
		$this->assertEquals('Server error', $json['message']);
	}
}
