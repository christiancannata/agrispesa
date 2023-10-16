<?php

/**
 * Abstract converter.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers
 */
namespace FSVendor\WPDesk\WooCommerce\CurrencySwitchers;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
/**
 * Abstract class for converters.
 */
abstract class AbstractConverter implements \FSVendor\WPDesk\WooCommerce\CurrencySwitchers\SwitcherConverter, \Psr\Log\LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @inheritDoc
     */
    abstract function convert($value);
    /**
     * @inheritDoc
     */
    public function convert_array($values)
    {
        foreach ($values as $key => $value) {
            if ($value) {
                $values[$key] = $this->convert($value);
            }
        }
        return $values;
    }
}
