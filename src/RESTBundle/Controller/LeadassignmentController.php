<?php

namespace RESTBundle\Controller;

use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Entity\LeadAssignment;
use Symfony\Component\DomCrawler\Crawler;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("LeadAssignment")
 */
class LeadassignmentController extends RESTBundleController
{

    /**
     * @REST\Get("/leadAssignment")
     * @REST\QueryParam(name="university", allowBlank=false)
     * @REST\QueryParam(name="program", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Return a lead assignment"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        //$this->checkAuthentication($paramFetcher);
        $program = $paramFetcher->get('program');
        $university = $paramFetcher->get('university');
        $document = new \DOMDocument();
        $document->loadXml(file_get_contents(__DIR__ . '/../Resources/leadassignment/lead_assignment.xml'));
        $xpathvar = new \Domxpath($document);
        $filterString = "//program[@name='%s']//university[@name='%s']/../@name";
        $filterString = sprintf($filterString, $program, $university);
        $queryResult = $xpathvar->query($filterString);
        if ($queryResult->length == 0) {
            throw new NotFoundHttpException();
        }
        $lc = $queryResult->item(0)->textContent;
        $result = new LeadAssignment();
        $result->setLc($lc);
        return $result;
    }

}