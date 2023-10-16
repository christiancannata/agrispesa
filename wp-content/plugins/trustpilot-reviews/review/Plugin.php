<?php
/**
 * Trustpilot-reviews
 *
 * @package   Trustpilot-reviews
 * @link      https://trustpilot.com
 */

namespace Trustpilot\Review;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Trustpilot-reviews
 * 
 * @subpackage Plugin
 */
class Plugin {

	/**
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 * 
	 * @var      string
	 */
	protected $plugin_name = 'Trustpilot-review';

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Setup instance attributes
	 */
	private function __construct() {
		$this->plugin_version = TRUSTPILOT_REVIEWS_VERSION;
	}

	/**
	 * Return an instance of this class.
	 * 
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->do_hooks();
		}
		return self::$instance;
	}

	/**
	 * Return the plugin slug.
	 * 
	 * @return    Plugin name variable.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Return the plugin version.
	 * 
	 * @return    Plugin version variable.
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * Add Settings link
	 */
	public static function settings_link( $links ) {
		$settings_url = esc_url( get_admin_url( null, 'admin.php?page=woocommerce-trustpilot-settings-page' ) );
		array_unshift( $links, '<a href="' . $settings_url . '">Settings</a>' );
		return $links;
	}

	/**
	 * Fired when the plugin is activated.
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( $_REQUEST['plugin'] ) : '';
		$referer = $plugin ? "activate-plugin_{$plugin}" : 'bulk-plugins';
		check_admin_referer( $referer );

		update_option( 'trustpilot_just_installed', true );
		$trustpilot_settings      = get_option( 'trustpilot_settings', trustpilot_get_default_settings() );
		$trustpilot_past_orders   = get_option( 'trustpilot_past_orders', trustpilot_get_default_past_orders() );
		$trustpilot_failed_orders = get_option( 'trustpilot_failed_orders', trustpilot_get_default_failed_orders() );
		$trustpilot_plugin_status = get_option( TRUSTPILOT_PLUGIN_STATUS, trustpilot_get_default_plugin_status() );
		add_option( 'trustpilot_settings', $trustpilot_settings );
		add_option( 'trustpilot_past_orders', $trustpilot_past_orders );
		add_option( 'trustpilot_failed_orders', $trustpilot_failed_orders );
		add_option( TRUSTPILOT_PLUGIN_STATUS, $trustpilot_plugin_status );
		add_option( 'show_past_orders_initial', 'true' );
		add_option( 'sync_in_progress', 'false' );
		$trustpilot_api = new TrustpilotHttpClient( TRUSTPILOT_API_URL );
		$data           = array(
			'settings' => $trustpilot_settings,
			'version'  => TRUSTPILOT_PLUGIN_VERSION,
			'event'    => 'Activated',
			'platform' => 'Wordpress-Woocommerce',

		);
		$trustpilot_api->postLog( $data );
	}

	/**
	 * Fired when the plugin is deactivated.
	 */
	public static function deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( $_REQUEST['plugin'] ) : '';
		$referer = $plugin ? "deactivate-plugin_{$plugin}" : 'bulk-plugins';
		check_admin_referer( $referer );

		$trustpilot_settings = trustpilot_get_settings( TRUSTPILOT_GENERAL_CONFIGURATION );
		$trustpilot_api      = new TrustpilotHttpClient( TRUSTPILOT_API_URL );
		$data                = array(
			'settings' => $trustpilot_settings,
			'version'  => TRUSTPILOT_PLUGIN_VERSION,
			'event'    => 'Deactivated',
			'platform' => 'Wordpress-Woocommerce',

		);
		$trustpilot_api->postLog( $data );
		update_option( 'trustpilot_settings', trustpilot_get_default_settings() );
		update_option( 'trustpilot_past_orders', trustpilot_get_default_past_orders() );
		update_option( 'trustpilot_failed_orders', trustpilot_get_default_failed_orders() );
		update_option( TRUSTPILOT_PLUGIN_STATUS, trustpilot_get_default_plugin_status() );
		update_option( 'show_past_orders_initial', 'true' );
	}

	/**
	 * Handle WP actions and filters.
	 */
	private function do_hooks() {
		add_filter( 'clean_url', array( $this, 'trustpilot_async_scripts' ), 11, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'trustpilot_load_js' ) );
		require_once( plugin_dir_path( __FILE__ ) . 'TrustBox.php' );
	}

	// Async load
	public function trustpilot_async_scripts( $url ) {
		if ( strpos( $url, '#trustpilot_async' ) === false ) {
			return $url;
		} elseif ( is_admin() ) {
			return str_replace( '#trustpilot_async', '', $url );
		} else {
			return str_replace( '#trustpilot_async', '', $url ) . "' async='async";
		}
	}

	public function trustpilot_load_js( $hook ) {
		wp_register_script( 'tp-js', plugins_url( 'assets/js/headerScript.min.js#trustpilot_async', __FILE__ ), [], '1.0' );
		$key = trustpilot_get_settings( TRUSTPILOT_GENERAL_CONFIGURATION )->key;
		$trustbox = trustpilot_get_settings( TRUSTPILOT_TRUSTBOX_CONFIGURATION );
		wp_localize_script(
			'tp-js',
			'trustpilot_settings',
			array(
				TRUSTPILOT_INTEGRATION_KEY => $key,
				TRUSTPILOT_SCRIPT          => TRUSTPILOT_SCRIPT_URL,
				TRUSTPILOT_INTEGRATION_APP => TRUSTPILOT_INTEGRATION_APP_URL,
				TRUSTPILOT_PREVIEW_SCRIPT  => TRUSTPILOT_PREVIEW_SCRIPT_URL,
				TRUSTPILOT_PREVIEW_CSS     => TRUSTPILOT_PREVIEW_CSS_URL,
				TRUSTPILOT_WP_PREVIEW_CSS  => TRUSTPILOT_WP_PREVIEW_CSS_URL,
				TRUSTPILOT_WIDGET_SCRIPT   => TRUSTPILOT_WIDGET_SCRIPT_URL,
			)
		);
		wp_enqueue_script( 'tp-js' );
		if (isset($trustbox->trustboxes) && count($trustbox->trustboxes) > 0) {
			wp_enqueue_script( 'widget-bootstrap', TRUSTPILOT_WIDGET_SCRIPT_URL . '#trustpilot_async', [], '1.0' );
		}
	}
}
