<?php

namespace Doi\Entity;

/**
 * Class Sequence
 *
 * @package Doi\Entity
 * @Entity
 * @Table(name="sequence")
 */
class Sequence
{


	/**
	 * @var int
	 * @Id @Column(type="integer") @GeneratedValue
	 */
	protected $number;

	/**
	 * @param int $number
	 */
	public function setNumber($number)
	{
		$this->number = $number;
	}

	/**
	 * @return int
	 */
	public function getNumber()
	{
		return $this->number;
	}



}