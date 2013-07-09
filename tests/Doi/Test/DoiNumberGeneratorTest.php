<?php

namespace Doi\Test;


use Doctrine\Tests\ORM\Id\SequenceGeneratorTest;
use Doi\DoiNumberGenerator;
use Doi\Entity\DoiRequest;

class DoiNumberGeneratorTest extends \PHPUnit_Framework_TestCase
{

	const CONST_SEQUENCE = 1;
	const CONST_PREFIX = 'prefix';

	public function generateDataProvider()
	{
		return array(
			array(
				array(
					'type' => DoiRequest::TYPE_JOURNAL,
					'isn' => '0038-0288',
					'issue' => 1,
					'volume' => 49,
				),
				self::CONST_PREFIX . '/issn.00380288.49.1.' . self::CONST_SEQUENCE,
			),
			array(
				array(
					'type' => DoiRequest::TYPE_BOOK,
					'isn' => '80-204-0105-9',
				),
				self::CONST_PREFIX . '/isbn.8020401059'
			)
		);
	}

	/**
	 * @dataProvider generateDataProvider
	 * @param $data
	 * @param $doi
	 */
	public function testGenerate($data, $doi)
	{
		$doiRequest = new DoiRequest();
		foreach($data as $name => $value)
		{
			$setter = sprintf('set%s', ucfirst($name));
			$doiRequest->$setter($value);
		}

		$mock = $this->getMockForAbstractClass('\Doi\SequenceGeneratorInterface', array('generate'));
		$mock->expects($doiRequest->getType() == DoiRequest::TYPE_JOURNAL ? $this->once() : $this->never())
			->method('generate')
			->will($this->returnValue(self::CONST_SEQUENCE));

		$generator = new DoiNumberGenerator($mock, self::CONST_PREFIX);
		$generatedDoi = $generator->generate($doiRequest);
		$this->assertEquals($doi, $generatedDoi);
	}
}
