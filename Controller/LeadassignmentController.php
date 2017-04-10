<?php

namespace NoobBundle\Controller;

use FOS\RestBundle\Request\ParamFetcherInterface;
use NoobBundle\Entity\LeadAssignment;
use Symfony\Component\DomCrawler\Crawler;
use FOS\RestBundle\Controller\Annotations as REST;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("LeadAssignment")
 */
class LeadassignmentController extends NoobBundleController
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

		$lc = $this->get('noob.leadassignment')->getLc($program, $city);
		if($lc == null) {
			throw new NotFoundHttpException();
		}

		return $lc;
	}

}
