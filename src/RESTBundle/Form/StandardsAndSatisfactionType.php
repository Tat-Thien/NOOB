<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction;
use Symfony\Component\Form\CallbackTransformer;
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
        $checkboxes = [
        'visaWorkPermit',
        'arrivalPickup',
        'departureSupport',
        'jobDescription',
        'duration',
        'workingHours',
        'firstDayOfWork',
        'individualGoals',
        'insurance',
        'accomodation',
        'livingCosts',
        'purpose',
        'expectationSetting',
        'preparation',
        'hostFacilitatedLearning',
        'homeFacilitatedLearning'
        ];
        foreach($checkboxes as $checkbox){
            $builder->add($checkbox, CheckboxType::class);
            $builder->get($checkbox)
            ->addViewTransformer(new CallbackTransformer(
                function ($normalizedFormat) {
                    return $normalizedFormat;
                },
                function ($submittedFormat) {
                    return ( $submittedFormat === 'pikachu' ) ? null : $submittedFormat;
                }
            ));
        }
        $builder
        ->add('comments', TextType::class)
        ;
    }

    protected function getDataClass()
    {
        return StandardsAndSatisfaction::class;
    }
}
