<?php

namespace RESTBundle\Entity;


class LeadAssignmentData
{

    private $lc;

    private $gisId;

    /**
     * @return mixed
     */
    public function getLc()
    {
        return $this->lc;
    }

    /**
     * @param mixed $lc
     */
    public function setLc($lc)
    {
        $this->lc = $lc;
    }

    /**
     * @return mixed
     */
    public function getGisId()
    {
        return $this->gisId;
    }

    /**
     * @param mixed $gisId
     */
    public function setGisId($gisId)
    {
        $this->gisId = $gisId;
    }

}