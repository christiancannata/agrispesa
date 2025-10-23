<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace WooCommerce\Facebook\Framework;

use WC_Facebookcommerce_Utils;
use WooCommerce\Facebook\Utilities\Heartbeat;

defined( 'ABSPATH' ) || exit;


/**
 * The BatchLog handler.
 *
 * @since 3.5.0
 */
class BatchLogHandler extends LogHandlerBase {

	/**
	 * Constructs a new BatchLog handler.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		add_action( Heartbeat::EVERY_5_MINUTES, array( $this, 'process_logs_batch' ) );
	}

	/**
	 * Function that runs every five minutes.
	 *
	 * @internal
	 *
	 * @since 3.5.0
	 */
	public function process_logs_batch() {

		if ( facebook_for_woocommerce()->get_integration()->is_meta_diagnosis_enabled() && get_transient( 'global_logging_message_queue' ) !== false && ! empty( get_transient( 'global_logging_message_queue' ) ) ) {
			$logs         = get_transient( 'global_logging_message_queue' );
			$chunked_logs = array_chunk( $logs, 20 );

			$chunked_failed_logs = array_map(
				function ( $logs_chunk ) {
					$logs_chunk_with_core_context = array_map(
						function ( $log ) {
							if ( empty( $log ) ) {
								return [];
							}
							return self::set_core_log_context( $log );
						},
						$logs_chunk
					);

					if ( empty( $logs_chunk_with_core_context ) ) {
						return [];
					}

					$context = [
						'event'      => 'persist_meta_logs',
						'extra_data' => [ 'meta_logs' => wp_json_encode( $logs_chunk_with_core_context ) ],
					];

					try {
						$response = facebook_for_woocommerce()->get_api()->log_to_meta( $context );
						if ( $response->success ) {
							return [];
						} else {
							Logger::log(
								'Bad response from Meta logging APIs',
								[],
								array(
									'should_send_log_to_meta' => false,
									'should_save_log_in_woocommerce' => true,
									'woocommerce_log_level'   => \WC_Log_Levels::ERROR,
								)
							);
							return $logs_chunk;
						}
					} catch ( \Exception $e ) {
						Logger::log(
							'Error persisting Meta logs: ' . $e->getMessage(),
							[],
							array(
								'should_send_log_to_meta' => false,
								'should_save_log_in_woocommerce' => true,
								'woocommerce_log_level'   => \WC_Log_Levels::ERROR,
							)
						);
						return $logs_chunk;
					}
				},
				$chunked_logs
			);

			$failed_logs = array_merge( ...$chunked_failed_logs );
			// Only keep the latest 100 failed logs, in case too much memory got eaten up on the host
			if ( count( $failed_logs ) > 100 ) {
				$failed_logs = array_slice( $failed_logs, -100 );
			}

			if ( ! empty( $failed_logs ) ) {
				set_transient( 'global_logging_message_queue', $failed_logs, HOUR_IN_SECONDS );
				return;
			}
		}

		set_transient( 'global_logging_message_queue', [], HOUR_IN_SECONDS );
	}
}
