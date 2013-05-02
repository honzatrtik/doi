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
 * @Entity @Table(name="doiRequest")
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
	 * @var int
	 * @Column(type="integer")
	 */
	protected $doiId;

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
	 * @Column(type="integer")
	 */
	protected $issue;

	/**
	 * @var int
	 * @Column(type="integer")
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



}