<?php

namespace Doi\Test;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/WebTestCase.php';


class RequisitiesTest extends WebTestCase
{

	public function testServices()
	{
		$this->assertInstanceOf('\Doctrine\DBAL\Connection', $this->app['db']);
		$this->assertInstanceOf('\Doctrine\Common\EventManager', $this->app['db.event_manager']);
	}

}