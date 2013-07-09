<?php

namespace Doi\Security;


use Doi\Entity\User;
use Doi\Repository\UserRepository;

/**
 * Class AuthenticatorInterface
 *
 * @package Doi\Security
 */
interface AuthenticatorInterface
{
	/**
	 * @param $username string
	 * @param $password string
	 * @return User
	 */
	public function authenticate($username, $password);

}