<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\AGB;
use AIESECGermany\EntityBundle\Entity\AGBAgreement;
use AIESECGermany\EntityBundle\Entity\Exchange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class AGBAgreementType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateSigned', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('agb', EntityType::class, array(
                'class' => AGB::class
            ))
            ->add('exchange', EntityType::class, array(
                'class' => Exchange::class
            ))
            ->add('contractPdfUrl')
        ;
    }

    protected function getDataClass()
    {
        return AGBAgreement::class;
    }
}
