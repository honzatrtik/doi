<?php

namespace Doi\Validator;


use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validatable;

class Isbn extends AbstractRule
{

	/**
	 * @link http://cs.wikipedia.org/wiki/International_Standard_Book_Number
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

		// Na zacatku muzeme mit ISBN
		$input = preg_replace('/^\s*isbn\s*/i', '', $input);

		// Dame pryc pomlcky a mezery vevnitr
		$input = preg_replace('/\s*-\s*/', '', $input);

		$chars = str_split($input);

		if (strlen($input) == 10)
		{
			// Zkontrolujeme platne znaky 1-9
			foreach (array_slice($chars, 0, 9) as $char)
			{
				if (!ctype_digit($char))
				{
					return FALSE;
				}
			}
			// Posledni znak je [0-9X]
			if (!ctype_digit($chars[9]) && strtoupper($chars[9]) !== 'X')
			{
				return FALSE;
			}

			$checksum = 0;
			$multiplicator = 10;
			foreach($chars as $char)
			{
				$checksum += (ctype_digit($char) ? $char : 10) * $multiplicator--;
			}

			// $checksum mod 11 = 0
			return !($checksum % 11);
		}
		elseif(strlen($input) == 13)
		{
			$checksum = 0;
			foreach($chars as $i => $char)
			{
				$multiplicator = ($i % 2) ? 3 : 1;
				$checksum += $char * $multiplicator;
			}

			// $checksum mod 10 = 0
			return !($checksum % 10);
		}
		else
		{
			return false;
		}


	}

}