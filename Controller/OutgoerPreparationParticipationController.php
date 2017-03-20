<?php

namespace NoobBundle\Controller;

use EntityBundle\Entity\OutgoerPreparation;
use EntityBundle\Entity\OutgoerPreparationParticipation;
use EntityBundle\Entity\StandardReintegrationActivity;
use EntityBundle\Entity\WelcomeHomeSeminar;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use NoobBundle\Entity\ReintegrationActivity;
use NoobBundle\Form\OutgoerPreparationType;
use NoobBundle\Form\OutgoerPreparationParticipationType;
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
 * @REST\RouteResource("OutgoerPreparationParticipation")
 */
class OutgoerPreparationParticipationController extends NoobBundleController
{

	/**
	 * @REST\Get("/outgoerPreparationParticipations")
	 * @REST\QueryParam(name="outgoerPreparation", description="Outgoer Preparation ID")
	 * @REST\QueryParam(name="person", description="Person ID")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
	 * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get all outgoer preparation participations",
	 *  output={"class"="NoobBundle\Form\OutgoerPreparationParticipationType", "collection"=true}
	 * )
	 */
	public function cgetAction(ParamFetcherInterface $paramFetcher)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();
		$outgoerPreparationID = $paramFetcher->get('outgoerPreparation');
		$personID = $paramFetcher->get('person');
		$qb->select('o')->from('EntityBundle\Entity\OutgoerPreparationParticipation', 'o');
		if ($outgoerPreparationID) {
			$qb->andWhere('o.outgoerPreparation = :outgoerPreparationID')->setParameter('outgoerPreparationID', $outgoerPreparationID);
		}
		if ($personID) {
			$qb->andWhere('o.person = :personID')->setParameter('personID', $personID);
		}
		$query = $qb->getQuery();
		$pagination = $this->createPaginationObject($paramFetcher, $query);
		return $pagination;
	}

	/**
	 * @REST\Get("/outgoerPreparationParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get a outgoer preparation participation",
	 *  output="NoobBundle\Form\OutgoerPreparationParticipationType"
	 * )
	 */
	public function getAction(ParamFetcherInterface $paramFetcher, $participationID)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('EntityBundle\Entity\OutgoerPreparationParticipation')->findOneById($participationID);
		if ($entity) {
			return $entity;
		} else {
			throw new HttpException(404);
		}
	}

	/**
	 * @REST\Post("/outgoerPreparationParticipations")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Create a outgoer preparation participation",
	 *  input="NoobBundle\Form\OutgoerPreparationParticipationType",
	 *  output="NoobBundle\Form\OutgoerPreparationParticipationType"
	 * )
	 */
	public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
	{
		$this->checkAuthentication($paramFetcher, true);
		$outgoerPreparationParticipation = new OutgoerPreparationParticipation();
		$form = $this->createForm(OutgoerPreparationParticipationType::class, $outgoerPreparationParticipation);
		$form->submit($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($outgoerPreparationParticipation);
			$em->flush();
			return $this->returnCreationResponse($outgoerPreparationParticipation);
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Patch("/outgoerPreparationParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Edit an outgoer preparation participation",
	 *  input="NoobBundle\Form\OutgoerPreparationParticipationType",
	 *  output="NoobBundle\Form\OutgoerPreparationParticipationType"
	 * )
	 */
	public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $participationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$outgoerPreparationParticipation = $em->getRepository('EntityBundle\Entity\OutgoerPreparationParticipation')->findOneById($participationID);
		if (!$outgoerPreparationParticipation) {
			throw new NotFoundHttpException();
		}
		$form = $this->createForm(OutgoerPreparationParticipationType::class, $outgoerPreparationParticipation, [
			'method' => 'PATCH'
		]);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em->merge($outgoerPreparationParticipation);
			$em->flush();
			return $this->returnModificationResponse($outgoerPreparationParticipation);
		}
		return array(
			'form' => $form
		);
	}

	/**
	 * @REST\Delete("/outgoerPreparationParticipations/{participationID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Delete an outgoer preparation participation"
	 * )
	 */
	public function deleteAction(ParamFetcherInterface $paramFetcher, Request $request, $participationID)
	{
		$this->checkAuthentication($paramFetcher, true);
		$em = $this->getDoctrine()->getManager();
		$outgoerPreparationParticipation = $em->getRepository('EntityBundle\Entity\OutgoerPreparationParticipation')->findOneById($participationID);
		if (!$outgoerPreparationParticipation) {
			throw new NotFoundHttpException();
		}
		$em->remove($outgoerPreparationParticipation);
		$em->flush();
		return $this->returnDeletionResponse();
	}

}
