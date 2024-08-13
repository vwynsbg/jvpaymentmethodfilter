<?php

declare(strict_types=1);

namespace PrestaShop\Module\PaymentMethodFilter\Admin;

use PrestaShop\Module\PaymentMethodFilter\Entity\PaymentMethodFilterConfig;
use PrestaShop\Module\PaymentMethodFilter\Form\PaymentMethodFilterConfigType;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentMethodFilterController extends FrameworkBundleAdminController
{
	/**
	 * @Route("/admin/paymentmethodfilter", name="admin_paymentmethodfilter_settings")
	 */
	public function settings(Request $request, EntityManagerInterface $entityManager): Response
	{
		$configs = $entityManager->getRepository(PaymentMethodFilterConfig::class)->findAll();

		// Create a new form for a new configuration
		$config = new PaymentMethodFilterConfig();
		$form = $this->createForm(PaymentMethodFilterConfigType::class, $config);

		if ($request->isMethod('POST')) {
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()) {
				// Persist the new configuration
				$entityManager->persist($form->getData());
				$entityManager->flush();

				$this->addFlash('success', 'Settings updated successfully');

				// Redirect to avoid double submission issue
				return $this->redirectToRoute('admin_paymentmethodfilter_settings');
			}
		}

		return $this->render('@Modules/jv_paymentmethodfilter/views/templates/admin/settings.twig', [
			'form' => $form->createView(),
			'configs' => $configs,
		]);
	}
}
