<?php
/**
 * WooFic
 *
 * @package   woofic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 WooFic
 * @license   MIT
 * @link      https://christiancannata.com
 */

declare(strict_types=1);

namespace WooFic\Services;


use FattureInCloud\Api\SettingsApi;
use FattureInCloud\Model\CreatePaymentMethodRequest;
use FattureInCloud\Model\PaymentMethod;
use FattureInCloud\Model\PaymentMethodType;

/**
 * Utility to show prettified wp_die errors, write debug logs as
 * string or array and to deactivate plugin and print a notice
 *
 * @package Woofic\Config
 * @since 1.0.0
 */
class WooficPaymentMethodsManager
{

    protected $config;
    protected $companyId;

    public function __construct($config, $companyId)
    {
        $this->config = $config;
        $this->companyId = $companyId;
    }


    public function createPaymentMethod($name, $slug)
    {
        $settingsApiIstance = new SettingsApi(
            null,
            $this->config
        );
        $paymentMethodToCreate = new PaymentMethod();
        $paymentMethodToCreate->setType(PaymentMethodType::STANDARD);
        $paymentMethodToCreate->setName($name);

        switch ($slug) {
            case 'paypal':
            case 'stripe':
                $paymentMethodCode = 'MP08';
                break;
            case 'bacs':
                $paymentMethodCode = 'MP05';
                break;
            case 'cheque':
                $paymentMethodCode = 'MP02';
                break;
            default:
                $paymentMethodCode = 'MP01';
                break;
        }

        $paymentMethodToCreate->setEiPaymentMethod($paymentMethodCode);

        $createPaymentMethodRequest = new CreatePaymentMethodRequest();
        $createPaymentMethodRequest->setData($paymentMethodToCreate);
        $response = $settingsApiIstance->createPaymentMethod($this->companyId, $createPaymentMethodRequest);
        $paymentMethod = $response->getData();
    }
}
