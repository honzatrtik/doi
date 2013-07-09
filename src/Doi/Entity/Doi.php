<?php

namespace Doi\Entity;

/**
 * Class Doi
 *
 * @HasLifecycleCallbacks
 * @package Doi\Entity
 * @Entity(repositoryClass="Doi\Repository\DoiRepository")
 * @Table(name="doi", indexes={@Index(name="doi_status_idx", columns={"status"}), @Index(name="doi_doi_idx", columns={"doi"}), @Index(name="doi_reserved_idx", columns={"reserved"})})
 */
class Doi
{

	const STATUS_RESERVED = 0;
	const STATUS_DEPOSITED = 1;

	private static $statusToText = array(
		self::STATUS_RESERVED => 'reserved',
		self::STATUS_DEPOSITED => 'deposited',
	);

	/**
	 * @var int
	 * @Id @Column(type="integer") @GeneratedValue
	 */
	protected $doiId;

	/**
	 * @var DoiRequest
	 * @OneToOne(targetEntity="DoiRequest", inversedBy="doi", cascade="persist")
	 * @joinColumn(name="doiRequestId", referencedColumnName="doiRequestId", nullable=false, unique=true)
	 */
	protected $doiRequest;

	/**
	 * @var User
	 * @OneToOne(targetEntity="User")
	 * @joinColumn(name="reservedByUserId", referencedColumnName="userId", nullable=true)
	 */
	protected $user;

	/**
	 * @var string
	 * @Column(type="string", unique=true)
	 */
	protected $doi;

	/**
	 * @var int
	 * @Column(type="integer")
	 */
	protected $status = self::STATUS_RESERVED;

	/**
	 * @var \DateTime
	 * @Column(type="datetime")
	 */
	protected $reserved;

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
	 * @param string $doi
	 */
	public function setDoi($doi)
	{
		$this->doi = $doi;
	}

	/**
	 * @return string
	 */
	public function getDoi()
	{
		return $this->doi;
	}

	/**
	 * @param \DateTime $reserved
	 */
	public function setReserved(\DateTime $reserved)
	{
		$this->reserved = $reserved;
	}

	/**
	 * @return \DateTime
	 */
	public function getReserved()
	{
		return $this->reserved;
	}

	/**
	 * @param int $status
	 */
	public function setStatus($status)
	{
		if (!isset(self::$statusToText[$status]))
		{
			throw new \InvalidArgumentException('Invalid status: ' . $status);
		}
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function getStatusAsText()
	{
		return self::$statusToText[$this->getStatus()];
	}

	/**
	 * @param \Doi\Entity\User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return \Doi\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}



	/**
	 * @param DoiRequest $doiRequest
	 */
	public function setDoiRequest(DoiRequest $doiRequest)
	{
		$this->doiRequest = $doiRequest;
	}

	/**
	 * @return \Doi\Entity\DoiRequest
	 */
	public function getDoiRequest()
	{
		return $this->doiRequest;
	}

	/**
	 * @PrePersist
	 */
	public function onPrePersistSetReserved()
	{
		if ($this->reserved === NULL)
		{
			$this->reserved = new \DateTime('now');
		}
	}



}