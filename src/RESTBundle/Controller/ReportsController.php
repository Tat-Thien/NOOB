<?php

namespace RESTBundle\Controller;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @REST\RouteResource("Reports")
 * TODO: use Doctrine instead of raw sql
 */
class ReportsController extends RESTBundleController
{

    /**
     * @REST\Get("/ixp/planned")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="lcId", requirements="\d+", allowBlank=true, description="ID of lc for which to display the data.")
     * @ApiDoc(
     *  description="Get a report of planned IXP"
     * )
     */
    public function getIxpPlannedAction(ParamFetcher $paramFetcher) {
        $this->checkAuthentication($paramFetcher);

        $em = $this->getDoctrine()->getManager();

        $sql = "
        SELECT DISTINCT jd.member_id as memberId, jd.committee_id as committeeId, yt.timeframe_internship as timeframeInternship
        FROM jd
        JOIN person p ON jd.member_id = p.id
        JOIN youth_talent_application_information yt ON yt.id = p.application_information_id
        ";

        $lcId = $paramFetcher->get('lcId');
        if ($lcId) $sql .= "WHERE jd.committee_id = " . $lcId;

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);

        $stmt->execute();
        $data = $stmt->fetchAll();

        if (!$data) {
            return [];
        }

        return $data;

    }


    /**
     * @REST\Get("/ixp/realized")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="lcId", requirements="\d+", allowBlank=true, description="ID of lc for which to display the data.")
     * @ApiDoc(
     *  description="Get a report of realized IXP"
     * )
     */
    public function getIxpRealizedAction(ParamFetcher $paramFetcher) {
        $this->checkAuthentication($paramFetcher);

        $em = $this->getDoctrine()->getManager();

        $sql = "
        SELECT DISTINCT e.id AS exchangeId, jd.member_id as memberId, jd.committee_id as committeeId
        FROM jd
        JOIN exchange e ON jd.member_id = e.person_id
        JOIN reintegration_activity_participation rap ON e.id = rap.exchange_id
        ";

        $lcId = $paramFetcher->get('lcId');
        if ($lcId) $sql .= "WHERE jd.committee_id = " . $lcId;

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);

        $stmt->execute();
        $data = $stmt->fetchAll();

        if (!$data) {
            return [];
        }

        return $data;

    }

    /**
     * @REST\Get("/reports/{lcId}/fobo")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  description="Get a report of FOBO for an LC."
     * )
     */
    public function getFoboAction(ParamFetcher $paramFetcher, $lcId) {
        $this->checkAuthentication($paramFetcher);

        $sql = "
        SELECT
        committee_id as committeeId,
        COUNT(CASE WHEN team = 'oGV' THEN 1 ELSE NULL END) AS oGV,
        COUNT(CASE WHEN team = 'oGT' THEN 1 ELSE NULL END) AS oGT,
        COUNT(CASE WHEN team = 'iGT' THEN 1 ELSE NULL END) AS iGT,
        COUNT(CASE WHEN team = 'REC' THEN 1 ELSE NULL END) AS REC,
        COUNT(CASE WHEN team = 'FIN' THEN 1 ELSE NULL END) AS FIN,
        COUNT(CASE WHEN team = 'TM' THEN 1 ELSE NULL END) AS TM,
        COUNT(CASE WHEN team = 'iGV' THEN 1 ELSE NULL END) AS iGV,
        COUNT(*) AS AllTeams
        FROM jd
        WHERE end_date > NOW() AND start_date < NOW()
        ";

        if($lcId != 'all'){
            $sql .= ' AND committee_id = ' . $lcId;
        } else {
            $sql .= ' GROUP BY committee_id';
        }
    
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);        

        $stmt->execute();
        $data = $stmt->fetchAll();

        if (!$data) {
            return [];
        }

        return $data;
    }


    /**
     * @REST\Get("/reports/unbookedOps")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(array=true, name="ids", requirements="\d+", allowBlank=false, description="IDs of persons for which to display the data.")
     * @ApiDoc(
     *  description="Get EPs with unbooked OP"
     * )
     */
    public function getUnbookedOpAction(ParamFetcher $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);

        $ids = $paramFetcher->get('ids');

        $whereClause = "
        WHERE fin.date_of_inpayment IS NOT NULL && (person.ops_online_booking_date IS NOT NULL || op.lc IS NOT NULL) &&
        (fin.amount_of_iccfee IS NULL || fin.icc_fee_booked = FALSE)
    ";

        if(is_array($ids) && count($ids)){
            $whereClause .= " && x.person_id in (" . join(",", $ids) . ")";
        } else {
            $whereClause .= " LIMIT 25";
        }

        return $this->getReport($whereClause);
    }

    /**
     * @REST\Get("/reports/unbookedMatches")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(array=true, name="ids", requirements="\d+", allowBlank=false, description="IDs of persons for which to display the data.")
     * @ApiDoc(
     *  description="Get EPs with unbooked Matches"
     * )
     */
    public function getUnbookedMatchesAction(ParamFetcher $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);

        $ids = $paramFetcher->get('ids');

        $whereClause = "
        WHERE fin.date_of_inpayment IS NOT NULL &&
        (fin.amount_of_matching_fee IS NULL || fin.matching_fee_booked = FALSE)
    ";

        if(is_array($ids) && count($ids)){
            $whereClause .= " && x.person_id in (" . join(",", $ids) . ")";
        } else {
            $whereClause .= " LIMIT 25";
        }

        return $this->getReport($whereClause);
    }

    /**
     * @REST\Get("/reports/unbookedReintegrations")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(array=true, name="ids", requirements="\d+", allowBlank=false, description="IDs of persons for which to display the data.")
     * @ApiDoc(
     *  description="Get EPs with unbooked Reintegrations"
     * )
     */
    public function getUnbookedReintegrationAction(ParamFetcher $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);

        $ids = $paramFetcher->get('ids');

        $whereClause = "
        WHERE fin.date_of_inpayment IS NOT NULL &&
        (fin.whs_fee IS NULL || fin.peds_fee_booked = FALSE)
    ";

        if(is_array($ids) && count($ids)){
            $whereClause .= " && x.person_id in (" . join(",", $ids) . ")";
        } else {
            $whereClause .= " LIMIT 25";
        }

        return $this->getReport($whereClause);
    }

    /**
     * @REST\Get("/reports/unbookedDeposits")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(array=true, name="ids", requirements="\d+", allowBlank=false, description="IDs of persons for which to display the data.")
     * @ApiDoc(
     *  description="Get EPs with unbooked Deposit"
     * )
     */
    public function getUnbookedDepositsAction(ParamFetcher $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);

        $ids = $paramFetcher->get('ids');

        $whereClause = "
        WHERE fin.date_of_inpayment IS NOT NULL && fin.exact_ep_account_in_balance = FALSE &&
        fin.peds_fee_booked = TRUE && fin.matching_fee_booked && icc_fee_booked
    ";

        if(is_array($ids) && count($ids)){
            $whereClause .= " && x.person_id in (" . join(",", $ids) . ")";
        } else {
            $whereClause .= " LIMIT 25";
        }

        return $this->getReport($whereClause);
    }

    /**
     * @REST\Get("/reports/auditAccounts")
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @REST\QueryParam(name="year", requirements="\d+", default="2016", allowBlank=false, description="year from which to get active accounts")
     * @ApiDoc(
     *  description="Get Audit/EP Info for a specific year"
     * )
     */
    public function getAuditAccountsAction(ParamFetcher $paramFetcher)
    {
        $this->checkAuthentication($paramFetcher);

        $year = intval($paramFetcher->get('year'));

        $whereClause = "
        WHERE fin.exact_epaccount_number LIKE '%" . $year ."%'
        || fin.date_of_inpayment LIKE '%" . $year ."%' 
        || fin.exact_ep_account_in_balance = FALSE
        || fin.updated_at LIKE '%" . $year ."%'
    ";

        return $this->getReport($whereClause);
    }


    function getReport($whereClause) {
        $sql = " 
        SELECT fin.date_of_inpayment, x.id exchangeId, x.person_id personId,
        person.ops_online_booking_date opsOnlineBookingDate,
        op.type opType, op.lc opLc, op.start_date opStartDate, op.end_date opEndDate,
        agb.implementation_date agbImplementationDate, agb.pdf_url agbUrl,
        fin.id as finId, fin.exact_epaccount_number,
        fin.amount_of_iccfee amountOfIccFee, fin.icc_fee_booked iccFeeBooked,
        fin.amount_of_matching_fee amountOfMatchingFee, fin.matching_fee_booked matchingFeeBooked,
        fin.whs_fee whsFee, fin.peds_fee_booked whsFeeBooked,  fin.whs_booking_date whsBookingDate,
        bank.account_owner bankAccountOwner, bank.iban bankIban, bank.bic bankBic, bank.bank_name bankName,
        ifnull(fin.amount_of_inpayment, 0) - ifnull(fin.amount_of_raising_fee, 0) - 
        ifnull(fin.amount_of_matching_fee, 0) - ifnull(fin.whs_fee, 0) -
        ifnull(fin.amount_of_isosfee, 0) as bankBalance
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
        LEFT JOIN bank_account bank
        ON bank.id = person.bank_account_id
        ";

        $sql .= $whereClause;

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        if (!$data) {
            return [];
        }
        return $data;
    }

}
