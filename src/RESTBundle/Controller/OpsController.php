<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\OutgoerPreparationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @REST\RouteResource("Ops")
 */
class OpsController extends FOSRestController
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
     * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all OPS"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('o')->from('AIESECGermany\EntityBundle\Entity\OutgoerPreparation', 'o');
        $salesforceID = $paramFetcher->get('salesforceID');
        if ($salesforceID) {
            $qb->andWhere('o.salesforceID LIKE :sfID')->setParameter('sfID', $salesforceID . '%');
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get OPS"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $opsID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\OutgoerPreparation')->findOneById($opsID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create OPS",
     *  input="RESTBundle\Form\OutgoerPreparationType",
     *  output="RESTBundle\Form\OutgoerPreparationType"
     * )
     */
    public function postAction(Request $request)
    {
        $ops = new OutgoerPreparation();
        $form = $this->createForm(new OutgoerPreparationType(), $ops);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ops);
            $em->flush();
            return $this->routeRedirectView('get_ops', array('opsID' => $ops->getId()));
        }
        return (string) $form->getErrors(true);
        return array(
            'form' => $form
        );
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Edit an OPS",
     *  input="RESTBundle\Form\OutgoerPreparationType",
     *  output="RESTBundle\Form\OutgoerPreparationType"
     * )
     * @REST\Patch
     */
    public function patchAction(Request $request, $opsID)
    {
        //$this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $ops = $em->getRepository('AIESECGermany\EntityBundle\Entity\OutgoerPreparation')->findOneById($opsID);
        $form = $this->createForm(new OutgoerPreparationType(), $ops, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($ops);
            $em->flush();
            return $this->routeRedirectView('get_ops', array('opsID' => $opsID));
        }
        return array(
            'form' => $form
        );
    }
}