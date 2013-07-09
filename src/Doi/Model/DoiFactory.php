<?php

namespace Doi\Model;


use Doi\DoiNumberGenerator;
use Doi\Entity\Doi;
use Doi\Entity\DoiRequest;

class DoiFactory
{
	/**
	 * @var DoiNumberGenerator
	 */
	protected $doiNumberGenerator;

	function __construct(DoiNumberGenerator $doiNumberGenerator)
	{
		$this->doiNumberGenerator = $doiNumberGenerator;
	}

	/**
	 * @param DoiRequest $doiRequest
	 * @return Doi
	 */
	public function createFromRequest(DoiRequest $doiRequest)
	{
		$doi = new Doi();
		$doi->setDoiRequest($doiRequest);
		$doi->setDoi($this->doiNumberGenerator->generate($doiRequest));
		return $doi;
	}
}