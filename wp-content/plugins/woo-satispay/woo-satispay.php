<?php
/*
 * Plugin Name: WooCommerce Satispay
 * Plugin URI: https://wordpress.org/plugins/woo-satispay/
 * Description: Save time and money by accepting payments from your customers with Satispay. Free, simple, secure! #doitsmart
 * Author: Satispay
 * Author URI: https://www.satispay.com/
 * Version: 2.1.5
 * WC requires at least: 3.4.0
 * WC tested up to: 6.1
 */
add_action('plugins_loaded', 'wc_satispay_init', 0);
add_filter('cron_schedules', 'wc_satispay_cron_schedule');
function wc_satispay_init() {
	if (!class_exists('WC_Payment_Gateway')) return;

	include_once('wc-satispay.php');

	add_filter('woocommerce_payment_gateways', 'wc_satispay_add_gateway');

	function wc_satispay_add_gateway($methods) {
		$methods[] = 'WC_Satispay';
		return $methods;
	}

	add_filter('plugin_action_links_'.plugin_basename( __FILE__ ), 'wc_satispay_action_links');
	function wc_satispay_action_links($links) {
		$pluginLinks = array(
			'<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout&section=satispay').'">'.__('Settings', 'woo-satispay').'</a>'
		);
		return array_merge($pluginLinks, $links);
	}
    add_action('wc_satispay_finalize_orders_event', 'wc_satispay_finalize_orders');
}

/**
 * Add more cron schedules.
 *
 * @param array $schedules List of WP scheduled cron jobs.
 *
 * @return array
 */
function wc_satispay_cron_schedule($schedules) {
    $schedules['every_four_hours'] = array(
        'interval' => 14400, // Every 4 hours
        'display'  => __( 'Every 4 hours' ),
    );
    return $schedules;
}

function wc_satispay_activate()
{
    if ( !wp_next_scheduled( 'wc_satispay_finalize_orders_event' ) ) {
        wp_schedule_event(time(), 'every_four_hours', 'wc_satispay_finalize_orders_event'); // wc_satispay_finalize_orders_event is a hook
    }
}
register_activation_hook( __FILE__, 'wc_satispay_activate');

function wc_satispay_deactivate()
{
    wp_clear_scheduled_hook('wc_satispay_finalize_orders_event');
}
register_deactivation_hook( __FILE__ , 'wc_satispay_deactivate');
