<?php

namespace NoobBundle\Form;

use EntityBundle\Entity\GlobalTalentApplicationInformation;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GlobalTalentApplicationInformationType extends AbstractRESTFormType
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
			->add('degree')
			->add('semester', IntegerType::class)
			->add('enrolled', CheckboxType::class)
			->add('focusOfInternship')
			->add('studyCourse')
			->add('graduation')
			->add('practicalExperience')
			->add('comments')
			->add('university')
		;
	}

	protected function getDataClass()
	{
		return GlobalTalentApplicationInformation::class;
	}
}
