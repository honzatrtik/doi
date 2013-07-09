<?php

namespace Doi;

use Doi\Entity\DoiRequest;

class DoiNumberGenerator
{
	protected $prefix;

	/**
	 * @var SequenceGeneratorInterface
	 */
	protected $sequenceGenerator;

	function __construct(SequenceGeneratorInterface $sequenceGenerator, $prefix)
	{
		$this->sequenceGenerator = $sequenceGenerator;
		$this->prefix = $prefix;
	}

	public function generate(DoiRequest $doiRequest)
	{
		if ($doiRequest->getType() == $doiRequest::TYPE_JOURNAL)
		{
			return sprintf(
				'%s/issn.%s.%d.%d.%s',
				$this->prefix,
				$doiRequest->getIsnAsNumber(),
				$doiRequest->getVolume(),
				$doiRequest->getIssue(),
				$this->sequenceGenerator->generate()
			);
		}
		else
		{
			return sprintf(
				'%s/isbn.%s',
				$this->prefix,
				$doiRequest->getIsnAsNumber()
			);
		}
	}
}