<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/30/13
 * Time: 9:42 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Test\Security;


use Doi\Entity\User;
use Doi\Repository\UserRepository;
use Doi\Security\Authenticator;
use Doi\Security\AuthenticatorException;

class AuthenticatorTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @param User $return
	 * @return UserRepository
	 */
	protected function getUserRepositoryMock(User $return = null)
	{
		$m = $this->getMock('\Doi\Repository\UserRepository', array('findOneBy'), array(), '', FALSE);
		$m->expects($this->once())
			->method('findOneBy')
			->will($this->returnValue($return));
		return $m;

	}

	/**
	 * @expectedException \Doi\Security\AuthenticatorException
	 * @expectedExceptionCode 1
	 */
	public function testAuthenticate1()
	{
		$user = new User();
		$user->setUsername('honzik@honzik.cz');
		$user->setPassword(crypt('heslicko', Authenticator::DEFAULT_SALT));

		$authenticator = new Authenticator($this->getUserRepositoryMock($user));
		$authenticator->authenticate('honzik@honzik.cz', 'spatneheslicko');
	}

	/**
	 * @expectedException \Doi\Security\AuthenticatorException
	 * @expectedExceptionCode 0
	 */
	public function testAuthenticate2()
	{
		$user = new User();
		$user->setUsername('honzik@honzik.cz');
		$user->setPassword(crypt('heslicko', Authenticator::DEFAULT_SALT));

		$authenticator = new Authenticator($this->getUserRepositoryMock(null));
		$authenticator->authenticate('honzik@honzik.cz', 'spatneheslicko');
	}

	public function testAuthenticate3()
	{
		$user = new User();
		$user->setUsername('honzik@honzik.cz');
		$user->setPassword(crypt('heslicko', Authenticator::DEFAULT_SALT));

		$authenticator = new Authenticator($this->getUserRepositoryMock($user));
		$authentictedUser = $authenticator->authenticate('honzik@honzik.cz', 'heslicko');

		$this->assertInstanceOf('\Doi\Entity\User', $authentictedUser);
		$this->assertEquals('honzik@honzik.cz', $authentictedUser->getUsername());
	}
}
