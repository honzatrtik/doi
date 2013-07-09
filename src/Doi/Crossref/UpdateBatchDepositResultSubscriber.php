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
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateBatchDepositResultSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManager
	 */
	protected $em;

	function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	public static function getSubscribedEvents()
	{
		return array(
			BatchDepositResultEvent::EVENT_NAME => array('onBatchDepositResult', 0),
			BatchArticleDepositResultEvent::EVENT_NAME => array('onBatchArticleDepositResult', 0),
		);
	}


	public function onBatchDepositResult(BatchDepositResultEvent $event)
	{
		$batch = $event->getBatch();
		$batch->setResultRetrieved(new \DateTime('now'));

		$this->em->persist($batch);
	}


	public function onBatchArticleDepositResult(BatchArticleDepositResultEvent $event)
	{
		$batch = $event->getBatch();
		$doi = $event->getDoi();

		/** @var BatchArticle $batchArticle */
		$batchArticle = $this->em->getRepository('\Doi\Entity\BatchArticle')
			->findByBatchIdAndDoi($batch->getBatchId(), $doi);

		if ($batchArticle)
		{
			$batchArticle->setResult($event->getStatus());
			$batchArticle->setResultMessage($event->getMessage());
			$this->em->persist($batchArticle);
		}
	}

}