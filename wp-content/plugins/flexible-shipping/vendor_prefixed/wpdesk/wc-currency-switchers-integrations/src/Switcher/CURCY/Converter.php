<?php

/**
 * Currency converter.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers\Switcher\CURCY
 */
namespace FSVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\CURCY;

use FSVendor\WPDesk\WooCommerce\CurrencySwitchers\AbstractConverter;
/**
 * Can convert currency using CURCY – Multi Currency for WooCommerce plugin.
 * @see https://wordpress.org/plugins/woo-multi-currency/
 */
class Converter extends \FSVendor\WPDesk\WooCommerce\CurrencySwitchers\AbstractConverter
{
    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        try {
            return wmc_get_price($value);
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->debug($e->getMessage());
            }
        }
        return $value;
    }
}
