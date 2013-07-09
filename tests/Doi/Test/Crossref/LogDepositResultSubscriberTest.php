<?php
/**
 * User: trta
 * Date: 7/4/13
 * Time: 5:49 PM
 */

namespace Doi\Test\Crossref;


use Doi\Crossref\BatchArticleDepositResultEvent;
use Doi\Crossref\LogDepositResultSubscriber;
use Doi\Entity\Batch;
use Doi\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LogDepositResultSubscriberTest extends WebTestCase
{

	protected $subscriber;

	public function testSuccess()
	{

		/** @var EventDispatcher $dispatcher */
		$dispatcher = $this->app['dispatcher'];
		$mock = $this->getMock('\Psr\Log\LoggerInterface');
		$mock->expects($this->once())->method('notice');


		$subscriber = new LogDepositResultSubscriber($mock);
		$dispatcher->addSubscriber($subscriber);


		$batch = new Batch();
		$batch->setBatchId('0001');
		$event = new BatchArticleDepositResultEvent($batch, '12345/issn.00380288.49.1', 'Success', 'Some message');
		$dispatcher->dispatch(BatchArticleDepositResultEvent::EVENT_NAME, $event);
	}

	public function testWarning()
	{

		/** @var EventDispatcher $dispatcher */
		$dispatcher = $this->app['dispatcher'];
		$mock = $this->getMock('\Psr\Log\LoggerInterface');
		$mock->expects($this->once())->method('warning');


		$subscriber = new LogDepositResultSubscriber($mock);
		$dispatcher->addSubscriber($subscriber);


		$batch = new Batch();
		$batch->setBatchId('0001');
		$event = new BatchArticleDepositResultEvent($batch, '12345/issn.00380288.49.1', 'Warning', 'Some message');
		$dispatcher->dispatch(BatchArticleDepositResultEvent::EVENT_NAME, $event);
	}

	public function testError()
	{

		/** @var EventDispatcher $dispatcher */
		$dispatcher = $this->app['dispatcher'];
		$mock = $this->getMock('\Psr\Log\LoggerInterface');
		$mock->expects($this->once())->method('error');


		$subscriber = new LogDepositResultSubscriber($mock);
		$dispatcher->addSubscriber($subscriber);


		$batch = new Batch();
		$batch->setBatchId('0001');
		$event = new BatchArticleDepositResultEvent($batch, '12345/issn.00380288.49.1', 'Failure', 'Some message');
		$dispatcher->dispatch(BatchArticleDepositResultEvent::EVENT_NAME, $event);
	}

}
