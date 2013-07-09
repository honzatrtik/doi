<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/30/13
 * Time: 10:09 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Security;


use Silex\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RequestSecurityServiceProvider implements ServiceProviderInterface
{
	const HEADER_USERNAME = 'X-Username';
	const HEADER_PASSWORD = 'X-Password';

	/**
	 * @var AuthenticatorInterface
	 */
	protected $authenticator;

	function __construct(AuthenticatorInterface $authenticator)
	{
		$this->authenticator = $authenticator;
	}


	/**
	 * @param Application $app An Application instance
	 */
	public function register(Application $app)
	{
		$authenticator = $this->authenticator;


		$app->before(function(Request $request) use ($authenticator, $app) {

			$username = $request->headers->get(RequestSecurityServiceProvider::HEADER_USERNAME);
			$password = $request->headers->get(RequestSecurityServiceProvider::HEADER_PASSWORD);
			try
			{
				$user = $authenticator->authenticate($username, $password);
				$app['user'] = $user;
			}
			catch(AuthenticatorException $e)
			{
				$app->abort(403, sprintf(
					'Invalid credentials, set right headers %s & %s',
					RequestSecurityServiceProvider::HEADER_USERNAME,
					RequestSecurityServiceProvider::HEADER_PASSWORD
				));
			}

		}, Application::EARLY_EVENT);
	}


	public function boot(Application $app)
	{
	}

}