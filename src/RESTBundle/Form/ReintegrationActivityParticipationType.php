<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\Exchange;
use AIESECGermany\EntityBundle\Entity\ReintegrationActivity;
use AIESECGermany\EntityBundle\Entity\ReintegrationActivityParticipation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ReintegrationActivityParticipationType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirmed', CheckboxType::class)
            ->add('exchange', EntityType::class, array(
                'class' => Exchange::class
            ))
            ->add('reintegrationActivity', EntityType::class, array(
                'class' => ReintegrationActivity::class
            ))
        ;
    }

    protected function getDataClass()
    {
        return ReintegrationActivityParticipation::class;
    }
}
