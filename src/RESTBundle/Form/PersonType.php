<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\Person;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonType extends AbstractRESTFormType
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
            ->add('rejected', CheckboxType::class)
            ->add('gtUp', CheckboxType::class)
        ;
    }

    protected function getDataClass()
    {
        return Person::class;
    }
}
