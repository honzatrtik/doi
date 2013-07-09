<?php

namespace Doi\Test;


use Doi\SequenceGenerator;

class SequenceGeneratorTest extends WebTestCase
{
	public function testGenerate()
	{
		$this->doctrineOrmDropCreate();
		$g = new SequenceGenerator($this->app['orm.em']);

		$numbers = array();
		$i = 100;
		while ($i--)
		{
			$numbers[] = $g->generate();
		}

		$this->assertEquals($numbers, array_unique($numbers));
	}
}
