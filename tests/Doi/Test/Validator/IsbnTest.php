<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/29/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Test\Validator;


use Doi\Validator\Isbn;

class IsbnTest extends \PHPUnit_Framework_TestCase
{


	public function getInvalidIsbns()
	{
		$isbns = array(
			'', '666', 'spatne isbn',
			'112011776z', '1120120304',
			'1135777285', 'isn 113-57-79-83X',
			'1130---998722', '1140001',
		);
		return array_map(function($isbn) {
			return array($isbn);
		}, $isbns);
	}

	public function getValidIsbns()
	{
		$isbns = array(
			'1120117763', '1120120314',
			'1135777284', '113577983X',
			'1139998722', '1140001272',
			'1155164164', '115516671X',
			'1155279360', '1155281918',
			'1155340809', '1155343352',
			'1155694082', '1155696638',
			'115697920X', '1156981751',
			'1158684169', '1158686714',
			'1161159681', '1161162232',
			'1175598089', '1175600636',
			'1184696322', '1184698872',
			'1187673609', '1187676152',
			'1189122561', '1189125110',
			'1189432323', '1189434873',
			'1199004162', '1199006718',
			'1199034886', '1199037435',
			'119928064X', '1199283193',
			'1199336963', '1199339512',
			'1199485446', '1199487996',
			'119997952X', '1199982075',
			'1200017927', '1200020472',
			'1200998405', '1201000955',
			'1209610248', '1209612798',
			'1212119045', '1212121597',
			'1219998729', '1220001279',
			'1224204808', '1224207351',
			'1231232005', '1231234555',
			'1231234563', '1231237112',
			'1232227846', '1232230391',
			'1234552329', '1234554879',
			'1234560003', '1234562553',
			'1234562561', '1234565110',
			'1234567687', '1234570238',
			'123457280X', '1234575353',
			'1235696642', '1235699196',
			'1237519365', '1237521912',
			'1237521920', '1237524474',
			'1237524482', '1237527031',
			'123752704X', '123752959X',
			'1249999367', '1250001919',
			'1274096642', '1274099196',
			'1281044482', '1281047031',
			'ISBN 978-99937-1-056-1',
			'ISBN 978-9934015960  ',
			'ISBN 978-9930943106',
			' isbn 978-988-00-3827-3 ',
			'ISBN 976-640-140-3',
			'isbN 964-6194-70-2',
		);

		return array_map(function($isbn) {
			return array($isbn);
		}, $isbns);
	}

	/**
	 * @dataProvider getValidIsbns
	 * @param $isbn
	 */
	public function testValidateValidIsbn($isbn)
	{
		$v = new Isbn();
		$this->assertTrue($v->validate($isbn), sprintf('Valid isbn: %s', $isbn));
	}

	/**
	 * @dataProvider getInvalidIsbns
	 * @param $isbn
	 */
	public function testValidateInvalidIsbn($isbn)
	{
		$v = new Isbn();
		$this->assertFalse($v->validate($isbn), sprintf('Invalid isbn: %s', $isbn));
	}
}
