<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\Exchange;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ExchangeType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('applicationID', IntegerType::class)
            ->add('feeAmount', IntegerType::class)
            ->add('focusOfInternship')
            ->add('salesforceID')
            ->add('internshipNumber', IntegerType::class)
            ->add('matchBreak', CheckboxType::class)
        ;
    }

    protected function getDataClass()
    {
        return Exchange::class;
    }
}
