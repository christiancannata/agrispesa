<?php
/**
 * Plugin Name: YITH WooCommerce Gift Cards
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-gift-cards
 * Description: <code><strong>YITH WooCommerce Gift Cards</strong></code> allows your users to purchase and give gift cards. In this way, you will increase the spread of your brand, your sales, and average spend, especially during the holidays. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 4.28.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-gift-cards
 * Domain Path: /languages/
 * WC requires at least: 10.0
 * WC tested up to: 10.2
 * Requires Plugins: woocommerce
 *
 * @package YITH\GiftCards
 **/

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

defined( 'YITH_YWGC_FREE' ) || define( 'YITH_YWGC_FREE', '1' );
defined( 'YITH_YWGC_SLUG' ) || define( 'YITH_YWGC_SLUG', 'yith-woocommerce-gift-cards' );
defined( 'YITH_YWGC_FREE_INIT' ) || define( 'YITH_YWGC_FREE_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_YWGC_SECRET_KEY' ) || define( 'YITH_YWGC_SECRET_KEY', 'GcGTnx2i0Qdavxe9b9by' );
defined( 'YITH_YWGC_PLUGIN_NAME' ) || define( 'YITH_YWGC_PLUGIN_NAME', 'YITH WooCommerce Gift Cards' );
defined( 'YITH_YWGC_INIT' ) || define( 'YITH_YWGC_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_YWGC_VERSION' ) || define( 'YITH_YWGC_VERSION', '4.28.0' );
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

$wp_upload_dir = wp_upload_dir();

defined( 'YITH_YWGC_SAVE_DIR' ) || define( 'YITH_YWGC_SAVE_DIR', $wp_upload_dir['basedir'] . '/yith-gift-cards/' );
defined( 'YITH_YWGC_SAVE_URL' ) || define( 'YITH_YWGC_SAVE_URL', $wp_upload_dir['baseurl'] . '/yith-gift-cards/' );

if ( ! file_exists( YITH_YWGC_SAVE_DIR ) ) {
	mkdir( YITH_YWGC_SAVE_DIR, 0770, true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
}

// Plugin Framework Loader.
if ( file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}

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
		if ( function_exists( 'yith_plugin_fw_load_plugin_textdomain' ) ) {
			yith_plugin_fw_load_plugin_textdomain( 'yith-woocommerce-gift-cards', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Load required classes
		 */

		// Free.
		require_once YITH_YWGC_DIR . 'includes/admin/class-ywgc-admin.php';
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

		// Load functions.
		require_once YITH_YWGC_DIR . 'includes/functions.yith-ywgc.php';

		// Post type helper class.
		require_once YITH_YWGC_DIR . 'includes/admin/post-types/class-yith-ywgc-gift-card-post-type-admin.php';

		// Privacy class.
		require_once YITH_YWGC_DIR . 'includes/class-yith-woocommerce-gift-cards-privacy.php';

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
