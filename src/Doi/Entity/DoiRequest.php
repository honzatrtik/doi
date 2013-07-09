<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trta
 * Date: 5/3/13
 * Time: 12:35 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Doi\Entity;

/**
 * Class DoiRequest
 *
 * @package Doi\Entity
 * @Entity @Table(name="doiRequest", indexes={@Index(name="doiRequest_type_idx", columns={"type"})})
 */
class DoiRequest
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
	protected $doiRequestId;

	/**
	 * @var Doi
	 * @oneToOne(targetEntity="Doi", mappedBy="doiRequest", cascade="persist")
	 */
	protected $doi;

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
	 * @param int $doiId
	 */
	public function setDoiId($doiId)
	{
		$this->doiId = $doiId;
	}

	/**
	 * @return int
	 */
	public function getDoiId()
	{
		return $this->doiId;
	}

	/**
	 * @param int $doiRequestId
	 */
	public function setDoiRequestId($doiRequestId)
	{
		$this->doiRequestId = $doiRequestId;
	}

	/**
	 * @return int
	 */
	public function getDoiRequestId()
	{
		return $this->doiRequestId;
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
	 * @param \Doi\Entity\Doi $doi
	 */
	public function setDoi(Doi $doi)
	{
		$this->doi = $doi;
		$this->doi->setDoiRequest($this);
	}

	/**
	 * @return \Doi\Entity\Doi
	 */
	public function getDoi()
	{
		return $this->doi;
	}






}