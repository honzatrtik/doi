<?php

namespace Doi\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Batch
 *
 * @HasLifecycleCallbacks
 * @package Doi\Entity
 * @Entity(repositoryClass="Doi\Repository\BatchRepository")
 * @Table(name="batch", indexes={@Index(name="batch_resultRetrieved_idx", columns={"resultRetrieved"}), @Index(name="batch_type_idx", columns={"type"})})
 */
class Batch
{

	const TYPE_JOURNAL = 'journal';
	const TYPE_BOOK = 'book';

	protected static $types = array(
		self::TYPE_JOURNAL,
		self::TYPE_BOOK
	);


	/**
	 * @var int
	 * @Id @Column(type="integer") @GeneratedValue
	 */
	protected $batchId;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $type;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $isn;

	/**
	 * @var int
	 * @Column(type="integer", nullable=true)
	 */
	protected $issue;

	/**
	 * @var int
	 * @Column(type="integer", nullable=true)
	 */
	protected $volume;

	/**
	 * @var integer
	 * @Column(type="decimal", precision=4, scale=0)
	 */
	protected $year;

	/**
	 * @var ArrayCollection
	 * @OneToMany(targetEntity="BatchArticle", mappedBy="batch", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
	 */
	protected $batchArticles;

	/**
	 *
	 * @ManyToOne(targetEntity="User", inversedBy="batches")
	 * @JoinColumn(name="subbmitedByUserId", referencedColumnName="userId", nullable=false)
	 * @var User
	 */
	protected $submittedBy;


	/**
	 * @var \DateTime
	 * @Column(type="datetime")
	 */
	protected $created;

	/**
	 * @var \DateTime
	 * @Column(type="datetime", nullable=true)
	 */
	protected $deposited;

	/**
	 * @var \DateTime
	 * @Column(type="datetime", nullable=true)
	 */
	protected $resultRetrieved;


	public function __construct()
	{
		$this->batchArticles = new ArrayCollection();
	}

	/**
	 * @param int $batchId
	 */
	public function setBatchId($batchId)
	{
		$this->batchId = $batchId;
	}

	/**
	 * @return int
	 */
	public function getBatchId()
	{
		return $this->batchId;
	}

	/**
	 * @return string
	 */
	public function getBatchIdPadded()
	{
		return sprintf('%04d', $this->batchId);
	}

	/**
	 * @param string $isn
	 */
	public function setIsn($isn)
	{
		$this->isn = $isn;
	}

	/**
	 * @return string
	 */
	public function getIsn()
	{
		return $this->isn;
	}

	/**
	 * @return string
	 */
	public function getIsnAsNumber()
	{
		$isn = $this->isn;
		$isn = trim($isn);
		$isn = preg_replace('/^\s*(issn|isbn)\s*/i', '', $isn);
		$isn = preg_replace('/\s*-\s*/', '', $isn);
		return $isn;
	}

	/**
	 * @param int $issue
	 */
	public function setIssue($issue)
	{
		$this->issue = $issue;
	}

	/**
	 * @return int
	 */
	public function getIssue()
	{
		return $this->issue;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		if (!in_array($type, self::$types))
		{
			throw new \InvalidArgumentException('Invalid type.');
		}
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $volume
	 */
	public function setVolume($volume)
	{
		$this->volume = $volume;
	}

	/**
	 * @return int
	 */
	public function getVolume()
	{
		return $this->volume;
	}

	/**
	 * @param int $year
	 */
	public function setYear($year)
	{
		$this->year = $year;
	}

	/**
	 * @return int
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBatchArticles()
	{
		return $this->batchArticles;
	}


	/**
	 * @param BatchArticle $article
	 */
	public function addBatchArticle(BatchArticle $article)
	{
		$article->setBatch($this);
		$this->batchArticles[] = $article;
	}



	/**
	 * @param \DateTime $created
	 */
	public function setCreated(\DateTime $created)
	{
		$this->created = $created;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}



	/**
	 * @param \DateTime $deposited
	 */
	public function setDeposited(\DateTime $deposited)
	{
		$this->deposited = $deposited;
	}

	/**
	 * @return \DateTime
	 */
	public function getDeposited()
	{
		return $this->deposited;
	}

	/**
	 * @param \DateTime $resultRetrieved
	 */
	public function setResultRetrieved(\DateTime $resultRetrieved)
	{
		$this->resultRetrieved = $resultRetrieved;
	}

	/**
	 * @return \DateTime
	 */
	public function getResultRetrieved()
	{
		return $this->resultRetrieved;
	}

	/**
	 * @param \Doi\Entity\User $submittedBy
	 */
	public function setSubmittedBy(User $submittedBy)
	{
		$this->submittedBy = $submittedBy;
	}

	/**
	 * @return \Doi\Entity\User
	 */
	public function getSubmittedBy()
	{
		return $this->submittedBy;
	}



	/**
	 * @PrePersist
	 */
	public function onPrePersistSetReserved()
	{
		if ($this->created === NULL)
		{
			$this->created = new \DateTime('now');
		}
	}
}