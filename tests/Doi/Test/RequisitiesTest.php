<?php

namespace Doi\Test;


class RequisitiesTest extends WebTestCase
{

	public function testServices()
	{
		$this->assertInstanceOf('\Doctrine\DBAL\Connection', $this->app['db']);
		$this->assertInstanceOf('\Doctrine\Common\EventManager', $this->app['db.event_manager']);
	}

}