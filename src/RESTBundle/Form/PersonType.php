<?php

namespace RESTBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, array(
                'required' => true
            ))
            ->add('email', EmailType::class, array(
                'required' => true
            ))
            ->add('opsOnline', CheckboxType::class)
            ->add('leadSource')
            ->add('outgoerPreparation', EntityType::class, array(
                'class' => 'AIESECGermanyEntityBundle:OutgoerPreparation'
            ))
            ->add('rejected', CheckboxType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AIESECGermany\EntityBundle\Entity\Person',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "person";
    }
}
