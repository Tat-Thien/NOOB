<?php

namespace RESTBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReintegrationActivityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('name', TextType::class, array(
                'mapped' => false
            ))
            ->add('lc', TextType::class, array(
                'mapped' => false
            ))
            ->add('startDate', 'date', array(
                'mapped' => false,
                'widget' => 'single_text'
            ))
            ->add('endDate', 'date', array(
                'mapped' => false,
                'widget' => 'single_text'
            ))
            ->add('salesforceID', TextType::class, array(
                'mapped' => false
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RESTBundle\Entity\ReintegrationActivity',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "reintegrationActivity";
    }
}
