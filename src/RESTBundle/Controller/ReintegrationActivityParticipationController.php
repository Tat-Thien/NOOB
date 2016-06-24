<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use AIESECGermany\EntityBundle\Entity\ReintegrationActivityParticipation;
use AIESECGermany\EntityBundle\Entity\StandardReintegrationActivity;
use AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Entity\ReintegrationActivity;
use RESTBundle\Form\OutgoerPreparationType;
use RESTBundle\Form\ReintegrationActivityParticipationType;
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
 * @REST\RouteResource("ReintegrationActivityParticipation")
 */
class ReintegrationActivityParticipationController extends RESTBundleController
{

    /**
     * @REST\Get("/reintegrationActivityParticipations")
     * @REST\QueryParam(name="reintegrationActivity", description="Reintegration activity ID")
     * @REST\QueryParam(name="exchange", description="Exchange ID")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all reintegration activity participations",
     *  output={"class"="RESTBundle\Form\ReintegrationActivityParticipationType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $reintegrationActivityID = $paramFetcher->get('reintegrationActivity');
        $exchangeID = $paramFetcher->get('exchange');
        $qb->select('r')->from('AIESECGermany\EntityBundle\Entity\ReintegrationActivityParticipation', 'r');
        if ($reintegrationActivityID) {
            $qb->andWhere('r.reintegrationActivity =:reintegrationActivityID')->setParameter('reintegrationActivityID', $reintegrationActivityID);
        }
        if ($exchangeID) {
            $qb->andWhere('r.exchange = :exchangeID')->setParameter('exchangeID', $exchangeID);
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
    }

    /**
     * @REST\Get("/reintegrationActivityParticipations/{participationID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get a reintegration activity participation",
     *  output="RESTBundle\Form\ReintegrationActivityParticipationType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $participationID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\ReintegrationActivityParticipation')->findOneById($participationID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @REST\Post("/reintegrationActivityParticipations")
     * @ApiDoc(
     *  resource=true,
     *  description="Create a reintegration activity participation",
     *  input="RESTBundle\Form\ReintegrationActivityParticipationType",
     *  output="RESTBundle\Form\ReintegrationActivityParticipationType"
     * )
     */
    public function postAction(Request $request)
    {
        $reintegrationActivityParticipation = new ReintegrationActivityParticipation();
        $form = $this->createForm(new ReintegrationActivityParticipationType(), $reintegrationActivityParticipation);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reintegrationActivityParticipation);
            $em->flush();
            return $this->routeRedirectView('get_reintegrationactivityparticipation',
                array('participationID' => $reintegrationActivityParticipation->getId()));
        }
        return array(
            'form' => $form
        );
    }

}