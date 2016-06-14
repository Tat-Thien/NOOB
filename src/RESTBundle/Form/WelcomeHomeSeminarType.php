<?php

namespace RESTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WelcomeHomeSeminarType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lc')
            ->add('startDate', 'date', array(
                'widget' => 'single_text'
            ))
            ->add('endDate', 'date', array(
                'widget' => 'single_text'
            ))
            ->add('salesforceID')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "welcomeHomeSeminar";
    }
}
