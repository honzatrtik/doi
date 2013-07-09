<?php

namespace Doi\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class BatchArticle
 *
 * @HasLifecycleCallbacks
 * @package Doi\Entity
 * @Entity(repositoryClass="Doi\Repository\BatchArticleRepository")
 * @Table(name="batchArticle", indexes={@Index(name="batchArticle_result_idx", columns={"result"})})
 */
class BatchArticle
{

	const RESULT_SUCCESS = 'success';
	const RESULT_ERROR = 'error';
	const RESULT_WARNING = 'warning';

	protected static $results = array(
		self::RESULT_SUCCESS,
		self::RESULT_WARNING,
		self::RESULT_ERROR,
	);

	/**
	 * @var int
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $batchArticleId;


	/**
	 * @var string
	 * @Column(type="string", nullable=true)
	 */
	protected $result;

	/**
	 * @var string
	 * @Column(type="string", nullable=true)
	 */
	protected $resultMessage;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $url;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $title;

	/**
	 * @var int
	 * @Column(type="integer")
	 */
	protected $page;

	/**
	 *
	 * @ManyToOne(targetEntity="Doi")
	 * @JoinColumn(name="doiId", referencedColumnName="doiId", nullable=false)
	 */
	protected $doi;

	/**
	 * @var Batch
	 * @ManyToOne(targetEntity="Batch")
	 * @JoinColumn(name="batchId", referencedColumnName="batchId", nullable=false)
	 */
	protected $batch;


	/**
	 * @var ArrayCollection
	 * @ManyToMany(targetEntity="Author", inversedBy="batchArticles")
	 * @JoinTable(name="batchArticle2Author",
	 *      joinColumns={@JoinColumn(name="batchArticleId", referencedColumnName="batchArticleId")},
	 *      inverseJoinColumns={@JoinColumn(name="authorId", referencedColumnName="authorId")})
	 */
	protected $authors;


	public function __construct()
	{
		$this->authors = new ArrayCollection();
	}

	/**
	 * @param int $batchArticleId
	 */
	public function setBatchArticleId($batchArticleId)
	{
		$this->batchArticleId = $batchArticleId;
	}

	/**
	 * @return int
	 */
	public function getBatchArticleId()
	{
		return $this->batchArticleId;
	}

	/**
	 * @param Doi $doi
	 */
	public function setDoi(Doi $doi)
	{
		$this->doi = $doi;
	}

	/**
	 * @return Doi
	 */
	public function getDoi()
	{
		return $this->doi;
	}

	/**
	 * @param string $result
	 */
	public function setResult($result)
	{
		$result = strtolower($result);
		if (!in_array($result, self::$results))
		{
			throw new \InvalidArgumentException('Invalid result.');
		}
		$this->result = $result;
	}

	/**
	 * @return string
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * @param string $resultMessage
	 */
	public function setResultMessage($resultMessage)
	{
		$this->resultMessage = $resultMessage;
	}

	/**
	 * @return string
	 */
	public function getResultMessage()
	{
		return $this->resultMessage;
	}





	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param int $page
	 */
	public function setPage($page)
	{
		$this->page = $page;
	}

	/**
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}


	/**
	 * @param \Doi\Entity\Batch $batch
	 */
	public function setBatch(Batch $batch)
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


	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAuthors()
	{
		return $this->authors;
	}


	/**
	 * @param Author $author
	 */
	public function addAuthor(Author $author)
	{
		$this->authors[] = $author;
		$author->addBatchArticle($this);
	}
}