<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\AGB;
use AIESECGermany\EntityBundle\Entity\BankAccount;
use AIESECGermany\EntityBundle\Entity\Exchange;
use AIESECGermany\EntityBundle\Entity\ExchangeAGB;
use AIESECGermany\EntityBundle\Entity\Person;
use AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\AGBType;
use RESTBundle\Form\BankAccountType;
use RESTBundle\Form\ExchangeAGBType;
use RESTBundle\Form\ExchangeType;
use FOS\RestBundle\Controller\Annotations as REST;
use RESTBundle\Form\PersonType;
use RESTBundle\Form\StandardsAndSatisfactionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @REST\RouteResource("People")
 */
class PeopleController extends FOSRestController
{
    
    public function postDummyAction()
    {
        $person = new Person();
        $person->setId(440715);
        $exchange1 = new Exchange();
        $exchange1->setFocusOfInternship("gcdp");
        $exchange1->setFeeAmount(120);
        $exchange1->setPerson($person);
        $exchange2 = new Exchange();
        $exchange2->setFocusOfInternship("gip");
        $exchange2->setFeeAmount(420);
        $exchange2->setPerson($person);
        $person->addExchange($exchange1);
        $person->addExchange($exchange2);
        $em = $this->getDoctrine()->getManager();
        $em->persist($person);
        $em->persist($exchange1);
        $em->persist($exchange2);
        $em->flush();
        return new Response('', Response::HTTP_OK);
    }

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
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Return all people"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')->from('AIESECGermany\EntityBundle\Entity\Person', 'p');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );

