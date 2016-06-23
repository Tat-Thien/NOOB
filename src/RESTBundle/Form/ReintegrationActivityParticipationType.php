<?php

namespace RESTBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReintegrationActivityParticipationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirmed', CheckboxType::class)
            ->add('exchange', EntityType::class, array(
                'class' => 'AIESECGermanyEntityBundle:Exchange'
            ))
            ->add('reintegrationActivity', EntityType::class, array(
                'class' => 'AIESECGermanyEntityBundle:ReintegrationActivity'
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AIESECGermany\EntityBundle\Entity\ReintegrationActivityParticipation',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "reintegrationActivityParticipation";
    }
}
