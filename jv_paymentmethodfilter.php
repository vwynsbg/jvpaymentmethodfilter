<?php
/**
 * MODULE jv_paymentmethodfilter
 *
 * @author  VWYNSBG
 * @copyright   Copyright (c) 2024 VWYNSBG
 * @license Proprietary - no redistribution without authorization
 **/

declare(strict_types=1);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\Module\PaymentMethodFilter\Entity\PaymentMethodFilterConfig;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class Jv_PaymentMethodFilter extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'jv_paymentmethodfilter';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'Jeremy Vanwynsberghe';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = [
			'min' => '8.0.0',
			'max' => '8.99.99',
		];        
		$this->bootstrap = true;
		
		parent::__construct();

		$this->displayName = $this->trans('JV Payment Method Filter', [], 'Modules.Paymentmethodfilter.Admin');
		$this->description = $this->trans('Filter Payment methods based on cart amount', [], 'Modules.Paymentmethodfilter.Admin');

		$this->controllers = ['admin', 'hook'];

		$this->confirmUninstall = $this->trans('Are you sure you want to uninstall ?', [], 'Modules.Paymentmethodfilter.Admin');
	}

	public function install()
	{
		if (Shop::isFeatureActive()) {
			Shop::setContext(Shop::CONTEXT_ALL);
		}
		return parent::install() && $this->registerHook('paymentOptions') && $this->installDb();
	}

	public function installDb(): bool
	{
		// Symfony ORM handles this automatically with Doctrine migrations.
		// If you are using Doctrine, configure your migrations here.
		return true;
	}

	public function uninstall(): bool
	{
		return parent::uninstall() && $this->uninstallDb();
	}

	public function uninstallDb()
	{
		// Symfony ORM handles this automatically with Doctrine migrations.
		// Here you can manage the deletion of the database if necessary.
		return true;
	}

	public function hookPaymentOptions($params)
	{
		$cart = $this->context->cart;
		$total = $cart->getOrderTotal(true, Cart::BOTH);

		$repository = $this->get('doctrine.orm.entity_manager')->getRepository(PaymentMethodFilterConfig::class);
		$configs = $repository->findAll();

		$payment_options = [];

		foreach ($configs as $config) {
			if ($total >= $config->getMinTotal() && $total <= $config->getMaxTotal()) {
				$payment_options[] = $this->createPaymentOption($config->getPaymentOption());
			}
		}

		return $payment_options;
	}

	protected function createPaymentOption(string $option): PaymentOption
	{
		$newOption = new PaymentOption();
		$newOption->SetCallToActionText($this->l($option))
				->SetAction($this->context->link->getModuleLink($this->name, 'validation', [], true));

		return $newOption;
	}
}
