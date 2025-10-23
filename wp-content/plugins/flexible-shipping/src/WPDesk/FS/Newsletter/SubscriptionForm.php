<?php

namespace WPDesk\FS\Newsletter;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

class SubscriptionForm implements Hookable {

	public function hooks(): void {
		add_action( 'admin_footer', [ $this, 'add_newsletter_form' ] );
	}

	public function add_newsletter_form() {
		if ( isset( $_GET['page'], $_GET['tab'], $_GET['section'] ) && $_GET['page'] === 'wc-settings' && $_GET['tab'] === 'shipping' && $_GET['section'] === 'flexible_shipping_info' ) {
			$email = wp_get_current_user()->user_email ?? '';
			require_once __DIR__ . '/views/newsletter-form.php';
		}
	}

}
