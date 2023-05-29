<?php

ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// disable xdebug backtrace
if ( function_exists( 'xdebug_disable' ) ) {
	xdebug_disable();
}

if ( getenv( 'PLUGIN_PATH' ) !== false ) {
	define( 'PLUGIN_PATH', getenv( 'PLUGIN_PATH' ) );
} else {
	define( 'PLUGIN_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR );
}

require_once( getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/functions.php' );

tests_add_filter( 'muplugins_loaded', function () {
	update_option( 'active_plugins', $plugins_to_active );
}, 100 );

putenv('WP_TESTS_DIR=' . getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit');
require_once( getenv( 'WC_DEVELOP_DIR' ) . '/tests/bootstrap.php' );

