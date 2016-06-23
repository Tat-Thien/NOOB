<?php

namespace RESTBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AGBAgreementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateSigned', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('agb', EntityType::class, array(
                'class' => 'AIESECGermanyEntityBundle:AGB'
            ))
            ->add('exchange', EntityType::class, array(
                'class' => 'AIESECGermanyEntityBundle:Exchange'
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AIESECGermany\EntityBundle\Entity\AGBAgreement',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "agbAgreement";
    }
}
