<?php

namespace RESTBundle\Entity;


class LeadAssignment
{

    private $lc;

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

}