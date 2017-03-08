<?php

namespace RESTBundle\Controller;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @REST\RouteResource("TN")
 */
class TNController extends RESTBundleController
{

    /**
     * @REST\Post("/tn/{tnID}/manager")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="manager", requirements="\d+", description="ID of the Manager to add.", allowBlank=false)
     * @ApiDoc(
     *  description="Adds a manager for this TN"
     * )
     */
    public function postManagerAction(ParamFetcherInterface $paramFetcher, $tnID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $tn = $em->getRepository('AIESECGermany\EntityBundle\Entity\TN')->findOneById($tnID);
        $managerId = $paramFetcher->get('manager');
        $manager = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($managerId);
        if(!$tn || !$manager) {
            throw new HttpException(404);
        }
        $manager->addManagedOpportunity($tn);
        $em->persist($manager);
        $em->flush();
        return $this->returnCreationResponse();
    }

    /**
     * @REST\Delete("/tn/{tnID}/manager")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="manager", requirements="\d+", description="ID of the Manager to add.", allowBlank=false)
     * @ApiDoc(
     *  description="Removes a manager for this TN"
     * )
     */
    public function deleteManagerAction(ParamFetcherInterface $paramFetcher, $tnID)
    {
        $this->checkAuthentication($paramFetcher);
        $em = $this->getDoctrine()->getManager();
        $tn = $em->getRepository('AIESECGermany\EntityBundle\Entity\TN')->findOneById($tnID);
        $managerId = $paramFetcher->get('manager');
        $manager = $em->getRepository('AIESECGermany\EntityBundle\Entity\Person')->findOneById($managerId);
        if(!$tn || !$manager) {
            throw new HttpException(404);
        }
        $manager->removeManagedOpportunity($tn);
        $em->persist($manager);
        $em->flush();
        return $this->returnDeletionResponse();
    }

}
