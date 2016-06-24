<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\WelcomeHomeSeminar;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WelcomeHomeSeminarType extends AbstractRESTFormType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', TextType::class, array(
                'mapped' => false
            ))
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

    protected function getDataClass()
    {
        return WelcomeHomeSeminar::class;
    }
}
