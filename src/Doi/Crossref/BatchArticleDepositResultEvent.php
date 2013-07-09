<?php
/**
 * User: trta
 * Date: 7/3/13
 * Time: 2:55 PM
 */

namespace Doi\Crossref;

use Doi\Entity\Batch;
use Symfony\Component\EventDispatcher\Event;

class BatchArticleDepositResultEvent extends Event
{
	const EVENT_NAME = 'crossref.batchArticleDepositResult';

	/**
	 * @var Batch
	 */
	protected $batch;

	/**
	 * @var string
	 */
	protected $doi;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $message;

	function __construct(Batch $batch, $doi, $status, $message)
	{
		$this->batch = $batch;
		$this->doi = $doi;
		$this->message = $message;
		$this->status = $status;
	}


	/**
	 * @return \Doi\Entity\Batch
	 */
	public function getBatch()
	{
		return $this->batch;
	}

	/**
	 * @return string
	 */
	public function getDoi()
	{
		return $this->doi;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}
}