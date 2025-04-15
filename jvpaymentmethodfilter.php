<?php

if (!defined('_PS_VERSION_')) {
	exit;
}

use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Jvpaymentmethodfilter extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'jvpaymentmethodfilter';
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
