<?php

namespace NoobBundle\Form;

use NoobBundle\Entity\ApplicationInformation;
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
			->add('type', ChoiceType::class, array(
				'choices'  => [
					'globalCitizenApplicationInformation',
					'globalTalentApplicationInformation',
					'youthTalentApplicationInformation'],
				'choices_as_values' => true))
			->add('fieldOfStudy', TextType::class, array(
				'mapped' => false
			))
			->add('howHeard', TextType::class, array(
				'mapped' => false
			))
			->add('comments', TextType::class, array(
				'mapped' => false
			))
			->add('degree', TextType::class, array(
				'mapped' => false
			))
			->add('semester', IntegerType::class, array(
				'mapped' => false))
			->add('enrolled', CheckboxType::class, array(
				'mapped' => false))
			->add('focusOfInternship', TextType::class, array(
				'mapped' => false
			))
			->add('studyCourse', TextType::class, array(
				'mapped' => false
			))
			->add('graduation', TextType::class, array(
				'mapped' => false
			))
			->add('practicalExperience', TextType::class, array(
				'mapped' => false
			))
			->add('intendedSemesterAbroad', CheckboxType::class, array(
				'mapped' => false))
			->add('timeframeSemesterAbroad', TextType::class, array(
				'mapped' => false
			))
			->add('consecutiveSemestersPossible', CheckboxType::class, array(
				'mapped' => false))
			->add('englishLevel', TextType::class, array(
				'mapped' => false
			))
			->add('nativeLanguage', TextType::class, array(
				'mapped' => false
			))
			->add('areasOfExperience', TextType::class, array(
				'mapped' => false
			))
			->add('detailsAreasOfExperience', TextType::class, array(
				'mapped' => false
			))
			->add('computerLiteracy', CheckboxType::class, array(
				'mapped' => false))
			->add('detailsComputerLiteracy', TextType::class, array(
				'mapped' => false
			))
			->add('applicationMotivation', TextType::class, array(
				'mapped' => false
			))
			->add('linkLinkedInOrCV', TextType::class, array(
				'mapped' => false
			))
			->add('competenciesToBeLearned', TextType::class, array(
				'mapped' => false
			))
			->add('functionalAreaInterested', TextType::class, array(
				'mapped' => false
			))
			->add('competenciesToBeImproved', TextType::class, array(
				'mapped' => false
			))
			->add('hoursToBeInvested', TextType::class, array(
				'mapped' => false))
			->add('interestInInternship', CheckboxType::class, array(
				'mapped' => false))
			->add('timeframeInternship', TextType::class, array(
				'mapped' => false
			))
			->add('university', TextType::class, array(
				'mapped' => false
			))
		;
	}

	protected function getDataClass()
	{
		return ApplicationInformation::class;
	}
}
