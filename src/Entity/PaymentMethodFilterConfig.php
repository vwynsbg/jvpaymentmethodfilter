<?php

declare(strict_types=1);

namespace PrestaShop\Module\PaymentMethodFilter\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PaymentMethodFilter\Repository\PaymentMethodFilterConfigRepository")
 * @ORM\Table(name="ps_paymentmethodfilter_config")
 */
class PaymentMethodFilterConfig
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	 private $id;

	 /**
	  * @ORM\Column(type="string", length=255)
	  */
	 private $paymentOption;

	 /**
	  * @ORM\Column(type="decimal", scale=2)
	  */
	 private $minTotal;

	 /**
	  * @ORM\Column(type="decimal", scale=2)
	  */
	 private $maxTotal;
}