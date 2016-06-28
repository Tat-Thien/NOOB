<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use AIESECGermany\EntityBundle\Entity\StandardReintegrationActivity;
use AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Entity\ReintegrationActivity;
use RESTBundle\Form\OutgoerPreparationType;
use RESTBundle\Form\ReintegrationActivityType;
use RESTBundle\Form\StandardReintegrationActivityType;
use RESTBundle\Form\WelcomeHomeSeminarType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("ReintegrationActivity")
 */
class ReintegrationActivityController extends RESTBundleController
{

    /**
     * @REST\Get("/reintegrationActivities")
     * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
     * @REST\QueryParam(name="name", description="name")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all reintegration activities",
     *  output={"class"="RESTBundle\Form\ReintegrationActivityType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $salesforceID = $paramFetcher->get('salesforceID');
        $name = $paramFetcher->get('name');
        if ($salesforceID) {
            $qb->select('r')->from('AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar', 'r');
            $qb->andWhere('r.salesforceID LIKE :sfID')->setParameter('sfID', $salesforceID . '%');
        }
        else if ($name) {
            $qb->select('r')->from('AIESECGermany\EntityBundle\Entity\StandardReintegrationActivity', 'r');
            $qb->andWhere('r.name = :name')->setParameter('name', $name);
        } else {
            $qb->select('r')->from('AIESECGermany\EntityBundle\Entity\ReintegrationActivity', 'r');
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
    }

    /**
     * @REST\Get("/reintegrationActivities/{reintegrationActivityID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get a reintegration activity",
     *  output="RESTBundle\Form\ReintegrationActivityType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $reintegrationActivityID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\ReintegrationActivity')->findOneById($reintegrationActivityID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @REST\Post("/reintegrationActivities")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Create WHS",
     *  input="RESTBundle\Form\ReintegrationActivityType",
     *  output="RESTBundle\Form\ReintegrationActivityType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher, true);
        $reintegrationActivity = new ReintegrationActivity();
        $form = $this->createForm(new ReintegrationActivityType(), $reintegrationActivity);
        $form->submit($request);
        if ($form->isValid()) {
            $type = $reintegrationActivity->getType();
            if ($type != 'welcomeHomeSeminar' && $type != 'standardReintegrationActivity') {
                throw new BadRequestHttpException();
            }
            $isWhs = $reintegrationActivity->getType() == 'welcomeHomeSeminar';
            $activity = $isWhs ? new WelcomeHomeSeminar() : new StandardReintegrationActivity();
            $activityType = $isWhs ? new WelcomeHomeSeminarType() : new StandardReintegrationActivityType();
            $form = $this->createForm($activityType, $activity);
            $form->submit($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($activity);
                $em->flush();
                return $this->returnCreationResponse($activity);
            }
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch("/reintegrationActivities/{reintegrationActivityID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit a WHS",
     *  input="RESTBundle\Form\ReintegrationActivityType",
     *  output="RESTBundle\Form\ReintegrationActivityType"
     * )
     */
    public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $reintegrationActivityID)
    {
        $this->checkAuthentication($paramFetcher, true);
        $em = $this->getDoctrine()->getManager();
        $reintegrationActivity = $em->getRepository('AIESECGermany\EntityBundle\Entity\ReintegrationActivity')->findOneById($reintegrationActivityID);
        if (!$reintegrationActivity) {
            throw new NotFoundHttpException();
        }
        $isWhs = get_class($reintegrationActivity) == WelcomeHomeSeminar::class;
        $reintegrationActivityType = $isWhs ? new WelcomeHomeSeminarType() : new StandardReintegrationActivityType();
        $form = $this->createForm($reintegrationActivityType, $reintegrationActivity, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($reintegrationActivity);
            $em->flush();
            return $this->returnModificationResponse($reintegrationActivity);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Delete("/reintegrationActivities/{reintegrationActivityID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a reintegration activity"
     * )
     */
    public function deleteAction(ParamFetcherInterface $paramFetcher, $reintegrationActivityID)
    {
        $this->checkAuthentication($paramFetcher, true);
        $em = $this->getDoctrine()->getManager();
        $whs = $em->getRepository('AIESECGermany\EntityBundle\Entity\ReintegrationActivity')->findOneById($reintegrationActivityID);
        if (!$whs) {
            throw new NotFoundHttpException();
        }
        $em->remove($whs);
        $em->flush();
        return $this->returnDeletionResponse();
    }
}