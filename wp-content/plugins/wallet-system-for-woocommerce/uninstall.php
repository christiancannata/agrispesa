<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete custom table after deactivation of the plugin.
global $wpdb;
$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
if ( $wpdb->get_var( 'show tables like "' . $wpdb->prefix . 'wps_wsfw_wallet_transaction"' ) == $table_name ) {
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction' );
}
$product_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
wp_delete_post( $product_id, true );
delete_option( 'wps_wsfw_rechargeable_product_id' );
delete_option( 'wps_wsfw_enable' );
delete_option( 'wsfw_enable_wallet_recharge' );
delete_option( 'wps_wsfw_allow_refund_to_wallet' );
delete_option( 'wps_wsfw_enable_email_notification_for_wallet_update' );
