<?php
/*
 * Plugin Name: WooCommerce Satispay
 * Plugin URI: https://wordpress.org/plugins/woo-satispay/
 * Description: Save time and money by accepting payments from your customers with Satispay. Free, simple, secure! #doitsmart
 * Author: Satispay
 * Author URI: https://www.satispay.com/
 * Version: 2.2.4
 * WC tested up to: 8.9.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );
add_action('plugins_loaded', 'wc_satispay_init', 0);
add_filter('cron_schedules', 'wc_satispay_cron_schedule');
function wc_satispay_init() {
	if (!class_exists('WC_Payment_Gateway')) return;

	include_once('wc-satispay.php');

    // Make the Satispay Payments gateway available to WC.
	add_filter('woocommerce_payment_gateways', 'wc_satispay_add_gateway', 15);
    function wc_satispay_add_gateway($methods) {
        $methods[] = 'WC_Satispay';
        return $methods;
    }

    // Registers WooCommerce Blocks integration.
    add_action( 'woocommerce_blocks_loaded', 'woocommerce_gateway_satispay_woocommerce_block_support');
    function woocommerce_gateway_satispay_woocommerce_block_support() {
        if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType') ) {
            require_once 'includes/blocks/wc-satispay-blocks.php';
            add_action(
                'woocommerce_blocks_payment_method_type_registration',
                function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
                    $payment_method_registry->register( new WC_Satispay_Blocks );
                }
            );
        }
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
