<?php

namespace RESTBundle\Form;

use RESTBundle\Entity\ApplicationInformation;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ApplicationInformationType extends AbstractRESTFormType
{
    protected function allowExtraFields() {
        return true;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class)
            ->add('fieldOfStudy', TextType::class)
            ->add('howHeard', TextType::class)
            ->add('comments', TextType::class)
            ->add('degree', TextType::class)
            ->add('semester', IntegerType::class)
            ->add('enrolled', CheckboxType::class)
            ->add('focusOfInternship', TextType::class)
            ->add('studyCourse', TextType::class)
            ->add('graduation', TextType::class)
            ->add('practicalExperience', TextType::class)
            ->add('intendedSemesterAbroad', CheckboxType::class)
            ->add('timeframeSemesterAbroad', TextType::class)
            ->add('consecutiveSemestersPossible', CheckboxType::class)
            ->add('englishLevel', TextType::class)
            ->add('nativeLanguage', TextType::class)
            ->add('areasOfExperience', TextType::class)
            ->add('detailsAreasOfExperience', TextType::class)
            ->add('computerLiteracy', CheckboxType::class)
            ->add('detailsComputerLiteracy', TextType::class)
            ->add('applicationMotivation', TextType::class)
            ->add('linkLinkedInOrCV', TextType::class)
            ->add('competenciesToBeLearned', TextType::class)
            ->add('functionalAreaInterested', TextType::class)
            ->add('competenciesToBeImproved', TextType::class)
            ->add('hoursToBeInvested', TextType::class)
            ->add('interestInInternship', CheckboxType::class)
            ->add('timeframeInternship', TextType::class)
            ->add('university', TextType::class)
        ;
    }

    protected function getDataClass()
    {
        return ApplicationInformation::class;
    }
}
