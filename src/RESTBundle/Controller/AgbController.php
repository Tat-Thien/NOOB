<?php

namespace RESTBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * @REST\RouteResource("Agb")
 */
class AgbController extends FOSRestController
{

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Get all AGB data"
     * )
     */
    public function cgetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findAll();
        return $entities;
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Get AGB data"
     * )
     */
    public function getAction($agbID)
    {
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