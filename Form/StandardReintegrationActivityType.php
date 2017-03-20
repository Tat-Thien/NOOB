<?php

namespace NoobBundle\Form;

use EntityBundle\Entity\StandardReintegrationActivity;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StandardReintegrationActivityType extends AbstractRESTFormType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('type', TextType::class, array(
				'mapped' => false
			))
			->add('name')
		;
	}

	protected function getDataClass()
	{
		return StandardReintegrationActivity::class;
	}
}
