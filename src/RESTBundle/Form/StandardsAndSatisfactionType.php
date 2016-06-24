<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StandardsAndSatisfactionType extends AbstractRESTFormType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visaWorkPermit', CheckboxType::class)
            ->add('arrivalPickup', CheckboxType::class)
            ->add('departureSupport', CheckboxType::class)
            ->add('jobDescription', CheckboxType::class)
            ->add('duration', CheckboxType::class)
            ->add('workingHours', CheckboxType::class)
            ->add('firstDayOfWork', CheckboxType::class)
            ->add('individualGoals', CheckboxType::class)
            ->add('insurance', CheckboxType::class)
            ->add('accomodation', CheckboxType::class)
            ->add('livingCosts', CheckboxType::class)
            ->add('purpose', CheckboxType::class)
            ->add('expectationSetting', CheckboxType::class)
            ->add('preparation', CheckboxType::class)
            ->add('hostFacilitatedLearning', CheckboxType::class)
            ->add('homeFacilitatedLearning', CheckboxType::class)
            ->add('comments', TextType::class)
        ;
    }

    protected function getDataClass()
    {
        return StandardsAndSatisfaction::class;
    }
}
