<?php

namespace NoobBundle\Controller;

use EntityBundle\Entity\AGBAgreement;
use FOS\RestBundle\Request\ParamFetcherInterface;
use NoobBundle\Form\AGBAgreementType;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @REST\RouteResource("AGBAgreement")
 */
class AgbAgreementController extends NoobBundleController
{

	/**
	 * @REST\Get("/agbAgreements")
	 * @REST\QueryParam(name="agb", description="AGB ID")
	 * @REST\QueryParam(name="exchange", description="Exchange ID")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
	 * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get all AGB agreements",
	 *  output={"class"="NoobBundle\Form\OutgoerPreparationParticipationType", "collection"=true}
	 * )
	 */

	public function cgetAction(ParamFetcherInterface $paramFetcher)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();
		$agbID = $paramFetcher->get('agb');
		$exchangeID = $paramFetcher->get('exchange');
		$qb->select('a')->from('EntityBundle\Entity\AGBAgreement', 'a');
		if ($agbID) {
			$qb->andWhere('a.agb = :agbID')->setParameter('agbID', $agbID);
		}
		if ($exchangeID) {
			$qb->andWhere('a.exchange = :exchangeID')->setParameter('exchangeID', $exchangeID);
		}
		$query = $qb->getQuery();
		$pagination = $this->createPaginationObject($paramFetcher, $query);
		return $pagination;
	}

	/**
	 * @REST\Get("/agbAgreements/{agbAgreementID}")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Get AGB data",
	 *  output={"class"="NoobBundle\Form\AGBAgreementType", "collection"=true}
	 * )
	 */
	public function getAction(ParamFetcherInterface $paramFetcher, $agbAgreementID)
	{
		$this->checkAuthentication($paramFetcher);
		$em = $this->getDoctrine()->getManager();
		$agbAgreement = $em->getRepository('EntityBundle\Entity\AGBAgreement')->findOneById($agbAgreementID);
		if (!$agbAgreement) {
			throw new NotFoundHttpException();
		}
		return $agbAgreement;
	}

	/**
	 * @REST\Post("/agbAgreements")
	 * @REST\QueryParam(name="access_token", allowBlank=false)
	 * @ApiDoc(
	 *  resource=true,
	 *  description="Sign AGBs",
	 *  input="NoobBundle\Form\AGBAgreementType",
	 *  output="NoobBundle\Form\AGBAgreementType"
	 * )
	 */
	public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
	{
		$this->checkAuthentication($paramFetcher);
		if(!$request->request->get('agb')){
			$request->request->set('agb', 1); //set standard agb, if not set
		}
		$em = $this->getDoctrine()->getManager();
		$applicationID = $request->request->get('applicationID');

		if(!$request->request->get('exchange') && $applicationID){
			$exchange = $em->getRepository('EntityBundle\Entity\Exchange')->findOneByApplicationID($applicationID);
			if ($exchange) {
				$request->request->set('exchange', $exchange->getId());
			}
		}
		$agbAgreement = new AGBAgreement();
		$form = $this->createForm(AGBAgreementType::class, $agbAgreement);
		$form->submit($request);
		if ($form->isValid()) {
			$em->persist($agbAgreement);
			$em->flush();
			return $this->returnCreationResponse($agbAgreement);
		}
		return array(
			'form' => $form
		);
	}
}
