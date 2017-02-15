<?php

namespace RESTBundle\Entity;

use AIESECGermany\EntityBundle\Entity\IAGB;
use AIESECGermany\EntityBundle\Entity\IExchange;

class AGBAgreementData
{

	private $dateSigned;

	private $agb;

	private $exchange;

    public function __construct($arg) {
        if($arg instanceof IAGB){
            $this->agb = $arg;
        } else if( $arg instanceof IExchange){
            $this->dateSigned = $arg->getAgbSignDate();
            $this->agb = $arg->getAgb();
            $this->exchange = $arg;
        } else {
            throw new \InvalidArgumentException('Must provide IAGB or IExchange');
        }
        
    }

    /**
     * @return mixed
     */
    public function getDateSigned()
    {
        return $this->dateSigned;
    }

    /**
     * @param mixed $dateSigned
     */
    public function setDateSigned($dateSigned)
    {
        $this->dateSigned = $dateSigned;
    }

    /**
     * @return mixed
     */
    public function getAgb()
    {
        return $this->agb;
    }

    /**
     * @param mixed $agb
     */
    public function setAgb($agb)
    {
        $this->agb = $agb;
    }

    /**
     * @return mixed
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param mixed $exchange
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
    }

}