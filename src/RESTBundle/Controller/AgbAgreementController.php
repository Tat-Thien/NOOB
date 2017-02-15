<?php

namespace RESTBundle\Controller;

use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\AGBAgreementType;
use RESTBundle\Entity\AGBAgreementData;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @REST\RouteResource("AGBAgreement")
 */
class AgbAgreementController extends RESTBundleController
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
     *  output={"class"="RESTBundle\Form\agbAgreementType", "collection"=true}
     * )
     */

    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {   
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $agbID = $paramFetcher->get('agb');
        $exchangeID = $paramFetcher->get('exchange');
        $qb->select('e')->from('AIESECGermany\EntityBundle\Entity\Exchange', 'e')
            ->andWhere('e.agbSignDate IS NOT NULL');
        if ($agbID) {
            $qb->andWhere('e.agb = :agbID')->setParameter('agbID', $agbID);
        }
        if ($exchangeID) {
            $qb->andWhere('e.id = :exchangeID')->setParameter('exchangeID', $exchangeID);
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query, 'AGBAgreementData');
        return $pagination;
    }

    /**
     * @REST\Get("/agbAgreements/{agbAgreementID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get AGB data",
     *  output={"class"="RESTBundle\Form\AGBAgreementType", "collection"=true}
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $agbAgreementID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneByAgb($agbAgreementID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        return new AGBAgreementData($exchange);
    }

    /**
     * @REST\Post("/agbAgreements")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Sign AGBs",
     *  input="RESTBundle\Form\AGBAgreementType",
     *  output="RESTBundle\Form\AGBAgreementType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $agb = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findOneBy(
            array(),
            array('id'=>'ASC')
        );
        $agbAgreement = new AGBAgreementData($agb);
        $form = $this->createForm(new AGBAgreementType(), $agbAgreement);
        $form->submit($request);
        if ($form->isValid()) {
            $exchange = $agbAgreement->getExchange();
            $exchange->setAgb($agbAgreement->getAgb());
            $exchange->setAgbSignDate($agbAgreement->getDateSigned());
            $em->persist($exchange);
            $em->flush();
            return $this->returnCreationResponse($agbAgreement);
        }
        return array(
            'form' => $form
        );
    }
}
