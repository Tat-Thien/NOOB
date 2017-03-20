<?php

namespace NoobBundle\Controller;

use EntityBundle\Entity\OutgoerPreparation;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use NoobBundle\Form\OutgoerPreparationType;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("OutgoerPreparation")
 */
class OutgoerPreparationController extends NoobBundleController
{

	/**
	 * @REST\Get("/outgoerPreparations")
	 * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
	 * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get all outgoer preparations",
	 *  output={"class"="NoobBundle\Form\OutgoerPreparationType", "collection"=true}
	 * )
	 */
	public function cgetAction(ParamFetcherInterface $paramFetcher)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();
		$qb->select('o')->from('EntityBundle\Entity\OutgoerPreparation', 'o');
		$salesforceID = $paramFetcher->get('salesforceID');
		if ($salesforceID) {
			$qb->andWhere('o.salesforceID LIKE :sfID')->setParameter('sfID', $salesforceID . '%');
		}
		$query = $qb->getQuery();
		$pagination = $this->createPaginationObject($paramFetcher, $query);
		return $pagination;
	}

	/**
	 * @REST\Get("/outgoerPreparations/{outgoerPreparationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get outgoer preparation",
	 *  output="NoobBundle\Form\OutgoerPreparationType"
	 * )
	 */
	public function getAction(ParamFetcherInterface $paramFetcher, $outgoerPreparationID)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('EntityBundle\Entity\OutgoerPreparation')->findOneById($outgoerPreparationID);
		if ($entity) {
			return $entity;
		} else {
			throw new HttpException(404);
		}
	}

	/**
	 * @REST\Post("/outgoerPreparations")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Create outgoer preparation",
	 *  input="NoobBundle\Form\OutgoerPreparationType",
	 *  output="NoobBundle\Form\OutgoerPreparationType"
	 * )
	 */
	public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
	{
		$this->checkAuthentication($paramFetcher, true);
		$ops = new OutgoerPreparation();
		$form = $this->createForm(OutgoerPreparationType::class, $ops);
		$form->submit($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($ops);
			$em->flush();
			return $this->returnCreationResponse($ops);
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Patch("/outgoerPreparations/{outgoerPreparationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Edit an outgoer preparations",
	 *  input="NoobBundle\Form\OutgoerPreparationType",
	 *  output="NoobBundle\Form\OutgoerPreparationType"
	 * )
	 */
	public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $outgoerPreparationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$ops = $em->getRepository('EntityBundle\Entity\OutgoerPreparation')->findOneById($outgoerPreparationID);
		if (!$ops) {
			throw new NotFoundHttpException();
		}
		$form = $this->createForm(OutgoerPreparationType::class, $ops, [
			'method' => 'PATCH'
		]);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em->merge($ops);
			$em->flush();
			return $this->returnModificationResponse();
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Delete("/outgoerPreparations/{outgoerPreparationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Delete an outgoer preparations"
	 * )
	 */
	public function deleteAction(ParamFetcherInterface $paramFetcher, $outgoerPreparationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$ops = $em->getRepository('EntityBundle\Entity\OutgoerPreparation')->findOneById($outgoerPreparationID);
		if (!$ops) {
			throw new NotFoundHttpException();
		}
		$em->remove($ops);
		$em->flush();
		return $this->returnDeletionResponse();
	}
}
