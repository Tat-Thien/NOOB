<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\AGBAgreement;
use Doctrine\ORM\Query;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\AGBAgreementType;
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
     *  output={"class"="RESTBundle\Form\OutgoerPreparationParticipationType", "collection"=true}
     * )
     */

    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $agbID = $paramFetcher->get('agb');
        $exchangeID = $paramFetcher->get('exchange');
        $qb->select('a')->from('AIESECGermany\EntityBundle\Entity\AGBAgreement', 'a');
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
     *  output={"class"="RESTBundle\Form\AGBAgreementType", "collection"=true}
     * )
     */
    public function getAction(ParamFetcher $paramFetcher, $agbAgreementID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $agbAgreement = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGBAgreement')->findOneById($agbAgreementID);
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
     *  input="RESTBundle\Form\AGBAgreementType",
     *  output="RESTBundle\Form\AGBAgreementType"
     * )
     */
    public function postAction(ParamFetcher $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher);
        $agbAgreement = new AGBAgreement();
        $form = $this->createForm(new AGBAgreementType(), $agbAgreement);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($agbAgreement);
            $em->flush();
            return $this->redirectWithAccessToken('get_agbagreement', array('agbAgreementID' => $agbAgreement->getId()),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }
}
