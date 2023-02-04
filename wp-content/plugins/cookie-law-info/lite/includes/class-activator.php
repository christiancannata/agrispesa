<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.webtoffee.com/
 * @since      3.0.0
 *
 * @package    CookieYes
 * @subpackage CookieYes/includes
 */

namespace CookieYes\Lite\Includes;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      3.0.0
 * @package    CookieYes
 * @subpackage CookieYes/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Activator {

	/**
	 * Instance of the current class
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Return the current instance of the class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Activate the plugin
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
	}
	/**
	 * Check the plugin version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'wt_cli_version', '2.1.3' ), CLI_VERSION, '<' ) ) {
			self::install();
		}
	}
	/**
	 * Install all the plugin
	 *
	 * @return void
	 */
	public static function install() {
		self::check_for_upgrade();
		if ( true === cky_first_time_install() ) {
			add_option( 'cky_first_time_activated_plugin', 'true' );
		}
		update_option( 'wt_cli_version', CLI_VERSION );
		do_action( 'cky_after_activate', CLI_VERSION );
	}

	/**
	 * Set a temporary flag during the first time installation.
	 *
	 * @return void
	 */
	public static function check_for_upgrade() {
		if ( false === get_option( 'cky_settings', false ) ) {
			update_option( 'cky_cookie_consent_lite_db_version', '3.0' );
			if ( false === get_site_transient( '_cky_first_time_install' ) ) {
				set_site_transient( '_cky_first_time_install', true, 30 );
			}
		}
	}
}
