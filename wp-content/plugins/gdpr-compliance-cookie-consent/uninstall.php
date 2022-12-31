<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
	die();
}

global $wpdb;

$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE 'gdpr-compliance-cookie-consent\_%';");

wp_cache_flush();