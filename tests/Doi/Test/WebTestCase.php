<?php

namespace Doi\Test;

 use Doi\Entity\User;
 use Symfony\Component\Console\Application;
 use Symfony\Component\Console\Input\StringInput;
 use Symfony\Component\Console\Output\ConsoleOutput;
 use Symfony\Component\Console\Output\NullOutput;
 use Symfony\Component\Console\Tester\CommandTester;
 use Symfony\Component\HttpKernel\HttpKernel;


class Response extends \Symfony\Component\HttpFoundation\Response
{
	/**
	 * @return mixed
	 */
	public function getJson()
	{
		return json_decode($this->getContent(), TRUE);
	}
}


class WebTestCase extends \Silex\WebTestCase
{

	/**
	 * @var \Silex\Application
	 */
	protected $app;

	/**
	 * Creates the application.
	 *
	 * @return \Silex\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__ . '/../../../bootstrap.php';
		$app->error(function (\Exception $e, $code) use ($app) {
			if (!$e instanceof \Symfony\Component\HttpKernel\Exception\HttpException)
			{
				throw $e;
			}
		});
		return $app;
	}

	protected function setUser($username, $password)
	{
		$user = new User();
		$user->setUsername($username);
		$user->setPassword($password);

		/** @var EntityManager $em */
		$em = $this->app['orm.em'];
		$em->persist($user);
		$em->flush();

		return $this->app['user'] = $user;
	}

	protected function doctrineOrmDropCreate()
	{
		$application = new Application();

		$application->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet(array(
			'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($this->app['orm.em'])
		)));

		$application->addCommands(array(
			new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
		));

		$application->doRun(new StringInput('orm:schema-tool:drop --force'), new ConsoleOutput());
		$application->doRun(new StringInput('orm:schema-tool:create'), new ConsoleOutput());
	}

	/**
	 * @return Response
	 */
	protected function request($method, $uri, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		$client = $this->createClient();
		$client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
		$response =  $client->getResponse();

		return new Response(
			$response->getContent(),
			$response->getStatusCode(),
			$response->headers->all()
		);
	}
}