<?php

declare(strict_types=1);

namespace PrestaShop\Module\PaymentMethodFilter\Form;

use PrestaShop\Module\PaymentMethodFilter\Entity\PaymentMethodFilterConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentMethodFilterConfigType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('paymentOption', TextType::class, [
			'label' => 'Payment Option',
		])
		->add('minTotal', NumberType::class, [
			'label' => 'Min Total',
			'scale' => 2,
		])
		->add('maxTotal', NumberType::class, [
			'label' => 'Max Total',
			'scale' => 2,
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => PaymentMethodFilterConfig::class,
		]);
	}
}