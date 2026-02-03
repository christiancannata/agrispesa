<?php

if ( ! current_user_can( 'activate_plugins' ) ) {
	return; 
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'trustpilot_settings' );
delete_option( 'trustpilot_past_orders' );
delete_option( 'trustpilot_failed_orders' );
delete_option( 'trustpilot_plugin_status' );
delete_option( 'show_past_orders_initial' );
delete_option( 'sync_in_progress' );
delete_option( 'trustpilot_page_urls' );
delete_option( 'trustpilot_custom_TrustBoxes' );
delete_option( 'past_orders' );
delete_option( 'failed_orders' );
