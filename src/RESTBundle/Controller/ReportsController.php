<?php

namespace RESTBundle\Controller;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("Reports")
 */
class ReportsController extends RESTBundleController
{

    /**
     * @REST\Get("/reports/unbookedOp")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="person_ids", allowBlank=false, description="IDs of persons for which to display the data.")
     * @ApiDoc(
     *  description="Get EPs with unbooked OP"
     * )
     */
    public function getUnbookedOpAction(ParamFetcher $paramFetcher)
    {
        // yeeeeep ultra hässlich, das wird noch korrigiert
        $this->checkAuthentication($paramFetcher);

        $personIds = $paramFetcher->get('person_ids');

        $sql = " 
        SELECT fin.date_of_inpayment, x.id exchangeId, x.person_id personId,
        person.ops_online_booking_date opsOnlineBookingDate,
        op.type opType, op.lc opLc, op.start_date opStartDate, op.end_date opEndDate,
        agb.implementation_date agbImplementationDate, agb.pdf_url agbUrl,
        fin.amount_of_iccfee amountOfIccFee, fin.icc_fee_booked iccFeeBooked,
        fin.amount_of_matching_fee amountOfMatchingFee, fin.matching_fee_booked matchingFeeBooked,
        fin.whs_fee whsFee, fin.whs_booking_date whsBookingDate
         FROM exchange x
        INNER JOIN finance_information fin
        ON x.finance_information_id = fin.id
        INNER JOIN person
        ON x.person_id = person.id
        INNER JOIN agbagreement
        ON x.id = agbagreement.exchange_id
        INNER JOIN agb
        ON agbagreement.agb_id = agb.id
        LEFT JOIN outgoer_preparation_participation opp
        ON x.person_id = opp.person_id
        LEFT JOIN outgoer_preparation op
        ON opp.outgoer_preparation_id = op.id
        WHERE fin.date_of_inpayment IS NOT NULL && (person.ops_online_booking_date IS NOT NULL || op.lc IS NOT NULL) &&
        (fin.amount_of_iccfee IS NULL || fin.icc_fee_booked = FALSE)
    ";
        $ids = $personIds ? json_decode($personIds) : null;
        if(is_array($ids) && count($ids)){
            $sql .= " && x.person_id in (" . join(",", $ids) . ")";
        }

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        if (!$data) {
            throw new NotFoundHttpException();
        }
        return $data;
    }

}
