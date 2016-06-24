<?php

namespace RESTBundle\Form;

use RESTBundle\Entity\ReintegrationActivity;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReintegrationActivityType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, array(
                'choices'  => ['welcomeHomeSeminar', 'standardReintegrationActivity'],
                'choices_as_values' => true))
            ->add('name', TextType::class, array(
                'mapped' => false
            ))
            ->add('lc', TextType::class, array(
                'mapped' => false
            ))
            ->add('startDate', DateType::class, array(
                'mapped' => false,
                'widget' => 'single_text'
            ))
            ->add('endDate', DateType::class, array(
                'mapped' => false,
                'widget' => 'single_text'
            ))
            ->add('salesforceID', TextType::class, array(
                'mapped' => false
            ))
        ;
    }

    protected function getDataClass()
    {
        return ReintegrationActivity::class;
    }
}
