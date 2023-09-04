<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/stratos-vetsos-08262473/
 * @since      1.0.0
 *
 * @package    Wc_Order_Navigation
 * @subpackage Wc_Order_Navigation/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Order_Navigation
 * @subpackage Wc_Order_Navigation/admin
 * @author     FullStack <vetsos.s@gmail.com>
 */
class Wc_Order_Navigation_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'woocommerce_order_actions_end', array( $this, 'meta_box_output' ) );

	}

	public function meta_box_output() {

		global $post, $wpdb, $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}

		$order_type_object = get_post_type_object( $post->post_type );

		$order_navigation = $wpdb->get_row( $wpdb->prepare( "
			SELECT
				(SELECT ID FROM {$wpdb->prefix}posts
				WHERE ID < %d
				AND post_type = '%s'
				AND post_status <> 'trash'
				ORDER BY ID DESC LIMIT 1 )
				AS prev_order_id,
				(SELECT ID FROM {$wpdb->prefix}posts
				WHERE ID > %d
				AND post_type = '%s'
				AND post_status <> 'trash'
				ORDER BY ID ASC LIMIT 1 )
				AS next_order_id
		", $post->ID, $post->post_type, $post->ID, $post->post_type ), ARRAY_A );

		include( 'partials/wc-order-navigation-display.php' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Order_Navigation_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Order_Navigation_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = get_current_screen();
		$action = isset( $_GET[ 'action' ] ) && $_GET [ 'action' ] ? $_GET[ 'action' ] : '';

		if( $screen->id === 'shop_order' && $action === 'edit' )
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-order-navigation-admin.css', array(), $this->version, 'all' );

	}

}
