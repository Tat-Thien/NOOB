<?php

namespace NoobBundle\Form;

use EntityBundle\Entity\GlobalCitizenApplicationInformation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GlobalCitizenApplicationInformationType extends AbstractRESTFormType
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
			->add('comments')
			->add('university')
		;
	}

	protected function getDataClass()
	{
		return GlobalCitizenApplicationInformation::class;
	}
}
