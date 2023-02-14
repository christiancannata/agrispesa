<?php
/**
 * Clone Woo Orders - Free by WP Masters
 *
 * This plugin adds One-Click Clone feature for each Woo Order at Orders List
 *
 * Plugin Name: Clone Woo Orders - Free by WP Masters
 * Plugin URI: https://wp-masters.com/woo-orders-clone
 * Version: 1.0.3
 * Author: WP Masters
 * Description: This plugin adds One-Click Clone feature for each Woo Order at Orders List
 * Text Domain: wpm-woo-clone-order
 * Author URI: https://wp-masters.com
 *
 * @author      WP Masters
 * @version     v.1.0.3 (18/12/22)
 * @copyright   Copyright (c) 2021
 */

/**
 * Text Domain
 */
define('WPM_WOOCOMMERCE_CLONE_ORDER_TEXT_DOMAIN', 'wpm-woo-clone-order');

/**
 * The core logic of Woo Order cloning
 */
function wpm_woocommerce_order_clone($order_id = false, $post_status = 'wc-pending')
{
    global $wpdb;

	/**
	 * Check if we not use actions from list
	 */
    if(!$order_id) {
	    if ( ! (isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'wpm_woocommerce_order_clone' == $_REQUEST['action']))) {
		    wp_die('No post to duplicate has been supplied!');
	    }

	    /**
	     * Verification
	     */
	    if ( ! isset($_GET['duplicate_nonce']) || ! wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__))) {
		    return;
	    }
    }

    /**
     * Get original post which must be cloned
     */
    $post_id         = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
	if ( (int)$order_id ){
		$post_id = $order_id;
	}
    $post            = get_post($post_id);
    $current_user    = wp_get_current_user();
    $new_post_author = get_current_user_id();

    /**
     * If post exists - make clone
     */
    if (isset($post) && $post != null) {

        /**
         * Data for new post object
         */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => $post_status,
            'post_title'     => $post->post_title,
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
        );

        /**
         * Create new post (order)
         */
        $new_post_id = wp_insert_post($args);

        /**
         * Grab original order items to be clone
         */
        $order     = wc_get_order($post_id);
        $new_order = wc_get_order($new_post_id);
        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            $quantity   = $item->get_quantity();
            $new_order->add_product(wc_get_product($product_id), $quantity);
        }

        /**
         * Duplicate fields using native WC Object API
         */
        $wc_duplicate_fields = [
            'customer_id',
            'billing_first_name',
            'billing_last_name',
            'billing_company',
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_state',
            'billing_postcode',
            'billing_country',
            'billing_email',
            'billing_phone',
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_city',
            'shipping_state',
            'shipping_postcode',
            'shipping_country',
        ];
        foreach ($wc_duplicate_fields as $field) {
            $setter_method = "set_{$field}";
            $getter_method = "get_{$field}";
            $new_order->$setter_method($order->$getter_method());
        }

        /**
         * Refresh Totals for new order
         */
        $new_order->calculate_totals();

        /**
         * Save all the changes to new order
         */
        $new_order->save();

        /**
         * Redirect user to new post and exit
         */
        wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
        exit;

    } else {
        wp_die(__('Can\'t clone, post with given ID not found: ', WPM_WOOCOMMERCE_CLONE_ORDER_TEXT_DOMAIN) . $post_id);
    }
}

add_action('admin_action_wpm_woocommerce_order_clone', 'wpm_woocommerce_order_clone');

/**
 * Add Clone button to Actions column at Woo orders list
 *
 * @param $post
 */
function wpm_woocommerce_order_clone_action_link($post)
{
    if (current_user_can('edit_posts') || current_user_can('manage_woocommerce')) {
        echo ' <a href="' . wp_nonce_url('admin.php?action=wpm_woocommerce_order_clone&post=' . $post->get_id(),
                basename(__FILE__),
                'duplicate_nonce') . '" title="' . __('Clone this order',
                WPM_WOOCOMMERCE_CLONE_ORDER_TEXT_DOMAIN) . '"><button type="button" class="button action">' . __('Clone',
                WPM_WOOCOMMERCE_CLONE_ORDER_TEXT_DOMAIN) . '</button></a>';
    }
}

add_filter('woocommerce_admin_order_actions_end', 'wpm_woocommerce_order_clone_action_link', 9999, 1);

/**
 * Add a Clone action in Select in Order page
 *
 * @param $actions
 */
function wpm_add_clone_order_action( $actions )
{
	$actions['clone_order'] = __( 'Clone Order', 'woocommerce' );
	$actions['clone_order_draft'] = __( 'Clone Order as Draft', 'woocommerce' );

	return $actions;
}

add_filter( 'woocommerce_order_actions', 'wpm_add_clone_order_action', 20, 1 );

/**
 * Trigger Clone action from  Order page
 *
 * @param $order
 */
function wpm_trigger_action_clone_order( $order ) {
	$order_id = $order->get_id();

	wpm_woocommerce_order_clone($order_id);
}

add_action( 'woocommerce_order_action_clone_order', 'wpm_trigger_action_clone_order', 20, 1 );

/**
 * Trigger Clone action from  Order page
 *
 * @param $order
 */
function wpm_trigger_action_clone_order_draft( $order ) {
	$order_id = $order->get_id();

	wpm_woocommerce_order_clone($order_id, 'wc-checkout-draft');
}

add_action( 'woocommerce_order_action_clone_order_draft', 'wpm_trigger_action_clone_order_draft', 20, 1 );