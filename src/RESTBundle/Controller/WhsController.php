<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\OutgoerPreparationType;
use RESTBundle\Form\WelcomeHomeSeminarType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("Whs")
 */
class WhsController extends RESTBundleController
{

    /**
     * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all WHS"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('w')->from('AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar', 'w');
        $salesforceID = $paramFetcher->get('salesforceID');
        if ($salesforceID) {
            $qb->andWhere('w.salesforceID LIKE :sfID')->setParameter('sfID', $salesforceID . '%');
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get WHS"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $whsID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar')->findOneById($whsID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create WHS",
     *  input="RESTBundle\Form\WelcomeHomeSeminarType",
     *  output="RESTBundle\Form\WelcomeHomeSeminarType"
     * )
     */
    public function postAction(Request $request)
    {
        $whs = new WelcomeHomeSeminar();
        $form = $this->createForm(new WelcomeHomeSeminarType(), $whs);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($whs);
            $em->flush();
            return $this->routeRedirectView('get_whs', array('whsID' => $whs->getId()));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Edit a WHS",
     *  input="RESTBundle\Form\WelcomeHomeSeminarType",
     *  output="RESTBundle\Form\WelcomeHomeSeminarType"
     * )
     * @REST\Patch
     */
    public function patchAction(Request $request, $whsID)
    {
        //$this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $whs = $em->getRepository('AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar')->findOneById($whsID);
        if (!$whs) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new WelcomeHomeSeminarType(), $whs, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($whs);
            $em->flush();
            return $this->routeRedirectView('get_whs', array('whsID' => $whsID));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a WHS"
     * )
     */
    public function deleteAction($whsID)
    {
        $em = $this->getDoctrine()->getManager();
        $whs = $em->getRepository('AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar')->findOneById($whsID);
        if (!$whs) {
            throw new NotFoundHttpException();
        }
        $em->remove($whs);
        $em->flush();
        return $this->view(null, 204);
    }
}