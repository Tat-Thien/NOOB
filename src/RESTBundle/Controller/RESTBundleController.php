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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class RESTBundleController extends FOSRestController
{

    protected function checkAuthentication(ParamFetcherInterface $paramFetcher, $advanced=false)
    {
        $providedAccessToken = $this->extractAccessToken($paramFetcher);
        $validTokens = [$this->getParameter('advanced_access_token')];
        if (!$advanced) {
            array_push($validTokens, $this->getParameter('simple_access_token'));
        }
        if (!in_array($providedAccessToken, $validTokens)) {
            throw new AccessDeniedHttpException();
        }
    }

    protected function createPaginationObject(ParamFetcherInterface $paramFetcher, Query $query)
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

    protected function redirectWithAccessToken($route, array $parameters = array(), ParamFetcherInterface $paramFetcher)
    {
        $parameters['access_token'] = $this->extractAccessToken($paramFetcher);
        return $this->routeRedirectView($route, $parameters);

    }

    private function extractAccessToken(ParamFetcherInterface $paramFetcher)
    {
        return $paramFetcher->get('access_token');
    }

}