<?php

/**
 * @package   WooFic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 Christian Cannata
 * @license   GPL 2.0+
 * @link      https://christiancannata.com
 *
 * Plugin Name:     WooFic - Integra FattureInCloud con WooCommerce
 * Plugin URI:      https://woofic.it
 * Description:     Integra la tua fatturazione di FattureInCloud con il tuo shop online fatto con WooCommerce.
 * Version:         1.0.3
 * Author:          Christian Cannata
 * Author URI:      https://christiancannata.com
 * Text Domain:     woofic
 * License:         GPL 2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:     /languages
 * Requires PHP:    7.4
 * WordPress-Plugin-Boilerplate-Powered: v3.3.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

define( 'W_VERSION', '1.0.3' );
define( 'W_TEXTDOMAIN', 'woofic' );
define( 'W_NAME', 'WooFic' );
define( 'W_PLUGIN_ROOT', plugin_dir_path( __FILE__ ) );
define( 'W_PLUGIN_ABSOLUTE', __FILE__ );
define( 'W_MIN_PHP_VERSION', '7.4' );
define( 'W_WP_VERSION', '5.3' );
define( 'WOOFIC_ENDPOINT', 'https://bbkhaddiuggia.loc' );
define( 'WOOFIC_ENDPOINT_USERNAME', 'ck_47778ab759347de0cb5ecdeba992fdfcff69592e' );
define( 'WOOFIC_ENDPOINT_PASSWORD', 'cs_22c8e94c7f68723942c4294a1cc1146f1885e1d4' );

add_action(
	'init',
	static function () {
		load_plugin_textdomain( W_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

if ( version_compare( PHP_VERSION, W_MIN_PHP_VERSION, '<=' ) ) {
	add_action(
		'admin_init',
		static function () {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	);
	add_action(
		'admin_notices',
		static function () {
			echo wp_kses_post(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					__( '"WooFic" requires PHP 7.4 or newer.', W_TEXTDOMAIN )
				)
			);
		}
	);

	// Return early to prevent loading the plugin.
	return;
}

$woofic_libraries = require W_PLUGIN_ROOT . 'vendor/autoload.php'; //phpcs:ignore

require_once W_PLUGIN_ROOT . 'functions/functions.php';
require_once W_PLUGIN_ROOT . 'functions/debug.php';

// Add your new plugin on the wiki: https://github.com/WPBP/WordPress-Plugin-Boilerplate-Powered/wiki/Plugin-made-with-this-Boilerplate

$requirements = new \Micropackage\Requirements\Requirements(
	'WooFic',
	array(
		'php'            => W_MIN_PHP_VERSION,
		'php_extensions' => array( 'mbstring' ),
		'wp'             => W_WP_VERSION,
		'plugins'        => array(//	array( 'file' => 'wp-router/wp-router.php', 'name' => 'WP Router', 'version' => '0.5' )
		),
	)
);

if ( ! $requirements->satisfied() ) {
	$requirements->print_notice();

	return;
}


/**
 * Create a helper function for easy SDK access.
 *
 * @return object
 * @global type $w_fs
 */
function w_fs() {
	global $w_fs;

	if ( ! isset( $w_fs ) ) {
		require_once W_PLUGIN_ROOT . 'vendor/freemius/wordpress-sdk/start.php';
		$w_fs = fs_dynamic_init(
			array(
				'id'             => '',
				'slug'           => 'woofic',
				'public_key'     => '',
				'is_live'        => false,
				'is_premium'     => true,
				'has_addons'     => false,
				'has_paid_plans' => true,
				'menu'           => array(
					'slug' => 'woofic',
				),
			)
		);

		if ( $w_fs->is_premium() ) {
			$w_fs->add_filter(
				'support_forum_url',
				static function ( $wp_org_support_forum_url ) { //phpcs:ignore
					return 'https://your-url.test';
				}
			);
		}
	}

	return $w_fs;
}

// w_fs();

// Documentation to integrate GitHub, GitLab or BitBucket https://github.com/YahnisElsts/plugin-update-checker/blob/master/README.md
Puc_v4_Factory::buildUpdateChecker( 'https://github.com/user-name/repo-name/', __FILE__, 'unique-plugin-or-theme-slug' );

if ( ! wp_installing() ) {
	register_activation_hook( W_TEXTDOMAIN . '/' . W_TEXTDOMAIN . '.php', array(
		new \WooFic\Backend\ActDeact,
		'activate'
	) );

	function wfic_ms_activate( $networkwide ) {
		if ( is_multisite() || $networkwide ) {

			deactivate_plugins( plugin_basename( __FILE__ ) );

			wp_die( __( 'Spiacenti il plugin WooCommerce Fattureincloud Premium non è attivabile nel Network di
            WordPress Multisite.', 'woo_fattureincloud_premium_textdomain' ) );

		}
	}


	register_activation_hook( __FILE__, 'wfic_ms_activate' );


	register_deactivation_hook( W_TEXTDOMAIN . '/' . W_TEXTDOMAIN . '.php', array(
		new \WooFic\Backend\ActDeact,
		'deactivate'
	) );
	add_action(
		'plugins_loaded',
		static function () use ( $woofic_libraries ) {
			new \WooFic\Engine\Initialize( $woofic_libraries );
		}
	);

}
