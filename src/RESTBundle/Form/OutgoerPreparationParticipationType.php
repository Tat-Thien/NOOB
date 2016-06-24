<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\OutgoerPreparation;
use AIESECGermany\EntityBundle\Entity\OutgoerPreparationParticipation;
use AIESECGermany\EntityBundle\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class OutgoerPreparationParticipationType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirmed', CheckboxType::class)
            ->add('person', EntityType::class, array(
                'class' => Person::class
            ))
            ->add('outgoerPreparation', EntityType::class, array(
                'class' => OutgoerPreparation::class
            ))
        ;
    }

    protected function getDataClass()
    {
        return OutgoerPreparationParticipation::class;
    }
}
