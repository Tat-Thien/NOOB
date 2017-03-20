<?php

namespace NoobBundle\Form;

use EntityBundle\Entity\BankAccount;
use Symfony\Component\Form\FormBuilderInterface;

class BankAccountType extends AbstractRESTFormType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('accountOwner')
			->add('iban')
			->add('bic')
			->add('bankName')
		;
	}

	protected function getDataClass()
	{
		return BankAccount::class;
	}
}
