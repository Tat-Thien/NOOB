<?php

namespace RESTBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRESTFormType extends AbstractType
{

    protected abstract function getDataClass();

    protected function allowExtraFields() {
        return false;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->getDataClass(),
            'csrf_protection' => false,
            'allow_extra_fields' => $this->allowExtraFields()
        ));
    }

    public function getBlockPrefix()
    {
        return null;
    }

}