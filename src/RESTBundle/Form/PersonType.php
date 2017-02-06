<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\Person;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonType extends AbstractRESTFormType
{
    protected function allowExtraFields() {
        return true;
    }
    
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
            ->add('leadSource')
            ->add('rejected', CheckboxType::class)
            ->add('gtUp', CheckboxType::class)
            ->add('newsletterPermitted', CheckboxType::class)
            ->add('salesforceID')
            ->add('opsOnlineBookingDate', DateType::class, array(
                'widget' => 'single_text'
            ))
        ;
    }

    protected function getDataClass()
    {
        return Person::class;
    }
}