        $serializer = $this->get('jms_serializer');
        return new Response($serializer->serialize($pagination, 'json'), Response::HTTP_OK,
            ['Content-Type' => 'application/json',
        ]
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Return a person"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create a person",
     *  input="RESTBundle\Form\PersonType",
     *  output="RESTBundle\Form\PersonType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        //$this->checkAuthentication($paramFetcher);
        $person = new Person();
        $form = $this->createForm(new PersonType(), $person);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            return $this->routeRedirectView('get_people', array('personID' => $person->getId()));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Edit a person",
     *  input="RESTBundle\Form\PersonType",
     *  output="RESTBundle\Form\PersonType"
     * )
     * @REST\Patch
     */
    public function patchAction(Request $request, $personID)
    {
        //$this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        $form = $this->createForm(new PersonType(), $person, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($person);
            $em->flush();
            return $this->routeRedirectView('get_people', array('personID' => $person->getId()));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get an exchange",
     *  output="RESTBundle\Form\ExchangeType"
     * )
     */
    public function getExchangesAction(ParamFetcherInterface $paramFetcher, $personID, $exchangeID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        return $exchange;
    }
    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all exchanges"
     * )
     */
    public function cgetExchangesAction(ParamFetcher $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from('AIESECGermany\EntityBundle\Entity\Exchange', 'e')->where('e.person = ?1')
            ->setParameter(1, $personID);
        $salesforceID = $paramFetcher->get('salesforceID');
        if ($salesforceID) {
            $qb->andWhere('e.salesforceID = ?2')->setParameter(2, $salesforceID);
        }
        $query = $qb->getQuery();
        $exchanges = $query->getResult();
        return $exchanges;
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create an exchange",
     *  input="RESTBundle\Form\ExchangeType",
     *  output="RESTBundle\Form\ExchangeType"
     * )
     */
    public function postExchangesAction(ParamFetcher $paramFetcher, Request $request, $personID)
    {
        //$this->checkAuthentication($paramFetcher);
        $exchange = new Exchange();
        $form = $this->createForm(new ExchangeType(), $exchange);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
            if (!$person) {
                throw new HttpException(404);
            }
            $person->addExchange($exchange);
            $exchange->setPerson($person);
            $em->persist($person);
            $em->persist($exchange);
            $em->flush();
            return $this->routeRedirectView('get_people_exchanges', array('personID' => $personID, 'exchangeID' => $exchange->getId()));
        }
        return array(
            'form' => $form
        );
    }


    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Edit an exchange",
     *  input="RESTBundle\Form\ExchangeType",
     *  output="RESTBundle\Form\ExchangeType"
     * )
     * @REST\Patch
     */
    public function patchExchangeAction(Request $request, $personID, $exchangeID)
    {
        //$this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        $form = $this->createForm(new ExchangeType(), $exchange, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($exchange);
            $em->flush();
            return $this->routeRedirectView('get_people_exchanges', array(
                'personID' => $personID, 'exchangeID' => $exchange->getId()));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get AGB data"
     * )
     */
    public function getExchangesAgbAction(ParamFetcher $paramFetcher, $personID, $exchangeID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        $agb = $exchange->getExchangeAgb();
        if (!$person || !$exchange || !$agb) {
            throw new HttpException(404);
        }
        return $agb;
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Sign AGBs",
     *  input="RESTBundle\Form\ExchangeAGBType",
     *  output="RESTBundle\Form\ExchangeAGBType"
     * )
     */
    public function postExchangesAgbAction(ParamFetcher $paramFetcher, Request $request, $personID, $exchangeID)
    {
        //$this->checkAuthentication($paramFetcher);
        $exchangeAGB = new ExchangeAGB();
        $form = $this->createForm(new ExchangeAGBType(), $exchangeAGB);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
            if (!$person) {
                throw new HttpException(404);
            }
            $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
            if (!$exchange) {
                throw new HttpException(404);
            }
            $exchangeAGB->setExchange($exchange);
            $exchange->setExchangeAgb($exchangeAGB);
            $em->persist($exchange);
            $em->persist($exchangeAGB);
            $em->flush();
            return $this->routeRedirectView('get_people_exchanges_agb', array('personID' => $personID, 'exchangeID' => $exchangeID));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Edit a signed AGB",
     *  input="RESTBundle\Form\ExchangeAGBType",
     *  output="RESTBundle\Form\ExchangeAGBType"
     * )
     * @REST\Patch
     */
    public function patchExchangesAgbAction(Request $request, $personID, $exchangeID)
    {
        //$this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        $exchangeAGB = $exchange->getExchangeAgb();
        $form = $this->createForm(new ExchangeAGBType(), $exchangeAGB, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($exchangeAGB);
            $em->flush();
            return $this->routeRedirectView('get_people_exchanges_agb', array(
                'personID' => $personID, 'exchangeID' => $exchange->getId()));
        }
        return array(
            'form' => $form
        );
    }

    public function getExchangesBankaccountAction($personID, $exchangeID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        $bankAccount = $exchange->getBankAccount();
        if (!$person || !$exchange || !$bankAccount) {
            throw new NotFoundHttpException();
        }
        return $bankAccount;
    }

    public function postExchangesBankaccountAction(Request $request, $personID, $exchangeID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new HttpException(404);
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new HttpException(404);
        }
        $bankAccount = new BankAccount();
        $form = $this->createForm(new BankAccountType(), $bankAccount);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $exchange->setBankAccount($bankAccount);
            $bankAccount->setExchange($exchange);
            $em->persist($exchange);
            $em->persist($bankAccount);
            $em->flush();
            return $this->routeRedirectView('post_people_exchanges_bankaccount', array('personID' => $personID, 'exchangeID' => $exchangeID));
        }
        return array(
            'form' => $form
        );
    }

    public function getExchangesStandardsandsatisfactionAction($personID, $exchangeID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new HttpException(404);
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new HttpException(404);
        }
        $sands = $exchange->getStandardsAndSatisfaction();
        if (!$sands) {
            throw new HttpException(404);
        }
        return $sands;
    }

    public function postExchangesStandardsandsatisfactionAction(Request $request, $personID, $exchangeID)
    {
        $logger = $this->get('logger');
        $logger->info("content:");
        $logger->info($request->getContent());
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new HttpException(404);
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new HttpException(404);
        }
        $sands = new StandardsAndSatisfaction();
        if ($exchange->getStandardsAndSatisfaction()) {
            $sands = $exchange->getStandardsAndSatisfaction();
        }
        $form = $this->createForm(new StandardsAndSatisfactionType(), $sands);
        $form->submit($request);
        if ($form->isValid()) {
            $exchange->setStandardsAndSatisfaction($sands);
            $em->persist($exchange);
            $em->persist($sands);
            $em->flush();
            return $this->routeRedirectView('get_people_exchanges_standardsandsatisfaction', array('personID' => $personID, 'exchangeID' => $exchangeID));
        }
        return array(
            'form' => $form
        );
    }
}
