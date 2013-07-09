<?php

namespace Doi;

interface SequenceGeneratorInterface
{
	/**
	 * @return int|string
	 */
	public function generate();
}