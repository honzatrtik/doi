<?php

namespace Doi;

use Doi\Validator\Issn;
use Respect\Validation\Exceptions\AbstractNestedException;
use Respect\Validation\Validator as V;

class JournalConfigProvider 
{

	protected $journals = array();

	protected function getValidator()
	{
		return V::arr()
		   ->key('issn', new Issn())
		   ->key('name', V::allOf(V::notEmpty(), V::string()))
		   ->key('language', V::allOf(V::notEmpty(), V::string()));
	}

	public function add($data)
	{
		$this->getValidator()->assert($data);

		$issn = $data['issn'];
		if (isset($this->journals[$issn]))
		{
			throw new \InvalidArgumentException('Issn already taken: ' . $issn);
		}

		$config = new JournalConfig();
		$config->setIssn($issn);
		$config->setName($data['name']);
		$config->setLanguage($data['language']);

		$this->journals[$issn] = $config;
	}


	/**
	 * @return JournalConfig[]
	 */
	public function getAll()
	{
		return $this->journals;
	}

	/**
	 * @param $issn
	 * @return JournalConfig
	 * @throws \OutOfBoundsException
	 */
	public function get($issn)
	{
		if (!isset($this->journals[$issn]))
		{
			throw new \OutOfBoundsException('Config not found for issn: ' . $issn);
		}
		return $this->journals[$issn];
	}

}