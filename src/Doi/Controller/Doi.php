<?php

namespace Doi\Controller;


use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doi\Entity;
use Doi\Model\DoiFactory;
use Doi\Validator\Isbn;
use Doi\Validator\Issn;
use Silex\Application;
use Respect\Validation\Validator as V;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Doi
{

	const DOI_PATTERN_REGEX = '^([^/]+)/(issn|isbn)(\.[\w]+)+$';

	/**
	 * @var DoiFactory
	 */
	protected $doiFactory;

	/**
	 * @var EntityManager
	 */
	protected $em;

	function __construct(EntityManager $em, DoiFactory $doiFactory)
	{
		$this->doiFactory = $doiFactory;
		$this->em = $em;
	}

	/**
	 * @param $doi
	 * @return Entity\Doi
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	public function findDoiByDoi($doi)
	{
		if (!$doi = $this->em->getRepository('\Doi\Entity\Doi')->findByDoi($doi))
		{
			throw new HttpException(404, 'Doi not found: ' . $doi);
		}
		return $doi;
	}


	public function getDoi(Application $app, Request $request, Entity\Doi $doi)
	{


		return $app->json(array(
			'doi' => $doi->getDoi(),
			'url' => $app['url_generator']->generate('doi_get_doi', array(
				'doi' => $doi->getDoi(),
			)),
			'status'=> $doi->getStatusAsText(),
			'type' => $doi->getDoiRequest()->getType(),
			'isn' => $doi->getDoiRequest()->getIsnAsNumber(),
			'issue' => $doi->getDoiRequest()->getIssue(),
			'volume' => $doi->getDoiRequest()->getVolume(),
			'reserved' => $doi->getReserved()->format('Y-m-d H:i:s'),
			'reservedBy' => $doi->getUser()
				? $doi->getUser()->getUsername()
				: NULL,
			'batchesUrl' =>  $app['url_generator']->generate('batch_get_batches_by_doi', array(
				'doi' => $doi->getDoi()
			))
		));
	}

	public function post(Application $app, Request $request)
	{
		$post = $request->request->all();
		$validator = $this->getPostDoiValidator();
		if (!$validator->validate($post))
		{
			throw new HttpException(400, 'Invalid parameters.');
		}

		$doiRequest = new Entity\DoiRequest();
		$doiRequest->setType($request->request->get('type'));
		$doiRequest->setIsn($request->request->get('isn'));
		$doiRequest->setIssue($request->request->get('issue'));
		$doiRequest->setVolume($request->request->get('volume'));

		$doi = $this->doiFactory->createFromRequest($doiRequest);

		try
		{
			$this->em->persist($doi);
			$this->em->flush();
		}
		catch(DBALException $e)
		{
			$previous = $e->getPrevious();
			if ($previous && $previous->getCode() == 23000)
			{
				// Vygenerovalo se nam stejne doi cislo
				throw new HttpException(400, 'Duplicate doi number.');
			}
			else
			{
				throw $e;
			}
		}


		return $app->json(array(
			'doi' => $doi->getDoi(),
			'url' => $app['url_generator']->generate('doi_get_doi', array(
				'doi' => $doi->getDoi(),
			))
		), 201);
	}


	public function deleteDoi(Application $app, Request $request, Entity\Doi $doi)
	{
		if ($doi->getStatus() == Entity\Doi::STATUS_RESERVED)
		{
			$this->em->remove($doi);
			$this->em->flush();
			return new Response('', 204);
		}
		else
		{
			throw new HttpException(400, 'Doi has been already deposited, can not delete.');
		}
	}


	public function getPostDoiValidator()
	{
		return V::oneOf(
			V::arr()
				->key('type', V::equals('book'))
				->key('isn', new Isbn()),
			V::arr()
				->key('type', V::equals('journal'))
				->key('isn', new Issn())
				->key('issue', V::regex('/^[0-9]+$/'))
				->key('volume', V::regex('/^[0-9]+$/'))
		);
	}

}
