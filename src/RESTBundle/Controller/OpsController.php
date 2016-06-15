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

    /**
     * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
     * @REST\QueryParam(name="access_token", allowBlank=false)
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
        $ops = $query->getResult();
        return $ops;
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
}