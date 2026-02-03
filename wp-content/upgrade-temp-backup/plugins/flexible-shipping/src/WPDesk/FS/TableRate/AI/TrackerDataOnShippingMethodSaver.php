<?php

namespace WPDesk\FS\TableRate\AI;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\ShippingMethodSingle;

class TrackerDataOnShippingMethodSaver implements Hookable {

	public const FIELDS = [
		'used_rules_from_ai',
		'used_ai_chat',
		'used_rules_table_paste',
	];

	public function hooks(): void {
		add_filter(
			'woocommerce_shipping_' . ShippingMethodSingle::SHIPPING_METHOD_ID . '_instance_settings_values',
			[ $this, 'process_tracker_data' ],
			10,
			2
		);
	}

	/**
	 * @param array $settings
	 * @param \WC_Shipping_Method $shipping_method
	 *
	 * @return array
	 */
	public function process_tracker_data( $settings, $shipping_method ) {
		$data = $shipping_method->get_post_data();

		foreach ( self::FIELDS as $field ) {
			$data_field = 'woocommerce_flexible_shipping_method_' . $field;
			if ( isset( $data[ $data_field ] ) ) {
				$settings[ $field ] = $data[ $data_field ];
			}
		}

		return $settings;
	}
}
