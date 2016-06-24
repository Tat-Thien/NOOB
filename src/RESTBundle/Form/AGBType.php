<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\AGB;
use Symfony\Component\Form\FormBuilderInterface;

class AGBType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('implementationDate',  'date', array(
                'widget' => 'single_text'
            ))
            ->add('pdfUrl')
            ->add('text')
        ;
    }

    protected function getDataClass()
    {
        return AGB::class;
    }
}
