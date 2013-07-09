<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/30/13
 * Time: 9:29 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Security;


use Doi\Repository\UserRepository;

class Authenticator implements AuthenticatorInterface
{
	const DEFAULT_SALT = 's@lts4ltsAlt';

	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	protected $salt;

	public function __construct(UserRepository $userRepository, $salt = self::DEFAULT_SALT)
	{
		$this->userRepository = $userRepository;
		$this->salt = $salt;
	}

	public function generatePassword($password)
	{
		return crypt($password, $this->salt);
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @return \Doi\Entity\User
	 * @throws AuthenticatorException
	 */
	public function authenticate($username, $password)
	{
		$user = $this->userRepository->findOneBy(array('username' => $username));
		if (!$user)
		{
			throw new AuthenticatorException(AuthenticatorException::USER_NOT_FOUND);
		}

		if (crypt($password, $user->getPassword()) !== $user->getPassword())
		{
			throw new AuthenticatorException(AuthenticatorException::INVALID_PASSWORD);
		}

		return $user;
	}
}