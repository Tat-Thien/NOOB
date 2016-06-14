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

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get all AGB data"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findAll();
        return $entities;
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