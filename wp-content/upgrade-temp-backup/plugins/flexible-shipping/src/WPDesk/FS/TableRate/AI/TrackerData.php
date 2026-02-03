<?php

namespace WPDesk\FS\TableRate\AI;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can append rule data to tracker data.
 */
class TrackerData implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks(): void {
		add_filter( 'flexible-shipping/tracker/method-settings', [ $this, 'append_ai_data' ], 10, 2 );
	}

	/**
	 * @param array $data
	 * @param array $instance_settings
	 *
	 * @return array
	 */
	public function append_ai_data( $data, array $instance_settings ) {
		if ( ! is_array( $data ) || ! is_array( $instance_settings ) ) {
			return $data;
		}

		$data['rules_table_ai'] = $this->append_data( $data['rules_table_ai'] ?? [], $instance_settings );

		return $data;
	}

	/**
	 * @param $data
	 * @param $instance_settings
	 *
	 * @return array
	 */
	private function append_data( $data, $instance_settings ) {
		foreach ( TrackerDataOnShippingMethodSaver::FIELDS as $field ) {
			if ( ! isset( $data[ $field ] ) ) {
				$data[ $field ] = 0;
			}
			if ( isset( $instance_settings[ $field ] ) ) {
				++$data[ $field ];
			}
		}

		return $data;
	}
}
