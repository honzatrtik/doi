<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/30/13
 * Time: 10:11 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Test\Security;

use Doi\Entity\User;
use Doi\Security\Authenticator;
use Doi\Security\RequestSecurityServiceProvider;
use Doi\Test\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class RequestSecurityServiceProviderTest extends WebTestCase
{
	public function setUp()
	{
		parent::setUp();

		$test = $this;
		$this->app->register(new RequestSecurityServiceProvider($this->app['security.authenticator']));
		$this->app->get('/', function(Application $app) use ($test) {

			$test->assertInstanceOf('\Doi\Entity\User', $app['user']);

			return new Response('Ok!');
		});
	}


	public function testNoCredentials()
	{
		$this->doctrineOrmDropCreate();
		$response = $this->request('GET', '/');

		$this->assertEquals(403, $response->getStatusCode());

	}


	public function testInvalidCredentials()
	{
		$this->doctrineOrmDropCreate();

		$em = $this->app['orm.em'];

		$user = new User();
		$user->setUsername('honzik@honzik.cz');
		$user->setPassword($this->app['security.authenticator']->generatePassword('heslicko'));

		$em->persist($user);
		$em->flush();


		$response = $this->request('GET', '/', array(), array(), array(
			'HTTP_' . str_replace('-', '_', strtoupper(RequestSecurityServiceProvider::HEADER_USERNAME)) => 'invalid',
			'HTTP_' . str_replace('-', '_', strtoupper(RequestSecurityServiceProvider::HEADER_PASSWORD)) => 'invalid',
		));

		$this->assertEquals(403, $response->getStatusCode());

	}

	public function testValidCredentials()
	{
		$this->doctrineOrmDropCreate();

		$em = $this->app['orm.em'];

		$user = new User();
		$user->setUsername('honzik@honzik.cz');
		$user->setPassword($this->app['security.authenticator']->generatePassword('heslicko'));

		$em->persist($user);
		$em->flush();


		$response = $this->request('GET', '/', array(), array(), array(
			'HTTP_' . str_replace('-', '_', strtoupper(RequestSecurityServiceProvider::HEADER_USERNAME)) => 'honzik@honzik.cz',
			'HTTP_' . str_replace('-', '_', strtoupper(RequestSecurityServiceProvider::HEADER_PASSWORD)) => 'heslicko',
		));

		$this->assertEquals(200, $response->getStatusCode());

	}
}
