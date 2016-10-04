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
     * @REST\QueryParam(name="committeeId", requirements="\d+", description="Entities per page.")
     * @REST\QueryParam(name="ids", array=true, requirements="\d+", description="List of ids")
     * @REST\QueryParam(name="sort", description="Sort by a column")
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
        
        $committeeId = $paramFetcher->get('committeeId');
        if ($committeeId) $qb->andWhere('j.committee_id = :id')->setParameter('id', $committeeId);
        
        $ids = $paramFetcher->get('ids');
        if (is_array($ids) && count($ids)) $qb->andWhere('j.id IN (:ids)')->setParameter('ids', $ids);

        //!!! sort is bound in pagination, just need to add the entity parameter
        $sort = $paramFetcher->get('sort');
        if ($sort) $_GET['sort'] = 'j.'.$sort;

        $query = $qb->getQuery();
        $pagination = $this->createPaginationObject($paramFetcher, $query);
        // var_export($pagination);die();
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

}