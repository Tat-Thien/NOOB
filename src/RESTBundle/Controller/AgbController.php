<?php

namespace RESTBundle\Controller;
use AIESECGermany\EntityBundle\Entity\AGB;
use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Form\AGBType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @REST\RouteResource("Agb")
 */
class AgbController extends RESTBundleController
{

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @REST\QueryParam(name="limit", requirements="\d+", default="10", description="Entities per page.")
     * @ApiDoc(
     *  resource=true,
     *  description="Get all AGB data",
     *  output={"class"="RESTBundle\Form\AGBType", "collection"=true}
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a')->from('AIESECGermany\EntityBundle\Entity\AGB', 'a');
        $pagination = $this->createPaginationObject($paramFetcher, $qb->getQuery());
        return $pagination;
    }

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Get AGB data",
     *  output="RESTBundle\Form\AGBType"
     * )
     */
    public function getAction(ParamFetcherInterface $paramFetcher, $agbID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();

        if($agbID == "latest"){
            $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findOneBy(
                array(),
                array('id' => 'ASC')
            );
        } else {
            $entity = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findOneById($agbID);    
        }
        
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
     *  description="Create an AGB",
     *  input="RESTBundle\Form\AGBType",
     *  output="RESTBundle\Form\AGBType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher, true);
        $agb = new AGB();
        $form = $this->createForm(new AGBType(), $agb);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($agb);
            $em->flush();
            return $this->returnCreationResponse($agb);
        }
        return array(
            'form' => $form
        );
    }

    /**
     * @REST\Patch
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Edit an AGB",
     *  input="RESTBundle\Form\AGBType",
     *  output="RESTBundle\Form\AGBType"
     * )
     */
    public function patchAction(ParamFetcherInterface $paramFetcher, Request $request, $agbID)
    {
        $this->checkAuthentication($paramFetcher, true);
        $em = $this->getDoctrine()->getManager();
        $agb = $em->getRepository('AIESECGermany\EntityBundle\Entity\AGB')->findOneById($agbID);
        if (!$agb) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new AGBType(), $agb, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->merge($agb);
            $em->flush();
            return $this->returnModificationResponse();
        }
        return array(
            'form' => $form
        );
    }
}