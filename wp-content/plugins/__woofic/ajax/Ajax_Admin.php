<?php

/**
 * WooFic
 *
 * @package   WooFic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 Christian Cannata
 * @license   GPL 2.0+
 * @link      https://christiancannata.com
 */

namespace WooFic\Ajax;

use WooFic\Engine\Base;

/**
 * AJAX as logged user
 */
class Ajax_Admin extends Base {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {
		if ( !\apply_filters( 'woofic_w_ajax_admin_initialize', true ) ) {
			return;
		}

		// For logged user
		\add_action( 'wp_ajax_your_admin_method', array( $this, 'your_admin_method' ) );
	}

	/**
	 * The method to run on ajax
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function your_admin_method() {
		$return = array(
			'message' => 'Saved',
			'ID'      => 2,
		);

		\wp_send_json_success( $return );
		// wp_send_json_error( $return );
	}

}
