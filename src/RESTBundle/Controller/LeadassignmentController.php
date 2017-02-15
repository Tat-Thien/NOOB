<?php

namespace RESTBundle\Controller;

use FOS\RestBundle\Request\ParamFetcherInterface;
use RESTBundle\Entity\LeadAssignmentData;
use Symfony\Component\DomCrawler\Crawler;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("LeadAssignmentData")
 */
class LeadassignmentController extends RESTBundleController
{

    /**
     * @REST\Get("/leadAssignment")
     * @REST\QueryParam(name="city", allowBlank=false)
     * @REST\QueryParam(name="program", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Return a lead assignment"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $program = $paramFetcher->get('program');
        $city = $paramFetcher->get('city');
        $lcMappingDocument = new \DOMDocument();
        $lcMappingDocument->loadXml(file_get_contents(__DIR__ . '/../Resources/leadassignment/lead_assignment.xml'));
        $lcMappingXpathvar = new \Domxpath($lcMappingDocument);
        $lcMappingFilterString = "//program[@name='%s']//city[@name='%s']/../@name";
        $lcMappingFilterString = sprintf($lcMappingFilterString, $program, $city);
        $lcMappingQueryResult = $lcMappingXpathvar->query($lcMappingFilterString);
        if ($lcMappingQueryResult->length == 0) {
            throw new NotFoundHttpException();
        }
        $lc = $lcMappingQueryResult->item(0)->textContent;
        $lcMappingDocument = new \DOMDocument();
        $lcMappingDocument->loadXml(file_get_contents(__DIR__ . '/../Resources/leadassignment/gis_lc_mapping.xml'));
        $lcMappingXpathvar = new \Domxpath($lcMappingDocument);
        $lcMappingFilterString = "//lc[@name='%s']/@gis-id";
        $lcMappingFilterString = sprintf($lcMappingFilterString, $lc);
        $lcMappingQueryResult = $lcMappingXpathvar->query($lcMappingFilterString);
        if ($lcMappingQueryResult->length == 0) {
            throw new NotFoundHttpException();
        }
        $gisId = $lcMappingQueryResult->item(0)->textContent;
        $result = new LeadAssignmentData();
        $result->setLc($lc);
        $result->setGisId($gisId);
        return $result;
    }

}