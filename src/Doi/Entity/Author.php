<?php

namespace Doi\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Author
 *
 * @HasLifecycleCallbacks
 * @package Doi\Entity
 * @Entity(repositoryClass="Doi\Repository\AuthorRepository")
 * @Table(name="author")
 */
class Author
{

	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $authorId;


	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $name;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $surname;


	/**
	 * @var ArrayCollection
	 * @ManyToMany(targetEntity="BatchArticle", mappedBy="authors")
	 */
	protected $batchArticles;



	public function __construct()
	{
		$this->batchArticles = new ArrayCollection();
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $surname
	 */
	public function setSurname($surname)
	{
		$this->surname = $surname;
	}

	/**
	 * @return string
	 */
	public function getSurname()
	{
		return $this->surname;
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
		$this->batchArticles[] = $article;
	}
}