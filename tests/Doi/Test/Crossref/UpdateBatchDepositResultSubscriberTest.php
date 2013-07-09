<?php
/**
 * User: trta
 * Date: 7/4/13
 * Time: 6:17 PM
 */

namespace Doi\Test\Crossref;

use Doi\Crossref\BatchArticleDepositResultEvent;
use Doi\Crossref\BatchDepositResultEvent;
use Doi\Crossref\UpdateBatchDepositResultSubscriber;
use Doi\Entity\Batch;
use Doi\Entity\BatchArticle;
use Doi\Repository\BatchArticleRepository;
use Doi\Test\WebTestCase;

class UpdateBatchDepositResultSubscriberTest extends WebTestCase
{
	protected $subscriber;


	public function testSubscriber()
	{
		/** @var EventDispatcher $dispatcher */
		$dispatcher = $this->app['dispatcher'];

		$batchArticle = new BatchArticle();

		$mockRepository = $this->getMock('Doi\Repository\BatchArticleRepository', array('findByBatchIdAndDoi'), array(), '', false);
		$mockRepository->expects($this->once())
			->method('findByBatchIdAndDoi')
			->will($this->returnValue($batchArticle));

		$mock = $this->getMock('\Doctrine\ORM\EntityManager', array(), array(), '', false);
		$mock->expects($this->any())->method('persist');
		$mock->expects($this->once())
			->method('getRepository')
			->with($this->equalTo('\Doi\Entity\BatchArticle'))
			->will($this->returnValue($mockRepository));


//		var_dump($mock->getRepository('\Doi\Entity\BatchArticle')->findByBatchIdAndDoi('neco', 'neco')); die();

		$subscriber = new UpdateBatchDepositResultSubscriber($mock);
		$dispatcher->addSubscriber($subscriber);

		$batch = new Batch();
		$batch->setBatchId('0001');

		$event = new BatchArticleDepositResultEvent($batch, '12345/issn.00380288.49.1', 'Success', 'Some message');
		$dispatcher->dispatch(BatchArticleDepositResultEvent::EVENT_NAME, $event);


		$event = new BatchDepositResultEvent($batch);
		$dispatcher->dispatch(BatchDepositResultEvent::EVENT_NAME, $event);


		$this->assertInstanceOf('\DateTime', $batch->getResultRetrieved());
		$this->assertEquals('success', strtolower($batchArticle->getResult()));
		$this->assertEquals('Some message', $batchArticle->getResultMessage());
	}
}
