<?php

namespace RESTBundle\Form;

use RESTBundle\Entity\SignupData;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class SignupType extends AbstractRESTFormType
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
        ->add('expaId')
        ->add('firstname')
        ->add('lastName')
        ->add('email')
        ->add('phone')
        ->add('city')
        ->add('country')
        ->add('gender')
        ->add('birthday')
        ->add('homeLc')
        ->add('homeMc')
        ->add('howHeard')
        ->add('leadSource')
        ->add('academicExperience')
        ->add('ProfessionalExperience')
        ->add('languages')
        ->add('comment')
        ->add('skills')
        ->add('intendedSemesterAbroadStartDate')
        ->add('intendedSemesterAbroadEndDate')
        ->add('besidesAiesec')
        ->add('experience')
        ->add('applicationMotivation')
        ->add('linkedin')
        ->add('skillsToBeLearned')
        ->add('functionalAreaInterestedIn')
        ->add('jdInterestedIn')
        ->add('hoursToBeInvested')
        ->add('focusOfInternship')
        ->add('earliestStartDate')
        ->add('latestEndDate')
        ->add('durationMin')
        ->add('durationMax')
        ->add('worldRegions')
        ;
    }

    protected function getDataClass()
    {
        return SignupData::class;
    }
}
