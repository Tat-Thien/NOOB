<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use RESTBundle\Form\OutgoerPreparationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class RESTBundleController extends FOSRestController
{

    protected function checkAuthentication(ParamFetcherInterface $paramFetcher, $advanced=false)
    {   
        $authenticated = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');
        if (!$authenticated) {
            throw new AccessDeniedHttpException();
        }
    }

    protected function createPaginationObject(ParamFetcherInterface $paramFetcher, Query $query, $wrapQueries = false)
    {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit,
            array('wrap-queries'=> $wrapQueries)
        );
        return $pagination;
    }

    protected function redirectWithAccessToken($route, array $parameters = array(), ParamFetcherInterface $paramFetcher)
    {
        $parameters['access_token'] = $this->extractAccessToken($paramFetcher);
        return $this->routeRedirectView($route, $parameters);

    }

    private function extractAccessToken(ParamFetcherInterface $paramFetcher)
    {
        return $paramFetcher->get('access_token');
    }

    protected function returnCreationResponse($createdObject)
    {
        return $this->view($createdObject, Response::HTTP_CREATED);
    }

    protected function returnModificationResponse()
    {
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    protected function returnDeletionResponse()
    {
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

}