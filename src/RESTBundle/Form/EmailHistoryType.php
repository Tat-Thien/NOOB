<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\EmailHistory;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailHistoryType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstMailSent', CheckboxType::class)
            ->add('twoDaysMailSent', CheckboxType::class)
            ->add('myXPOnlineMailSent', CheckboxType::class)
            ->add('iSOSFirstMailSent', CheckboxType::class)
            ->add('iSOSSecondMailSent', CheckboxType::class)
            ->add('sAndSMailSent', CheckboxType::class)
            ->add('getResponse')
        ;
    }

    protected function getDataClass()
    {
        return EmailHistory::class;
    }
}
