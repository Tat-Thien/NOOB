<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\OutgoerPreparationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class RESTBundleController extends FOSRestController
{

    protected function checkAuthentication(ParamFetcherInterface $paramFetcher, $advanced=false)
    {
        $providedAccessToken = $paramFetcher->get('access_token');
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

}