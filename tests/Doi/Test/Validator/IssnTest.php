<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/29/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Test\Validator;


use Doi\Validator\Issn;

class IssnTest extends \PHPUnit_Framework_TestCase
{


	public function getInvalidIssns()
	{
		$issns = array(
			'', '666', 'spatne Issn',
			'112011776z', '1120120304',
			'1135777285', 'isn 113-57-79-83X',
			'1130---998722', '1140001',
		);
		return array_map(function($issn) {
			return array($issn);
		}, $issns);
	}

	public function getValidIssns()
	{
		$issns = array(
			' ISSN 1214-4029',
			'12144029',
			'0378-5955',
		);

		return array_map(function($issn) {
			return array($issn);
		}, $issns);
	}

	/**
	 * @dataProvider getValidIssns
	 * @param $issn
	 */
	public function testValidateValidIssn($issn)
	{
		$v = new Issn();
		$this->assertTrue($v->validate($issn), sprintf('Valid Issn: %s', $issn));
	}

	/**
	 * @dataProvider getInvalidIssns
	 * @param $issn
	 */
	public function testValidateInvalidIssn($issn)
	{
		$v = new Issn();
		$this->assertFalse($v->validate($issn), sprintf('Invalid Issn: %s', $issn));
	}
}
