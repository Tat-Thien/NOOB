<?php

namespace RESTBundle\Controller;

use AIESECGermany\EntityBundle\Entity\JD;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use RESTBundle\Form\JdType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @REST\RouteResource("JD")
 */
class JdController extends RESTBundleController
{

	/**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @REST\QueryParam(name="ids", array=true, requirements="\d+", description="List of ids")
     * @REST\QueryParam(name="committeeIds", array=true, requirements="\d+", description="List of committee IDs")
     * @REST\QueryParam(name="newies", requirements="(true|false)", default="false", description="restrict returned JDs to Newies, meaning people that have only ever had one JD")
     * @REST\QueryParam(name="startDateFrom", description="return JDs with startDate > startDateFrom")
     * @REST\QueryParam(name="startDateTo", description="return JDs with startDate < startDateTo")
     * @REST\QueryParam(name="sort", description="Sort by a column")
     * @REST\QueryParam(name="direction", requirements="(asc|desc)", default="asc", description="Sort direction")
     * @ApiDoc(
     *  resource=true,
     *  description="Return JDs",
     *  output={"class"="RESTBundle\Form\JdType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('j')->from('AIESECGermany\EntityBundle\Entity\JD', 'j');
        $wrapQueries = false;
        
        $ids = $paramFetcher->get('ids');
        if (is_array($ids) && count($ids)) $qb->andWhere('j.id IN (:ids)')->setParameter('ids', $ids);

        $committeeIds = $paramFetcher->get('committeeIds');
        if (is_array($committeeIds) && count($committeeIds))
            $qb->andWhere('j.committeeId IN (:committeeIds)')->setParameter('committeeIds', $committeeIds);

        if($paramFetcher->get('startDateFrom')) {
            try {
                $startDateFrom = new \DateTime($paramFetcher->get('startDateFrom'));
                $qb->andWhere('j.startDate > :startDateFrom')->setParameter('startDateFrom', $startDateFrom);
            } catch (Exception $e) {}
        }

        if($paramFetcher->get('startDateTo')) {
            try {
                $startDateTo = new \DateTime($paramFetcher->get('startDateTo'));
                $qb->andWhere('j.startDate < :startDateTo')->setParameter('startDateTo', $startDateTo);
            } catch (Exception $e) {}
        }

        if($paramFetcher->get('newies') == 'true'){
            $qb->addGroupBy('j.memberId');
            $qb->having('COUNT(j.memberId) = 1');
            $wrapQueries = true;
        }

        //!!! sort is bound in pagination, just need to add the entity parameter
        $sort = $paramFetcher->get('sort');
        if ($sort) $_GET['sort'] = 'j.'.$sort;

        $query = $qb->getQuery();
        // var_export($query->getSql());
        $pagination = $this->createPaginationObject($paramFetcher, $query, $wrapQueries);
        
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Return a JD",
     *  output="RESTBundle\Form\JdType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $jdID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\JD')->findOneById($jdID);
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
     *  description="Create a JD",
     *  input="RESTBundle\Form\JdType",
     *  output="RESTBundle\Form\JdType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher);
        $jd = new JD();
        $form = $this->createForm(new JdType(), $jd);
        $form->submit($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($jd);
            $em->flush();
            return $this->returnCreationResponse($jd);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit a JD",
     *  input="RESTBundle\Form\JdType",
     *  output="RESTBundle\Form\JdType"
     * )
     */
    public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $jdID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $jd = $em->getRepository('AIESECGermany\EntityBundle\Entity\JD')->findOneById($jdID);
        if (!$jd) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new JdType(), $jd, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($jd);
            $em->flush();
            return $this->returnModificationResponse();
        }
        return array(
            'form' => $form
        );
    }

}