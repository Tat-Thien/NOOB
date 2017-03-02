<?php

namespace RESTBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class OutgoerPreparationParticipationData
{
    public $confirmed;
    public $person;
    public $outgoerPreparation;

    private function __construct($confirmed, $person, $outgoerPreparation){
        $this->confirmed = $confirmed;
        $this->person = $person;
        $this->outgoerPreparation = $outgoerPreparation;
    }

    public static function fromEP($ep){
        return new self($ep->confirmed(), $ep->person(), $ep->outgoerPreparation());
    }
}