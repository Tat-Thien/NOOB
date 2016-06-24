<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use AIESECGermany\EntityBundle\Entity\OutgoerPreparationParticipation;
use AIESECGermany\EntityBundle\Entity\StandardReintegrationActivity;
use AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Entity\ReintegrationActivity;
use RESTBundle\Form\OutgoerPreparationType;
use RESTBundle\Form\OutgoerPreparationParticipationType;
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
 * @REST\RouteResource("OutgoerPreparationParticipation")
 */
class OutgoerPreparationParticipationController extends RESTBundleController
{

    /**
     * @REST\Get("/outgoerPreparationParticipations")
     * @REST\QueryParam(name="outgoerPreparation", description="Outgoer Preparation ID")
     * @REST\QueryParam(name="person", description="Person ID")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all outgoer preparation participations",
     *  output={"class"="RESTBundle\Form\OutgoerPreparationParticipationType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $outgoerPreparationID = $paramFetcher->get('outgoerPreparation');
        $personID = $paramFetcher->get('person');
        $qb->select('o')->from('AIESECGermany\EntityBundle\Entity\OutgoerPreparationParticipation', 'o');
        if ($outgoerPreparationID) {
            $qb->andWhere('o.outgoerPreparation = :outgoerPreparationID')->setParameter('outgoerPreparationID', $outgoerPreparationID);
        }
        if ($personID) {
            $qb->andWhere('r.person = :personID')->setParameter('personID', $personID);
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
    }

    /**
     * @REST\Get("/outgoerPreparationParticipation/{participationID}")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get a outgoer preparation participation",
     *  output="RESTBundle\Form\OutgoerPreparationParticipationType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $participationID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\OutgoerPreparationParticipation')->findOneById($participationID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @REST\Post("/outgoerPreparationParticipations")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Create a outgoer preparation participation",
     *  input="RESTBundle\Form\OutgoerPreparationParticipationType",
     *  output="RESTBundle\Form\OutgoerPreparationParticipationType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher, true);
        $outgoerPreparationParticipation = new OutgoerPreparationParticipation();
        $form = $this->createForm(new OutgoerPreparationParticipationType(), $outgoerPreparationParticipation);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($outgoerPreparationParticipation);
            $em->flush();
            return $this->routeRedirectView('get_outgoerpreparationparticipations',
                array('participationID' => $outgoerPreparationParticipation->getId()));
        }
        return array(
            'form' => $form
        );
    }

}