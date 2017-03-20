<?php

namespace NoobBundle\Controller;

use EntityBundle\Entity\OnlineOutgoerPreparationParticipation;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use NoobBundle\Form\OnlineOutgoerPreparationParticipationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @REST\RouteResource("OnlineOutgoerPreparationParticipation")
 */
class OnlineOutgoerPreparationParticipationController extends NoobBundleController
{

	/**
	 * @REST\Get("/onlineOutgoerPreparationParticipations")
	 * @REST\QueryParam(name="person", description="Person ID")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
	 * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get all online outgoer preparation participations",
	 *  output={"class"="NoobBundle\Form\OnlineOutgoerPreparationParticipationType", "collection"=true}
	 * )
	 */
	public function cgetAction(ParamFetcherInterface $paramFetcher)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();
		$personID = $paramFetcher->get('person');
		$qb->select('o')->from('EntityBundle\Entity\OnlineOutgoerPreparationParticipation', 'o');
		if ($personID) {
			$qb->andWhere('o.person = :personID')->setParameter('personID', $personID);
		}
		$query = $qb->getQuery();
		$pagination = $this->createPaginationObject($paramFetcher, $query);
		return $pagination;
	}

	/**
	 * @REST\Get("/onlineOutgoerPreparationParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get an online outgoer preparation participation",
	 *  output="NoobBundle\Form\OnlineOutgoerPreparationParticipationType"
	 * )
	 */
	public function getAction(ParamFetcherInterface $paramFetcher, $participationID)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('EntityBundle\Entity\OnlineOutgoerPreparationParticipation')->findOneById($participationID);
		if ($entity) {
			return $entity;
		} else {
			throw new HttpException(404);
		}
	}

	/**
	 * @REST\Post("/onlineOutgoerPreparationParticipations")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Create an online outgoer preparation participation",
	 *  input="NoobBundle\Form\OnlineOutgoerPreparationParticipationType",
	 *  output="NoobBundle\Form\OnlineOutgoerPreparationParticipationType"
	 * )
	 */
	public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
	{
		$this->checkAuthentication($paramFetcher, true);
		$onlineOutgoerPreparationParticipation = new OnlineOutgoerPreparationParticipation();
		$form = $this->createForm(OnlineOutgoerPreparationParticipationType::class, $onlineOutgoerPreparationParticipation);
		$form->submit($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($onlineOutgoerPreparationParticipation);
			$em->flush();
			return $this->returnCreationResponse($onlineOutgoerPreparationParticipation);
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Patch("/onlineOutgoerPreparationParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Edit an online outgoer preparation participation",
	 *  input="NoobBundle\Form\OnlineOutgoerPreparationParticipationType",
	 *  output="NoobBundle\Form\OnlineOutgoerPreparationParticipationType"
	 * )
	 */
	public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $participationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$onlineOutgoerPreparationParticipation = $em->getRepository('EntityBundle\Entity\OnlineOutgoerPreparationParticipation')->findOneById($participationID);
		if (!$onlineOutgoerPreparationParticipation) {
			throw new NotFoundHttpException();
		}
		$form = $this->createForm(OnlineOutgoerPreparationParticipationType::class, $onlineOutgoerPreparationParticipation, [
			'method' => 'PATCH'
		]);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em->merge($onlineOutgoerPreparationParticipation);
			$em->flush();
			return $this->returnModificationResponse($onlineOutgoerPreparationParticipation);
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Delete("/onlineOutgoerPreparationParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Delete an online outgoer preparation participation"
	 * )
	 */
	public function deleteAction(ParamFetcherInterface $paramFetcher, Request $request, $participationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$onlineOutgoerPreparationParticipation = $em->getRepository('EntityBundle\Entity\OnlineOutgoerPreparationParticipation')->findOneById($participationID);
		if (!$onlineOutgoerPreparationParticipation) {
			throw new NotFoundHttpException();
		}
		$em->remove($onlineOutgoerPreparationParticipation);
		$em->flush();
		return $this->returnDeletionResponse();
	}

}
