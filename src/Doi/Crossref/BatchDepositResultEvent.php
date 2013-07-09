<?php
/**
 * User: trta
 * Date: 7/3/13
 * Time: 2:55 PM
 */

namespace Doi\Crossref;

use Doi\Entity\Batch;
use Symfony\Component\EventDispatcher\Event;

class BatchDepositResultEvent extends Event
{
	const EVENT_NAME = 'crossref.batchDepositResult';

	/**
	 * @var Batch
	 */
	protected $batch;

	function __construct(Batch $batch)
	{
		$this->batch = $batch;
	}

	/**
	 * @return \Doi\Entity\Batch
	 */
	public function getBatch()
	{
		return $this->batch;
	}

}