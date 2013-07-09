<?php

namespace Doi\Validator;


use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validatable;

class Issn extends AbstractRule
{

	/**
	 * @link http://cs.wikipedia.org/wiki/International_Standard_Serial_Number
	 *
	 * @param $input
	 * @return bool
	 */
	public function validate($input)
	{

		if (!is_string($input))
		{
			return FALSE;
		}

		// trim space na zacatku / konci
		$input = trim($input);

		// Na zacatku muzeme mit ISSN
		$input = preg_replace('/^\s*issn\s*/i', '', $input);

		// Dame pryc pomlcky a mezery vevnitr
		$input = preg_replace('/\s*-\s*/', '', $input);

		$chars = str_split($input);

		if (strlen($input) == 8)
		{
			// Zkontrolujeme platne znaky 1-7
			foreach (array_slice($chars, 0, 7) as $char)
			{
				if (!ctype_digit($char))
				{
					return FALSE;
				}
			}
			// Posledni znak je [0-9X]
			if (!ctype_digit($chars[7]) && strtoupper($chars[7]) !== 'X')
			{
				return FALSE;
			}

			$checksum = 0;
			$multiplicator = 8;
			foreach($chars as $char)
			{
				$checksum += (ctype_digit($char) ? $char : 10) * $multiplicator--;
			}

			// $checksum mod 11 = 0
			return !($checksum % 11);
		}
		else
		{
			return false;
		}


	}

}