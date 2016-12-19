<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\JD;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class JdType extends AbstractRESTFormType
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
            ->add('memberId', IntegerType::class, array(
                'required' => true
            ))
            ->add('committeeId', IntegerType::class, array(
                'required' => true
            ))
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text',
                'required' => true
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text',
                'required' => true
            ))
            ->add('kpi', CollectionType::class, array(
                'entry_type'   => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('mos', CollectionType::class, array(
                'entry_type'   => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('reasonForLeaving')
            ->add('team')
        ;
    }

    protected function getDataClass()
    {
        return JD::class;
    }
}
