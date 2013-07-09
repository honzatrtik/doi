<?php

namespace Doi\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class User
 *
 * @package Doi\Entity
 * @Entity(repositoryClass="Doi\Repository\UserRepository")
 * @Table(name="user")
 */
class User
{
	/**
	 * @var int
	 * @Id @Column(type="integer") @GeneratedValue
	 */
	protected $userId;


	/**
	 * @var string
	 * @Column(type="string", unique=true)
	 */
	protected $username;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $password;


	/**
	 * @OneToMany(targetEntity="Batch", mappedBy="submittedBy")
	 * @var ArrayCollection
	 */
	protected $batches;

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		if (!filter_var($username, FILTER_VALIDATE_EMAIL))
		{
			throw new \InvalidArgumentException('Username must be valid email address.');
		}
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}



}