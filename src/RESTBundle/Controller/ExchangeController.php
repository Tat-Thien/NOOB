<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\ExchangeAGB;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @REST\RouteResource("Exchange")
 */
class ExchangeController extends RESTBundleController
{

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Return all exchanges",
     *  output={"class"="RESTBundle\Form\ExchangeType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from('AIESECGermany\EntityBundle\Entity\Exchange', 'e');
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Return an exchange",
     *  output="RESTBundle\Form\ExchangeType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $exchangeId)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeId);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

}
