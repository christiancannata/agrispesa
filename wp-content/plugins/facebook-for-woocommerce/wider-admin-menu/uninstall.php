<?php
/**
 * Wider Admin Menu uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = get_option( 'wpmwam_options' );
if ( $options['wpmwam_lnt'] ) {
	delete_option( 'wpmwam_options' );
}
