<?php

namespace RESTBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class AgbAgreementData
{
    public $agb;

    public $agbSignDate;

    public $contractPdfUrl;

    private function __construct($agb, $agbSignDate, $contractPdfUrl){
        $this->agb = $agb;
        $this->agbSignDate = $agbSignDate;
        $this->contractPdfUrl = $contractPdfUrl;
    }

    public static function fromExchange($exchange){
        return new self($exchange->getAgb(), $exchange->getAgbSignDate(), $exchange->getContractPdfUrl());
    }
}