<?php

namespace FSVendor\Octolize\Csat;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\ShowDecision\ShouldShowStrategy;
use FSVendor\WPDesk\ShowDecision\WooCommerce\ShippingMethodInstanceStrategy;
class Csat implements Hookable
{
    private CsatOption $csat_option;
    private CsatCode $csat_code;
    private ShouldShowStrategy $show_strategy;
    private string $display_on_action;
    public function __construct(CsatOption $csat_option, CsatCode $csat_code, string $display_on_action, ShouldShowStrategy $show_strategy)
    {
        $this->csat_option = $csat_option;
        $this->csat_code = $csat_code;
        $this->display_on_action = $display_on_action;
        $this->show_strategy = $show_strategy;
    }
    public function hooks()
    {
        add_action($this->display_on_action, [$this, 'display_csat']);
        if ($this->csat_option instanceof Hookable) {
            $this->csat_option->hooks();
        }
    }
    public function display_csat()
    {
        if ($this->csat_option->is_value_to_display() && $this->show_strategy->shouldDisplay()) {
            echo $this->csat_code->get_csat_code();
            // phpcs:ignore
        }
    }
    public static function create_for_shipping_method_instance(string $shipping_method_id, string $option_name, string $csat_code_file, string $display_on_action): self
    {
        return new self(new CsatOptionDependedOnShippingMethod('csat_' . $option_name, $shipping_method_id), new CsatCodeFromFile($csat_code_file), $display_on_action, new ShippingMethodInstanceStrategy(new \WC_Shipping_Zones(), $shipping_method_id));
    }
}
