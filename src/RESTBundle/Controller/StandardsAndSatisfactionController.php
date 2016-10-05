<?php

namespace RESTBundle\Controller;
use AIESECGermany\EntityBundle\Entity\AGB;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\StandardsAndSatisfactionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @REST\RouteResource("StandardsAndSatisfaction")
 */
class StandardsAndSatisfactionController extends RESTBundleController
{

    /**
     * @REST\Get("/standardsAndSatisfaction")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all Standards and Satisfaction data",
     *  output={"class"="RESTBundle\Form\StandardsAndSatisfactionType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')->from('AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction', 's');
        $pagination = $this->createPaginationObject($paramFetcher, $qb->getQuery());
        return $pagination;
    }

    /**
     * @REST\Get("/standardsAndSatisfaction/{snsID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get Standards and Satisfaction data",
     *  output="RESTBundle\Form\StandardsAndSatisfactionType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $snsID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction')->findOneById($snsID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

}