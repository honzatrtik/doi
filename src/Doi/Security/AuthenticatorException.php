<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/30/13
 * Time: 9:29 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Security;


class AuthenticatorException extends \RuntimeException
{

	const USER_NOT_FOUND = 0;
	const INVALID_PASSWORD = 1;

	public function __construct($code = 0, Exception $previous = NULL)
	{
		parent::__construct('', $code, $previous);
	}

}