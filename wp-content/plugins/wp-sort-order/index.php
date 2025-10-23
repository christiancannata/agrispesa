<?php if ( ! defined( 'ABSPATH' ) ) exit; 
/*
Plugin Name: WP Sort Order
Plugin URI: http://androidbubble.com/blog/wordpress/plugins/wp-sort-order
Description: Order plugins, terms (Users, Posts, Pages, Custom Post Types and Custom Taxonomies) using a Drag and Drop with jQuery ui Sortable.
Version: 1.3.5
Author: Fahad Mahmood 
Author URI: http://www.androidbubble.com
Text Domain: wpso-sort-order
Domain Path: /languages/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This WordPress Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This free software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/ 

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	include('inc/functions.php');
	
	global $wpso_data, $wpso_pro, $wpso_premium_link, $wpso_premium, $premium_click, $wpso_allowed_pages, $premium_tags_list;
	$wpso_allowed_pages = array('plugins.php'=>'Plugins');
	$wpso_data = get_plugin_data(__FILE__, true, false);
	$wpso_dir = plugin_dir_path( __FILE__ );
	$premium_tags_list = array('usercategories');
	define( 'WPSO_URL', plugins_url( '', __FILE__ ) );
	define( 'WPSO_DIR', $wpso_dir );
	$wpso_premium = $wpso_dir.'pro/wpso_extended.php';
	$wpso_pro = file_exists($wpso_premium);
	$wpso_premium_link = 'https://shop.androidbubbles.com/product/wp-sort-order-pro';//https://shop.androidbubble.com/products/wordpress-plugin?variant=36439508713627';//
	
	$premium_click = $wpso_pro?'':'<small class="premium">('.__('Premium Feature','wpso-sort-order').')</small>';
	
	if(is_admin()){
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'wpso_plugin_links' );	
		
	}	
	
		
		
	function wpso_activate() {			
		
	}
	register_activation_hook( __FILE__, 'wpso_activate' );
	
	if($wpso_pro)
	include($wpso_premium);
	
	//echo $wpso_premium;
	
	include('inc/hooks.php');
	