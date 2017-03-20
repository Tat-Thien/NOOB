<?php

namespace NoobBundle\Form;

use EntityBundle\Entity\YouthTalentApplicationInformation;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class YouthTalentApplicationInformationType extends AbstractRESTFormType
{
	protected function allowExtraFields() {
		return true;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('type', TextType::class, array(
				'mapped' => false
			))
			->add('fieldOfStudy')
			->add('howHeard')
			->add('semester', IntegerType::class)
			->add('enrolled', CheckboxType::class)
			->add('graduation')
			->add('intendedSemesterAbroad', CheckboxType::class)
			->add('timeframeSemesterAbroad')
			->add('consecutiveSemestersPossible', CheckboxType::class)
			->add('englishLevel')
			->add('nativeLanguage')
			->add('practicalExperience')
			->add('besidesAIESEC')
			->add('areasOfExperience')
			->add('detailsAreasOfExperience')
			->add('computerLiteracy', CheckboxType::class)
			->add('detailsComputerLiteracy')
			->add('applicationMotivation')
			->add('linkLinkedInOrCV')
			->add('competenciesToBeLearned')
			->add('functionalAreaInterested')
			->add('concretePosition')
			->add('competenciesToBeImproved')
			->add('hoursToBeInvested')
			->add('interestInInternship', CheckboxType::class)
			->add('timeframeInternship')
			->add('comments')
			->add('university')
		;
	}

	protected function getDataClass()
	{
		return YouthTalentApplicationInformation::class;
	}
}
