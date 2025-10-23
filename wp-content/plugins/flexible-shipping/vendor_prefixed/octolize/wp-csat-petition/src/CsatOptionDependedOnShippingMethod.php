<?php

namespace FSVendor\Octolize\Csat;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class CsatOptionDependedOnShippingMethod extends CsatOption implements Hookable
{
    private string $shipping_method_id;
    public function __construct(string $option_name, string $shipping_method_id)
    {
        parent::__construct($option_name);
        $this->shipping_method_id = $shipping_method_id;
    }
    public function hooks()
    {
        add_filter('woocommerce_shipping_' . $this->shipping_method_id . '_instance_settings_values', [$this, 'update_settings']);
    }
    public function update_settings($settings)
    {
        $this->increase();
        return $settings;
    }
}
