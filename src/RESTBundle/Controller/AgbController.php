<?php

namespace RESTBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * @REST\RouteResource("Agb")
 */
class AgbController extends FOSRestController
{

    private function checkAuthentication(ParamFetcherInterface $paramFetcher)
    {
        $providedAccessToken = $paramFetcher->get('access_token');
        $securedAccessToken = $this->getParameter('access_token');
        if ($providedAccessToken != $securedAccessToken) {
            throw new AccessDeniedHttpException();
        }
    }

    private function createPaginationObject(ParamFetcherInterface $paramFetcher, Query $query)
    {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all AGB data"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a')->from('AIESECGermany\EntityBundle\Entity\AGB', 'a');
        $pagination = $this->createPaginationObject($paramFetcher, $qb->getQuery());
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get AGB data"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $agbID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findOneById($agbID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    public function postAction($agbID)
    {
    }
}