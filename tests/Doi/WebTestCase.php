<?php

namespace Doi\Test;

require_once __DIR__ . '/../../vendor/autoload.php';

abstract class WebTestCase extends \Silex\WebTestCase
{
	/**
	 * Creates the application.
	 *
	 * @return HttpKernel
	 */
	public function createApplication()
	{
		return require __DIR__ . '/../../bootstrap.php';
	}
}