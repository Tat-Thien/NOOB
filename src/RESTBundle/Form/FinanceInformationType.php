<?php

namespace RESTBundle\Form;

use AIESECGermany\EntityBundle\Entity\FinanceInformation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinanceInformationType extends AbstractRESTFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exactEPAccountNumber')
            ->add('paymentMode')
            ->add('amountOfInpayment', NumberType::class)
            ->add('inpaymentBooked', CheckboxType::class)
            ->add('amountOfRaisingFee', NumberType::class)
            ->add('amountOfICCFee', NumberType::class)
            ->add('amountOfMatchingFee', NumberType::class)
            ->add('whsFee', NumberType::class)
            ->add('amountOfRefunding', NumberType::class)
            ->add('refundBooked', CheckboxType::class)
            ->add('amountOfRetention', NumberType::class)
            ->add('retentionBooked', CheckboxType::class)
            ->add('exactEpAccountInBalance', CheckboxType::class)
            ->add('finComments')
            ->add('iSOS', CheckboxType::class)
            ->add('amountOfISOSFee', NumberType::class)
            ->add('dateOfInpayment', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('raisingFeeBooked', CheckboxType::class)
            ->add('iccFeeBooked', CheckboxType::class)
            ->add('matchingFeeBooked', CheckboxType::class)
            ->add('pedsFeeBooked', CheckboxType::class)
            ->add('reasonForRefunding')
            ->add('otherReasonsForRefunding')
            ->add('reasonForRetention')
            ->add('otherReasonsForRetention')
            ->add('calculatedBalance', NumberType::class)
        ;
    }

    protected function getDataClass()
    {
        return FinanceInformation::class;
    }
}
