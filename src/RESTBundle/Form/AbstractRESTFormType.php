<?php

namespace RESTBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRESTFormType extends AbstractType
{

    protected abstract function getDataClass();

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->getDataClass(),
            'csrf_protection' => false
        ));
    }

    public function getBlockPrefix()
    {
        return null;
    }

}