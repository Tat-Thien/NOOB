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
     *  output={"class"="RESTBundle\Entity\AgbAgreementData", "collection"=true}
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
            // ->andWhere('e.agbSignDate IS NOT NULL')
            ;
        if ($agbID) {
            $qb->andWhere('e.agb = :agbID')->setParameter('agbID', $agbID);
        }
        if ($exchangeID) {
            $qb->andWhere('e.id = :exchangeID')->setParameter('exchangeID', $exchangeID);
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query, 'AgbAgreementData::fromExchange');
        return $pagination;
    }

}
