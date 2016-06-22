<?php

namespace RESTBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExchangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('applicationID', IntegerType::class)
            ->add('feeAmount', IntegerType::class)
            ->add('focusOfInternship')
            ->add('salesforceID')
            ->add('welcomeHomeSeminar', EntityType::class, array(
                'class' => 'AIESECGermanyEntityBundle:WelcomeHomeSeminar'
            ))
            ->add('internshipNumber', IntegerType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AIESECGermany\EntityBundle\Entity\Exchange',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "exchange";
    }
}
