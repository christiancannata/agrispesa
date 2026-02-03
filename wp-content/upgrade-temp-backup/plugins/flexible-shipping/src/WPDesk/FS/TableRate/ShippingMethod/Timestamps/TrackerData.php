<?php

namespace WPDesk\FS\TableRate\ShippingMethod\Timestamps;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

class TrackerData implements Hookable {

	public function hooks(): void {
		add_filter( 'flexible-shipping/tracker/method-settings', [ $this, 'append_time_data_to_tracker' ], 10, 2 );
	}

	/**
	 * @param array $data
	 * @param array $instance_settings
	 *
	 * @return array
	 */
	public function append_time_data_to_tracker( $data, $instance_settings ) {
		if ( ! is_array( $data ) || ! is_array( $instance_settings ) ) {
			return $data;
		}

		if ( isset( $instance_settings[ MethodTimestamps::CREATION_TIME_WITH_FREE ] ) && isset( $instance_settings[ MethodTimestamps::METHOD_RULES_UPDATE_TIME_WITH_FREE ] ) ) {
			$time                                 = $instance_settings[ MethodTimestamps::METHOD_RULES_UPDATE_TIME_WITH_FREE ] - $instance_settings[ MethodTimestamps::CREATION_TIME_WITH_FREE ];
			$data['rules_table_config_time_free'] = $this->append_time_data( $data['rules_table_configuration_time'] ?? [], $time );
		}

		return $data;
	}

	private function append_time_data( array $data, int $time ): array {
		$data['methods_count'] = ( $data['methods_count'] ?? 0 ) + 1;
		$data['min_time']      = min( $data['min_time'] ?? $time, $time );
		$data['max_time']      = max( $data['max_time'] ?? $time, $time );
		$data['avg_time']      = ( (float) ( ( $data['avg_time'] ?? 0.0 ) + $time ) ) / $data['methods_count'];

		return $data;
	}
}
