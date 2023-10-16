<?php
/**
 * Plugin Name: YITH WooCommerce Gift Cards
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-gift-cards
 * Description: <code><strong>YITH WooCommerce Gift Cards</strong></code> allows your users to purchase and give gift cards. In this way, you will increase the spread of your brand, your sales, and average spend, especially during the holidays. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 4.5.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-gift-cards
 * Domain Path: /languages/
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 **/

/*
Copyright 2013-2023  Your Inspiration Solutions  (email : plugins@yithemes.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! function_exists( 'yith_ywgc_install_woocommerce_admin_notice' ) ) {

	/**
	 * Yith_ywgc_install_woocommerce_admin_notice
	 *
	 * @return void
	 */
	function yith_ywgc_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'YITH WooCommerce Gift Cards is enabled but not effective. It requires WooCommerce in order to work.', 'yit' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

// region    ****    Define constants.

defined( 'YITH_YWGC_FREE' ) || define( 'YITH_YWGC_FREE', '1' );
defined( 'YITH_YWGC_SLUG' ) || define( 'YITH_YWGC_SLUG', 'yith-woocommerce-gift-cards' );
defined( 'YITH_YWGC_FREE_INIT' ) || define( 'YITH_YWGC_FREE_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_YWGC_SECRET_KEY' ) || define( 'YITH_YWGC_SECRET_KEY', 'GcGTnx2i0Qdavxe9b9by' );
defined( 'YITH_YWGC_PLUGIN_NAME' ) || define( 'YITH_YWGC_PLUGIN_NAME', 'YITH WooCommerce Gift Cards' );
defined( 'YITH_YWGC_INIT' ) || define( 'YITH_YWGC_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_YWGC_VERSION' ) || define( 'YITH_YWGC_VERSION', '4.5.0' );
defined( 'YITH_YWGC_DB_CURRENT_VERSION' ) || define( 'YITH_YWGC_DB_CURRENT_VERSION', '1.0.1' );
defined( 'YITH_YWGC_FILE' ) || define( 'YITH_YWGC_FILE', __FILE__ );
defined( 'YITH_YWGC_DIR' ) || define( 'YITH_YWGC_DIR', plugin_dir_path( __FILE__ ) );
defined( 'YITH_YWGC_URL' ) || define( 'YITH_YWGC_URL', plugins_url( '/', __FILE__ ) );
defined( 'YITH_YWGC_ASSETS_URL' ) || define( 'YITH_YWGC_ASSETS_URL', YITH_YWGC_URL . 'assets' );
defined( 'YITH_YWGC_ASSETS_DIR' ) || define( 'YITH_YWGC_ASSETS_DIR', YITH_YWGC_DIR . 'assets' );
defined( 'YITH_YWGC_SCRIPT_URL' ) || define( 'YITH_YWGC_SCRIPT_URL', YITH_YWGC_ASSETS_URL . '/js/' );
defined( 'YITH_YWGC_TEMPLATES_DIR' ) || define( 'YITH_YWGC_TEMPLATES_DIR', YITH_YWGC_DIR . 'templates/' );
defined( 'YITH_YWGC_ASSETS_IMAGES_URL' ) || define( 'YITH_YWGC_ASSETS_IMAGES_URL', YITH_YWGC_ASSETS_URL . '/images/' );
defined( 'YITH_YWGC_VIEWS_PATH' ) || define( 'YITH_YWGC_VIEWS_PATH', YITH_YWGC_DIR . 'views/' );
defined( 'YITH_YWGC_MODULES_PATH' ) || define( 'YITH_YWGC_MODULES_PATH', YITH_YWGC_DIR . 'modules/' );
defined( 'YITH_YWGC_MODULES_URL' ) || define( 'YITH_YWGC_MODULES_URL', YITH_YWGC_URL . 'modules/' );
defined( 'YITH_YWGC_PREMIUM_LANDING_URL' ) || define( 'YITH_YWGC_PREMIUM_LANDING_URL', 'https://yithemes.com/themes/plugins/yith-woocommerce-gift-cards/' );

// endregion.

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWGC_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWGC_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWGC_DIR );

if ( ! function_exists( 'yith_ywgc_init' ) ) {
	/**
	 * Init the plugin
	 *
	 * @author YITH <plugins@yithemes.com>
	 * @since  1.0.0
	 */
	function yith_ywgc_init() {
        
		/**
		 * Load text domain and start plugin
		 */
		load_plugin_textdomain( 'yith-woocommerce-gift-cards', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		/**
		 * Load required classes
		 */

		// Free.
		require_once( YITH_YWGC_DIR . 'includes/admin/class-ywgc-admin.php' );
		require_once YITH_YWGC_DIR . 'includes/class-yith-wc-product-gift-card.php';
		require_once YITH_YWGC_DIR . 'includes/class-yith-ywgc-cart-checkout.php';
		require_once YITH_YWGC_DIR . 'includes/class-yith-ywgc-emails.php';
		require_once YITH_YWGC_DIR . 'includes/class-yith-woocommerce-gift-cards.php';
		require_once YITH_YWGC_DIR . 'includes/admin/class-yith-ywgc-backend.php';
		require_once YITH_YWGC_DIR . 'includes/class-yith-ywgc-frontend.php';
		require_once YITH_YWGC_DIR . 'includes/class-yith-ywgc-gift-card.php';
		require_once YITH_YWGC_DIR . 'includes/class-yith-ywgc-module.php';
		require_once YITH_YWGC_DIR . 'includes/class-yith-ywgc-modules.php';
		require_once YITH_YWGC_DIR . 'includes/admin/taxonomies/class-yith-ywgc-categories.php';
		require_once YITH_YWGC_DIR . 'includes/shortcodes/class-yith-ywgc-shortcodes.php';

		// Load functions
		require_once YITH_YWGC_DIR . 'includes/functions.yith-ywgc.php';

		// Start the plugin.
		YITH_YWGC();

		do_action( 'yith_ywgc_loaded' );

	}
}
add_action( 'yith_ywgc_init', 'yith_ywgc_init' );

if ( ! function_exists( 'YITH_YWGC' ) ) {
	/**
	 * Get the main plugin class
	 *
	 * @since  1.0.0
	 */
	function YITH_YWGC() {// phpcs:ignore WordPress.NamingConventions
		return YITH_WooCommerce_Gift_Cards::get_instance();
	}
}

if ( ! function_exists( 'yith_ywgc_install' ) ) {
	/**
	 * Install the plugin
	 *
	 * @since  1.0.0
	 */
	function yith_ywgc_install() {

		if ( ! function_exists( 'yith_deactivate_plugins' ) ) {
			require_once 'plugin-fw/yit-deactive-plugin.php';
		}

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywgc_install_woocommerce_admin_notice' );
		} elseif ( defined( 'YITH_YWGC_PREMIUM' ) || defined( 'YITH_YWGC_EXTENDED' ) ) {
			yith_deactivate_plugins( 'YITH_YWGC_FREE_INIT' );
		} else {
			do_action( 'yith_ywgc_init' );
		}
	}
}

add_action( 'plugins_loaded', 'yith_ywgc_install', 11 );
