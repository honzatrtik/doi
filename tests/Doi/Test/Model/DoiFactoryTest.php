<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/7/13
 * Time: 12:39 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Test\Model;


use Doi\DoiNumberGenerator;
use Doi\Entity\DoiRequest;
use Doi\Model\DoiFactory;
use Doi\Test\WebTestCase;

class DoiFactoryTest extends WebTestCase
{


	protected function getDoiNumberGeneratorMock($doiNumber)
	{
		$mock = $this->getMock('\Doi\DoiNumberGenerator', array('generate'), array(), '', FALSE);
		$mock->expects($this->once())
			->method('generate')
			->with($this->anything())
			->will($this->returnValue($doiNumber));

		return $mock;
	}

	public function testCreateFromRequest()
	{
		$doiNumber = 'someNumber';
		$f = new DoiFactory($this->getDoiNumberGeneratorMock($doiNumber));
		$doiRequest = new DoiRequest();

		$doi = $f->createFromRequest($doiRequest);

		$this->assertInstanceOf('\Doi\Entity\Doi', $doi, '::createFromRequest return instance of \Doi\Entity\Doi');
		$this->assertEquals($doiRequest, $doi->getDoiRequest());
		$this->assertEquals($doiNumber, $doi->getDoi(), $doiNumber);
	}

}
