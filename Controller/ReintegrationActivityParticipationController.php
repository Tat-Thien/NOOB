<?php

namespace NoobBundle\Controller;

use EntityBundle\Entity\OutgoerPreparation;
use EntityBundle\Entity\ReintegrationActivityParticipation;
use EntityBundle\Entity\StandardReintegrationActivity;
use EntityBundle\Entity\WelcomeHomeSeminar;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use NoobBundle\Entity\ReintegrationActivity;
use NoobBundle\Form\OutgoerPreparationType;
use NoobBundle\Form\ReintegrationActivityParticipationType;
use NoobBundle\Form\ReintegrationActivityType;
use NoobBundle\Form\StandardReintegrationActivityType;
use NoobBundle\Form\WelcomeHomeSeminarType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("ReintegrationActivityParticipation")
 */
class ReintegrationActivityParticipationController extends NoobBundleController
{

	/**
	 * @REST\Get("/reintegrationActivityParticipations")
	 * @REST\QueryParam(name="reintegrationActivity", description="Reintegration activity ID")
	 * @REST\QueryParam(name="exchange", description="Exchange ID")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
	 * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get all reintegration activity participations",
	 *  output={"class"="NoobBundle\Form\ReintegrationActivityParticipationType", "collection"=true}
	 * )
	 */
	public function cgetAction(ParamFetcherInterface $paramFetcher)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();
		$reintegrationActivityID = $paramFetcher->get('reintegrationActivity');
		$exchangeID = $paramFetcher->get('exchange');
		$qb->select('r')->from('EntityBundle\Entity\ReintegrationActivityParticipation', 'r');
		if ($reintegrationActivityID) {
			$qb->andWhere('r.reintegrationActivity =:reintegrationActivityID')->setParameter('reintegrationActivityID', $reintegrationActivityID);
		}
		if ($exchangeID) {
			$qb->andWhere('r.exchange = :exchangeID')->setParameter('exchangeID', $exchangeID);
		}
		$query = $qb->getQuery();
		$pagination = $this->createPaginationObject($paramFetcher, $query);
		return $pagination;
	}

	/**
	 * @REST\Get("/reintegrationActivityParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get a reintegration activity participation",
	 *  output="NoobBundle\Form\ReintegrationActivityParticipationType"
	 * )
	 */
	public function getAction(ParamFetcherInterface $paramFetcher, $participationID)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('EntityBundle\Entity\ReintegrationActivityParticipation')->findOneById($participationID);
		if ($entity) {
			return $entity;
		} else {
			throw new HttpException(404);
		}
	}

	/**
	 * @REST\Post("/reintegrationActivityParticipations")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Create a reintegration activity participation",
	 *  input="NoobBundle\Form\ReintegrationActivityParticipationType",
	 *  output="NoobBundle\Form\ReintegrationActivityParticipationType"
	 * )
	 */
	public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
	{
		$this->checkAuthentication($paramFetcher, true);
		$reintegrationActivityParticipation = new ReintegrationActivityParticipation();
		$form = $this->createForm(new ReintegrationActivityParticipationType(), $reintegrationActivityParticipation);
		$form->submit($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($reintegrationActivityParticipation);
			$em->flush();
			return $this->returnCreationResponse($reintegrationActivityParticipation);
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Patch("/reintegrationActivityParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Edit a reintegration activity participation",
	 *  input="NoobBundle\Form\ReintegrationActivityParticipationType",
	 *  output="NoobBundle\Form\ReintegrationActivityParticipationType"
	 * )
	 */
	public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $participationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$reintegrationActivityParticipation = $em->getRepository('EntityBundle\Entity\ReintegrationActivityParticipation')->findOneById($participationID);
		if (!$reintegrationActivityParticipation) {
			throw new NotFoundHttpException();
		}
		$form = $this->createForm(new ReintegrationActivityParticipationType(), $reintegrationActivityParticipation, [
			'method' => 'PATCH'
		]);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em->merge($reintegrationActivityParticipation);
			$em->flush();
			return $this->returnModificationResponse($reintegrationActivityParticipation);
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Delete("/reintegrationActivityParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Delete a reintegration activity participation"
	 * )
	 */
	public function deleteAction(ParamFetcherInterface $paramFetcher, Request $request, $participationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$reintegrationActivityParticipation = $em->getRepository('EntityBundle\Entity\ReintegrationActivityParticipation')->findOneById($participationID);
		if (!$reintegrationActivityParticipation) {
			throw new NotFoundHttpException();
		}
		$em->remove($reintegrationActivityParticipation);
		$em->flush();
		return $this->returnDeletionResponse();
	}

}
