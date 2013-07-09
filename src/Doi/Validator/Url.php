<?php

namespace Doi\Validator;


use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validatable;

class Url extends AbstractRule
{

	/**
	 * @param $input
	 * @return bool
	 */
	public function validate($input)
	{
		return filter_var($input, FILTER_VALIDATE_URL);
	}

}