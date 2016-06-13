<?php

namespace RESTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StandardsAndSatisfactionType extends AbstractType
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
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AIESECGermany\EntityBundle\Entity\StandardsAndSatisfaction',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "standardsAndSatisfaction";
    }
}
