<?php

namespace Doi\Validator;


use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validatable;

class UrlException extends ValidationException
{
	public static $defaultTemplates = array(
		self::MODE_DEFAULT => array(
			self::STANDARD => '{{name}} must be valid url',
		),
		self::MODE_NEGATIVE => array(
			self::STANDARD => '{{name}} must not be valid url',
		)
	);
}