<?php

namespace RESTBundle\Controller;

use DateTime;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @REST\RouteResource("Exchange")
 */
class ExchangeController extends RESTBundleController
{

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @REST\QueryParam(array=true, name="updated")
     * @REST\QueryParam(name="updated[from]", description="Filter updated from")
     * @REST\QueryParam(array=true, name="created")
     * @REST\QueryParam(name="created[from]", description="Filter created from")
     * @ApiDoc(
     *  resource=true,
     *  description="Return all exchanges",
     *  output={"class"="RESTBundle\Form\ExchangeType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from('AIESECGermany\EntityBundle\Entity\Exchange', 'e');

        $updated = $paramFetcher->get('updated');
        $created = $paramFetcher->get('created');
        if(isset($updated['from'])) $qb->andWhere('e.updatedAt > :updatedFrom')
                ->setParameter('updatedFrom', DateTime::createFromFormat("!Y-m-d", $updated['from']));
        if(isset($created['from'])) $qb->andWhere('e.createdAt > :createdFrom')
            ->setParameter('createdFrom', DateTime::createFromFormat("!Y-m-d", $created['from']));

        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Return an exchange",
     *  output="RESTBundle\Form\ExchangeType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $exchangeId)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeId);
        if ($entity) {
            return $entity;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @REST\Get("/exchanges/{personID}/agbAgreement")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Return the AgbAgreement, if not signed yet returns agbAgreementData with current AGB",
     *  output="RESTBundle\Entity\AgbAgreementData"
     * )
     */
    public function getAgbAgreementAction(ParamFetcherInterface $paramFetcher, $exchangeId)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeId);
        if ($entity->getAgbSignDate()) {
            return AgbAgreementData::fromExchange($entity);
        } else {
            $agbAgreement = AgbAgreementData::fromExchange($entity);
            $agb = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findOneBy(
                array(),
                array('id'=>'DESC')
            );
            $agbAgreement->setAgb($agb);
            return $agbAgreement;
        }
    }

    /**
     * @REST\Post("/exchanges/{personID}/agbAgreement")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Sign the AGB",
     *  output="RESTBundle\Entity\AgbAgreementData"
     * )
     */
    public function postAgbAgreementAction(ParamFetcherInterface $paramFetcher, $exchangeId)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\Exchange')->findOneById($exchangeId);
        if (!$entity) {
            throw new HttpException(404);
        }
        if ($entity->getAgbSignDate()) {
            throw new BadRequestHttpException();
        }
        $agb = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findOneBy(
            array(),
            array('id'=>'DESC')
        );
        $exchange->setAgbSignDate(new \DateTime());
        $exchange->setAgb($agb);
        $em->persist($exchange);
        $em->flush();
        return $this->returnCreationResponse(AgbAgreementData::fromExchange($exchange));
    }

}
