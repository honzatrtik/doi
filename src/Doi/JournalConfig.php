<?php

namespace Doi;

class JournalConfig
{

	protected $issn;
	protected $name;
	protected $language;

	/**
	 * @param mixed $issn
	 */
	public function setIssn($issn)
	{
		$this->issn = $issn;
	}

	/**
	 * @return mixed
	 */
	public function getIssn()
	{
		return $this->issn;
	}

	/**
	 * @param mixed $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @return mixed
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

}