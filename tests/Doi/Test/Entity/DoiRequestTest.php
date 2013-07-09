<?php

namespace Doi\Test\Entity;


use Doi\Entity\DoiRequest;

class DoiRequestTest extends \PHPUnit_Framework_TestCase
{

	public function getIsnAsNumberDataProvider()
	{
		return array(
			array(
				' ISBN 123-752-959X', '123752959X',
			),
			array(
				'ISSN 1214-4029 ', '12144029',
			),
		);

	}

	/**
	 * @dataProvider getIsnAsNumberDataProvider
	 */
	public function testGetIsnAsNumber($isn, $isnAsNumber)
	{
		$r = new DoiRequest();
		$r->setIsn($isn);

		$this->assertEquals($isnAsNumber, $r->getIsnAsNumber(), $isn . ' -> ' . $isnAsNumber);
	}
}
