<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\AGB;
use AIESECGermany\EntityBundle\Entity\BankAccount;
use AIESECGermany\EntityBundle\Entity\EmailHistory;
use AIESECGermany\EntityBundle\Entity\Exchange;
use AIESECGermany\EntityBundle\Entity\ExchangeAGB;
use AIESECGermany\EntityBundle\Entity\FinanceInformation;
use AIESECGermany\EntityBundle\Entity\GlobalCitizenApplicationInformation;
use AIESECGermany\EntityBundle\Entity\GlobalTalentApplicationInformation;
use AIESECGermany\EntityBundle\Entity\Person;
use AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction;
use AIESECGermany\EntityBundle\Entity\YouthTalentApplicationInformation;
use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Entity\ApplicationInformation;
use RESTBundle\Form\AGBType;
use RESTBundle\Form\ApplicationInformationType;
use RESTBundle\Form\BankAccountType;
use RESTBundle\Form\EmailHistoryType;
use RESTBundle\Form\ExchangeType;
use FOS\RestBundle\Controller\Annotations as REST;
use RESTBundle\Form\FinanceInformationType;
use RESTBundle\Form\GlobalCitizenApplicationInformationType;
use RESTBundle\Form\GlobalTalentApplicationInformationType;
use RESTBundle\Form\PersonType;
use RESTBundle\Form\StandardsAndSatisfactionType;
use RESTBundle\Form\YouthTalentApplicationInformationType;
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
    
    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Return all people",
     *  output={"class"="RESTBundle\Form\PersonType", "collection"=true}
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
     *  description="Return a person",
     *  output="RESTBundle\Form\PersonType"
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
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Create a person",
     *  input="RESTBundle\Form\PersonType",
     *  output="RESTBundle\Form\PersonType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher);
        $person = new Person();
        $form = $this->createForm(new PersonType(), $person);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            return $this->redirectWithAccessToken('get_people', array('personID' => $person->getId()),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit a person",
     *  input="RESTBundle\Form\PersonType",
     *  output="RESTBundle\Form\PersonType"
     * )
     * @REST\Patch
     */
    public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $personID)
    {
        $this->checkAuthentication($paramFetcher);
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
            return $this->redirectWithAccessToken('get_people', array('personID' => $person->getId()),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a person"
     * )
     */
    public function deleteAction(ParamFetcherInterface $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher, true);
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
     * @REST\Get("/people/{personID}/emailHistory")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get email history for a person",
     *  output={"class"="RESTBundle\Form\EmailHistoryType", "collection"=true}
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
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\Post("/people/{personID}/emailHistory")
     * @ApiDoc(
     *  resource=true,
     *  description="Create email history for a person",
     *  input="RESTBundle\Form\EmailHistoryType",
     *  output="RESTBundle\Form\EmailHistoryType"
     * )
     */
    public function postEmailhistoryAction(ParamFetcher $paramFetcher, Request $request, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $emailHistory = new EmailHistory();
        $form = $this->createForm(new EmailHistoryType(), $emailHistory);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
            if (!$person) {
                throw new HttpException(404);
            }
            $person->setEmailHistory($emailHistory);
            $em->persist($person);
            $em->persist($emailHistory);
            $em->flush();
            return $this->redirectWithAccessToken('get_people_emailhistory', array('personID' => $personID),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch("/people/{personID}/emailHistory")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit email history for a person",
     *  input="RESTBundle\Form\EmailHistoryType",
     *  output="RESTBundle\Form\EmailHistoryType"
     * )
     */
    public function patchEmailhistoryAction(ParamFetcher $paramFetcher, Request $request, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $emailHistory = $person->getEmailHistory();
        if (!$emailHistory) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new EmailHistoryType(), $emailHistory, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($emailHistory);
            $em->flush();
            return $this->redirectWithAccessToken('get_people_emailhistory', array('personID' => $personID),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Delete("/people/{personID}/emailHistory")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a email history"
     * )
     */
    public function deleteEmailhistoryAction(ParamFetcher $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $emailHistory = $person->getEmailHistory();
        if (!$emailHistory) {
            throw new NotFoundHttpException();
        }
        $em->remove($emailHistory);
        $em->flush();
        return $this->view(null, 204);
    }

    /**
     * @REST\Get("/people/{personID}/applicationInformation")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get application information for a person",
     *  output="RESTBundle\Form\ApplicationInformationType"
     * )
     */
    public function getApplicationInformationAction(ParamFetcher $paramFetcher, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $applicationInformation = $person->getApplicationInformation();
        if (!$applicationInformation) {
            throw new NotFoundHttpException();
        }
        return $applicationInformation;
    }

    /**
     * @REST\Post("/people/{personID}/applicationInformation")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Create application information",
     *  input="RESTBundle\Form\ApplicationInformationType",
     *  output="RESTBundle\Form\ApplicationInformationType"
     * )
     */
    public function postApplicationInformationAction(ParamFetcher $paramFetcher, Request $request, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $applicationInformation = new ApplicationInformation();
        $form = $this->createForm(new ApplicationInformationType(), $applicationInformation);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
            if (!$person) {
                throw new HttpException(404);
            }
            $type = $applicationInformation->getType();
            if ($type != 'globalCitizenApplicationInformation' &&
                $type != 'globalTalentApplicationInformation' &&
                $type != 'youthTalentApplicationInformation') {
                throw new BadRequestHttpException();
            }
            $informationEntityMapper =
                ['globalCitizenApplicationInformation' => new GlobalCitizenApplicationInformation(),
                'globalTalentApplicationInformation' => new GlobalTalentApplicationInformation(),
                'youthTalentApplicationInformation' => new YouthTalentApplicationInformation()];
            $informationTypeMapper =
                ['globalCitizenApplicationInformation' => new GlobalCitizenApplicationInformationType(),
                    'globalTalentApplicationInformation' => new GlobalTalentApplicationInformationType(),
                    'youthTalentApplicationInformation' => new YouthTalentApplicationInformationType()];
            $applicationInformation = $informationEntityMapper[$type];
            $applicationInformationType = $informationTypeMapper[$type];
            $form = $this->createForm($applicationInformationType, $applicationInformation);
            $form->submit($request);
            if ($form->isValid()) {
                $person->setApplicationInformation($applicationInformation);
                $em->persist($person);
                $em->persist($applicationInformation);
                $em->flush();
                return $this->redirectWithAccessToken('get_people_application_information', array('personID' => $personID),
                    $paramFetcher);
            }
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch("/people/{personID}/applicationInformation")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit application information for a person",
     *  input="RESTBundle\Form\ApplicationInformationType",
     *  output="RESTBundle\Form\ApplicationInformationType"
     * )
     */
    public function patchApplicationInformationAction(ParamFetcher $paramFetcher, Request $request, $personID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $applicationInformation = $person->getApplicationInformation();
        if (!$applicationInformation) {
            throw new NotFoundHttpException();
        }
        $informationTypeMapper =
            [GlobalCitizenApplicationInformation::class => new GlobalCitizenApplicationInformationType(),
                GlobalTalentApplicationInformation::class => new GlobalTalentApplicationInformationType(),
                YouthTalentApplicationInformation::class => new YouthTalentApplicationInformationType()];
        $class = get_class($applicationInformation);
        $informationType = $informationTypeMapper[$class];
        $form = $this->createForm($informationType, $applicationInformation, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($applicationInformation);
            $em->flush();
            return $this->redirectWithAccessToken('get_people_application_information', array('personID' => $personID),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Get("/people/{personID}/bankAccount")
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
     * @REST\Post("/people/{personID}/bankAccount")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Create a bank account",
     *  input="RESTBundle\Form\BankAccountType",
     *  output="RESTBundle\Form\BankAccountType"
     * )
     */
    public function postBankaccountAction(ParamFetcher $paramFetcher, Request $request, $personID)
    {
        $this->checkAuthentication($paramFetcher);
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
            return $this->redirectWithAccessToken('get_people_bankaccount', array('personID' => $personID),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch("/people/{personID}/bankAccount")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit bank account data for a person",
     *  input="RESTBundle\Form\BankAccountType",
     *  output="RESTBundle\Form\BankAccountType"
     * )
     */
    public function patchBankaccountAction(ParamFetcher $paramFetcher, Request $request, $personID)
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
        $form = $this->createForm(new BankAccountType(), $bankAccount, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($bankAccount);
            $em->flush();
            return $this->redirectWithAccessToken('get_people_bankaccount', array('personID' => $personID),
                $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Delete("/people/{personID}/bankAccount")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Delete a bank account"
     * )
     */
    public function deleteBankaccountAction(ParamFetcher $paramFetcher, $personID)
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
        $person->setBankAccount(null);
        $em->remove($bankAccount);
        $em->persist($person);
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
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
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
     *  description="Get all exchanges",
     *  output={"class"="RESTBundle\Form\ExchangeType", "collection"=true}
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
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Create an exchange",
     *  input="RESTBundle\Form\ExchangeType",
     *  output="RESTBundle\Form\ExchangeType"
     * )
     */
    public function postExchangesAction(ParamFetcher $paramFetcher, Request $request, $personID)
    {
        $this->checkAuthentication($paramFetcher);
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
            return $this->redirectWithAccessToken('get_people_exchanges',
                array('personID' => $personID, 'exchangeID' => $exchange->getId()), $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }


    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit an exchange",
     *  input="RESTBundle\Form\ExchangeType",
     *  output="RESTBundle\Form\ExchangeType"
     * )
     * @REST\Patch
     */
    public function patchExchangeAction(ParamFetcher $paramFetcher, Request $request, $personID, $exchangeID)
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
        $form = $this->createForm(new ExchangeType(), $exchange, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($exchange);
            $em->flush();
            return $this->redirectWithAccessToken('get_people_exchanges', array(
                'personID' => $personID, 'exchangeID' => $exchange->getId()), $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Delete an exchange"
     * )
     */
    public function deleteExchangeAction(ParamFetcher $paramFetcher, $personID, $exchangeID)
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
     * @REST\Get("/people/{personID}/exchanges/{exchangeID}/financeInformation")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get finance information for an exchange",
     *  output="RESTBundle\Form\FinanceInformationType"
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
     * @REST\Patch("/people/{personID}/exchanges/{exchangeID}/financeInformation")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit finance information for an exchange",
     *  input="RESTBundle\Form\FinanceInformationType",
     *  output="RESTBundle\Form\FinanceInformationType"
     * )
     */
    public function patchExchangesFinanceinformationAction(ParamFetcher $paramFetcher, Request $request, $personID, $exchangeID)
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
        $financeInformation = $exchange->getFinanceInformation();
        $form = $this->createForm(new FinanceInformationType(), $financeInformation, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($financeInformation);
            $em->flush();
            return $this->redirectWithAccessToken('get_people_exchanges_financeinformation',
                array('personID' => $personID, 'exchangeID' => $exchangeID), $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Get("/people/{personID}/exchanges/{exchangeID}/standardsAndSatisfaction")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get standards and satisfaction",
     *  output="RESTBundle\Form\StandardsAndSatisfactionType"
     * )
     */
    public function getExchangesStandardsandsatisfactionAction(ParamFetcherInterface $paramFetcher, $personID, $exchangeID)
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
        $sands = $exchange->getStandardsAndSatisfaction();
        if (!$sands) {
            throw new NotFoundHttpException();
        }
        return $sands;
    }

    /**
     * @REST\Post("/people/{personID}/exchanges/{exchangeID}/standardsAndSatisfaction")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Create standards and satisfaction",
     *  input="RESTBundle\Form\StandardsAndSatisfactionType",
     *  output="RESTBundle\Form\StandardsAndSatisfactionType"
     * )
     */
    public function postExchangesStandardsandsatisfactionAction(ParamFetcher $paramFetcher, Request $request, $personID, $exchangeID)
    {
        $this->checkAuthentication($paramFetcher, true);
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
            return $this->redirectWithAccessToken('get_people_exchanges_standardsandsatisfaction',
                array('personID' => $personID, 'exchangeID' => $exchangeID), $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch("/people/{personID}/exchanges/{exchangeID}/standardsAndSatisfaction")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit standards and satisfaction",
     *  input="RESTBundle\Form\StandardsAndSatisfactionType",
     *  output="RESTBundle\Form\StandardsAndSatisfactionType"
     * )
     */
    public function patchExchangesStandardsandsatisfactionAction(ParamFetcher $paramFetcher, Request $request, $personID, $exchangeID)
    {
        $this->checkAuthentication($paramFetcher, true);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        $sands = $exchange->getStandardsAndSatisfaction();
        if (!$sands) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new StandardsAndSatisfactionType(), $sands, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($sands);
            $em->flush();
            return $this->redirectWithAccessToken('get_people_exchanges_standardsandsatisfaction', array(
                'personID' => $personID, 'exchangeID' => $exchangeID), $paramFetcher);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Delete("/people/{personID}/exchanges/{exchangeID}/standardsAndSatisfaction")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Delete standards and satisfaction"
     * )
     */
    public function deleteExchangesStandardsandsatisfactionAction(ParamFetcher $paramFetcher, $personID, $exchangeID)
    {
        $this->checkAuthentication($paramFetcher, true);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($personID);
        if (!$person) {
            throw new NotFoundHttpException();
        }
        $exchange = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeID);
        if (!$exchange) {
            throw new NotFoundHttpException();
        }
        $sands = $exchange->getStandardsAndSatisfaction();
        if (!$sands) {
            throw new NotFoundHttpException();
        }
        $exchange->setStandardsAndSatisfaction(null);
        $em->remove($sands);
        $em->persist($exchange);
        $em->flush();
        return $this->view(null, 204);
    }
}
