<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/30/13
 * Time: 10:09 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi;


use Silex\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionHandlerServiceProvider implements ServiceProviderInterface
{

	/**
	 * @param Application $app An Application instance
	 */
	public function register(Application $app)
	{
		$app->error(function(HttpException $e) use ($app) {
			return $app->json(array(
				'message' => $e->getMessage(),
			), $e->getStatusCode());
		});
	}


	public function boot(Application $app)
	{
	}

}