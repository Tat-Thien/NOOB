<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\OutgoerPreparationType;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("OutgoerPreparation")
 */
class OutgoerPreparationController extends RESTBundleController
{

    /**
     * @REST\Get("/outgoerPreparations")
     * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all outgoer preparations",
     *  output={"class"="RESTBundle\Form\OutgoerPreparationType", "collection"=true}
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
     * @REST\Get("/outgoerPreparations/{outgoerPreparationID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get outgoer preparation",
     *  output="RESTBundle\Form\OutgoerPreparationType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $outgoerPreparationID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\OutgoerPreparation')->findOneById($outgoerPreparationID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @REST\Post("/outgoerPreparations")
     * @ApiDoc(
     *  resource=true,
     *  description="Create outgoer preparation",
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
            return $this->routeRedirectView('get_outgoerpreparation', array('outgoerPreparationID' => $ops->getId()));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch("/outgoerPreparations/{outgoerPreparationID}")
     * @ApiDoc(
     *  resource=true,
     *  description="Edit an outgoer preparations",
     *  input="RESTBundle\Form\OutgoerPreparationType",
     *  output="RESTBundle\Form\OutgoerPreparationType"
     * )
     */
    public function patchAction(Request $request, $outgoerPreparationID)
    {
        //$this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $ops = $em->getRepository('AIESECGermany\EntityBundle\Entity\OutgoerPreparation')->findOneById($outgoerPreparationID);
        if (!$ops) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new OutgoerPreparationType(), $ops, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($ops);
            $em->flush();
            return $this->routeRedirectView('get_outgoerpreparation', array('outgoerPreparationID' => $outgoerPreparationID));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Delete("/outgoerPreparations/{outgoerPreparationID}")
     * @ApiDoc(
     *  resource=true,
     *  description="Delete an outgoer preparations"
     * )
     */
    public function deleteAction($outgoerPreparationID)
    {
        $em = $this->getDoctrine()->getManager();
        $ops = $em->getRepository('AIESECGermany\EntityBundle\Entity\OutgoerPreparation')->findOneById($outgoerPreparationID);
        if (!$ops) {
            throw new NotFoundHttpException();
        }
        $em->remove($ops);
        $em->flush();
        return $this->view(null, 204);
    }
}