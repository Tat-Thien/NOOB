<?php

namespace NoobBundle\Form;

use EntityBundle\Entity\AGB;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class AGBType extends AbstractRESTFormType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('implementationDate', DateType::class, array(
				'widget' => 'single_text'
			))
			->add('pdfUrl')
			->add('text')
		;
	}

	protected function getDataClass()
	{
		return AGB::class;
	}
}
