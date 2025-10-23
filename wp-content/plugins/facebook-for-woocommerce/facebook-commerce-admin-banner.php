<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

/**
 * Outputs WhatApp utility messaging recruitment admin banner
 *
 * @since 3.5.2
 */

/**
 * WhatsApp Admin Banner class for Facebook for WooCommerce.
 */
class WC_Facebookcommerce_Admin_Banner {
	const BANNER_ID = 'wc_facebook_admin_banner';

	public function __construct() {
		add_action(
			'admin_enqueue_scripts',
			array( $this, 'enqueue_banner_script' )
		);
		add_action(
			'wp_ajax_wc_facebook_dismiss_banner',
			array( $this, 'ajax_dismiss_banner' )
		);
	}

	/**
	 * Enqueue the admin banner script and localize data.
	 */
	public function enqueue_banner_script() {
		wp_enqueue_script(
			'whatsapp-admin-banner',
			plugins_url(
				'assets/js/admin/whatsapp-admin-banner.js',
				__FILE__
			),
			array( 'jquery' ),
			'1.0',
			true
		);
		wp_localize_script(
			'whatsapp-admin-banner',
			'WCFBAdminBanner',
			array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( self::BANNER_ID ),
				'banner_id' => self::BANNER_ID,
			)
		);
	}

	/**
	 * AJAX handler to dismiss the banner.
	 */
	public function ajax_dismiss_banner() {
		check_ajax_referer( self::BANNER_ID, 'nonce' );
		update_user_meta(
			get_current_user_id(),
			self::BANNER_ID,
			1
		);
	}

	/**
	 * Output the banner HTML if it should be shown.
	 */
	public function render_banner() {
		// Check if the WhatsApp admin banner should be shown.
		if ( strtotime( 'now' ) > strtotime( '2025-06-15 23:59:59' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( get_user_meta(
			get_current_user_id(),
			self::BANNER_ID,
			true
		) ) {
			return;
		}

		$banner_html  = '<div class="fb-wa-banner" data-nonce="' .
			esc_attr( wp_create_nonce( self::BANNER_ID ) ) .
			'" data-banner-id="' .
			esc_attr( self::BANNER_ID ) .
			'">';
		$banner_html .= '<img src="' .
			esc_url(
				plugins_url(
					'assets/images/ico-whatsapp.png',
					__FILE__
				)
			) .
			'" width="36" height="36" alt="WhatsApp Logo" />';
		$banner_html .= '<h2>Sign up to test WhatsApp’s new integration with '
			. 'WooCommerce</h2>';
		$banner_html .= '<p>We’re launching a brand new WhatsApp integration for '
			. 'WooCommerce allowing businesses to send order tracking notifications '
			. 'on WhatsApp. Sign up for a chance to join our testing program and get '
			. 'early access to this new feature. As a thank you, participants who '
			. 'complete testing will receive a $500 ad credit.</p>';
		$banner_html .= '<a class="wa-cta-button" '
			. 'href="https://facebookpso.qualtrics.com/jfe/form/SV_0SVseus9UADOhhQ"'
			. '>Sign Up</a>';
		$banner_html .= '<a class="wa-close-button" title="Close banner" '
			. 'href="#"><img src="' .
			esc_url(
				plugins_url(
					'assets/images/ico-close.svg',
					__FILE__
				)
			) .
			'" width="16" height="16" alt="Close button" /></a>';
		$banner_html .= '</div>';

		echo wp_kses_post( $banner_html );
	}
}
