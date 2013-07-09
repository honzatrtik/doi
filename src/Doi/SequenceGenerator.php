<?php

namespace Doi;

use Doctrine\ORM\EntityManager;
use Doi\Entity\Sequence;

class SequenceGenerator implements SequenceGeneratorInterface
{

	/**
	 * @var EntityManager
	 */
	protected $em;

	function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @return int|string
	 */
	public function generate()
	{
		$sequence = new Sequence();
		$this->em->persist($sequence);
		$this->em->flush($sequence);
		return $sequence->getNumber();
	}
}