<?php
/**
 * User: trta
 * Date: 7/3/13
 * Time: 2:55 PM
 */

namespace Doi\Crossref;

use Doctrine\ORM\EntityManager;
use Doi\Entity\Batch;
use Doi\Entity\BatchArticle;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogDepositResultSubscriber implements EventSubscriberInterface
{


	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	protected static $levels = array(
		'success' => 'notice',
		'warning' => 'warning',
		'failure' => 'error',
	);

	function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}


	public static function getSubscribedEvents()
	{
		return array(
			BatchArticleDepositResultEvent::EVENT_NAME => array('onBatchArticleDepositResult', 0),
		);
	}

	protected function getLevel($status)
	{
		$status = strtolower($status);
		return isset(self::$levels[$status])
			? self::$levels[$status]
			: 'critical';
	}

	public function onBatchArticleDepositResult(BatchArticleDepositResultEvent $event)
	{
		$batch = $event->getBatch();
		$doi = $event->getDoi();
		$status = $event->getStatus();
		$message = $event->getMessage();

		$message = array(
			'batchId' => $batch->getBatchId(),
			'doi' => $doi,
			'status' => $status,
			'message' => $message,
		);

		call_user_func(array($this->logger, $this->getLevel($status)), $message);
	}

}