<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\AGB;
use AIESECGermany\EntityBundle\Entity\BankAccount;
use AIESECGermany\EntityBundle\Entity\EmailHistory;
use AIESECGermany\EntityBundle\Entity\Exchange;
use AIESECGermany\EntityBundle\Entity\ExchangeAGB;
use AIESECGermany\EntityBundle\Entity\FinanceInformation;
use AIESECGermany\EntityBundle\Entity\Person;
use AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction;
use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\AGBType;
use RESTBundle\Form\BankAccountType;
use RESTBundle\Form\EmailHistoryType;
use RESTBundle\Form\ExchangeAGBType;
use RESTBundle\Form\ExchangeType;
use FOS\RestBundle\Controller\Annotations as REST;
use RESTBundle\Form\FinanceInformationType;
use RESTBundle\Form\PersonType;
use RESTBundle\Form\StandardsAndSatisfactionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @REST\RouteResource("People")
 */
class PeopleController extends RESTBundleController
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
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')->from('AIESECGermany\EntityBundle\Entity\Person', 'p');
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
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
            $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($person->getId());
            $emailHistory = new EmailHistory();
            $person->setEmailHistory($emailHistory);
            $em->persist($person);
            $em->persist($emailHistory);
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
        if (!$person) {
            throw new NotFoundHttpException();
        }
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
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a person"
     * )
     */
    public function deleteAction($personID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $em->remove($person);
        $em->flush();
        return $this->view(null, 204);
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get email history for a person"
     * )
     */
    public function getEmailhistoryAction(ParamFetcher $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        return $person->getEmailHistory();
    }


    /**
     * @REST\Patch
     * @ApiDoc(
     *  resource=true,
     *  description="Edit email history for a person",
     *  input="RESTBundle\Form\EmailHistoryType",
     *  output="RESTBundle\Form\EmailHistoryType"
     * )
     */
    public function patchEmailhistoryAction(Request $request, $personID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $emailHistory = $person->getEmailHistory();
        $form = $this->createForm(new EmailHistoryType(), $emailHistory, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($emailHistory);
            $em->flush();
            return $this->routeRedirectView('get_people_emailhistory', array('personID' => $personID));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get bank account data for a person",
     *  output="RESTBundle\Form\BankAccountType"
     * )
     */
    public function getBankaccountAction(ParamFetcher $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $bankAccount = $person->getBankAccount();
        if (!$bankAccount) {
            throw new NotFoundHttpException();
        }
        return $bankAccount;
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create a bank account",
     *  input="RESTBundle\Form\BankAccountType",
     *  output="RESTBundle\Form\BankAccountType"
     * )
     */
    public function postBankaccountAction(Request $request, $personID)
    {
        $bankAccount = new BankAccount();
        $form = $this->createForm(new BankAccountType(), $bankAccount);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
            if (!$person) {
                throw new HttpException(404);
            }
            $person->setBankAccount($bankAccount);
            $em->persist($person);
            $em->persist($bankAccount);
            $em->flush();
            return $this->routeRedirectView('get_people_bankaccount', array('personID' => $personID));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch
     * @ApiDoc(
     *  resource=true,
     *  description="Edit bank account data for a person",
     *  input="RESTBundle\Form\BankAccountType",
     *  output="RESTBundle\Form\BankAccountType"
     * )
     */
    public function patchBankaccountAction(Request $request, $personID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $bankAccount = $person->getBankAccount();
        if (!$bankAccount) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new BankAccountType(), $bankAccount, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($bankAccount);
            $em->flush();
            return $this->routeRedirectView('get_people_bankaccount', array('personID' => $personID));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a bank account"
     * )
     */
    public function deleteBankaccountAction($personID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $bankAccount = $person->getBankAccount();
        if (!$bankAccount) {
            throw new NotFoundHttpException();
        }
        $em->remove($bankAccount);
        $em->flush();
        return $this->view(null, 204);
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
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        return $exchange;
    }
    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="salesforceID", description="Salesforce ID")
     * @REST\QueryParam(name="applicationID", description="Application ID")
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all exchanges"
     * )
     */
    public function cgetExchangesAction(ParamFetcher $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from('AIESECGermany\EntityBundle\Entity\Exchange', 'e')->where('e.person = ?1')
            ->setParameter(1, $personID);
        $salesforceID = $paramFetcher->get('salesforceID');
        if ($salesforceID) {
            $qb->andWhere('e.salesforceID = ?2')->setParameter(2, $salesforceID);
        }
        $applicationID = $paramFetcher->get('applicationID');
        if ($applicationID) {
            $qb->andWhere('e.applicationID = ?3')->setParameter(3, $applicationID);
        }
        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
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
            $financeInformation = new FinanceInformation();
            $exchange->setFinanceInformation($financeInformation);
            $person->addExchange($exchange);
            $exchange->setPerson($person);
            $em->persist($person);
            $em->persist($financeInformation);
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
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
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
     * @ApiDoc(
     *  resource=true,
     *  description="Delete an exchange"
     * )
     */
    public function deleteExchangeAction($personID, $exchangeID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        $em->remove($exchange);
        $em->flush();
        return $this->view(null, 204);
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get finance information for an exchange"
     * )
     */
    public function getExchangesFinanceinformationAction(ParamFetcher $paramFetcher, $personID, $exchangeID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        return $exchange->getFinanceInformation();
    }


    /**
     * @REST\Patch
     * @ApiDoc(
     *  resource=true,
     *  description="Edit finance information for an exchange",
     *  input="RESTBundle\Form\FinanceInformationType"
     * )
     */
    public function patchExchangesFinanceinformationAction(Request $request, $personID, $exchangeID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        $financeInformation = $exchange->getFinanceInformation();
        $form = $this->createForm(new FinanceInformationType(), $financeInformation, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($financeInformation);
            $em->flush();
            return $this->routeRedirectView('get_people_exchanges_financeinformation',
                array('personID' => $personID, 'exchangeID' => $exchangeID));
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
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        $agb = $exchange->getExchangeAgb();
        if (!$agb) {
            throw new NotFoundHttpException();
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
                throw new NotFoundHttpException();
            }
            $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
            if (!$exchange) {
                throw new NotFoundHttpException();
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
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        $exchangeAGB = $exchange->getExchangeAgb();
        if (!$exchangeAGB) {
            throw new NotFoundHttpException();
        }
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


    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a signed AGB"
     * )
     */
    public function deleteExchangesAgbAction($personID, $exchangeID)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        $exchangeAGB = $exchange->getExchangeAgb();
        if (!$exchangeAGB) {
            throw new NotFoundHttpException();
        }
        $em->remove($exchangeAGB);
        $em->flush();
        return $this->view(null, 204);
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
