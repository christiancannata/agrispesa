<?php
/**
 * Add New Tab on the My Account page
 */

function add_settings_box_endpoint()
{

	add_rewrite_endpoint('settings-box', EP_ROOT | EP_PAGES);

}

add_action('init', 'add_settings_box_endpoint');

// ------------------

// 2. Add new query var

function settings_box_query_vars($vars)
{

	$vars[] = 'settings-box';

	return $vars;

}

add_filter('query_vars', 'settings_box_query_vars', 0);

// ------------------

// 3. Insert the new endpoint into the My Account menu

function settings_box_link_my_account($items)
{

	$items['settings-box'] = 'Preferenze Box';

	return $items;

}

add_filter('woocommerce_account_menu_items', 'settings_box_link_my_account');

// ------------------

// 4. Add content to the new tab

function settings_box_content()
{
	if (wcs_user_has_subscription()) { // Current user has an active subscription
		echo '<p>I have active subscription</p>';
	}
	echo "test";
}

add_action('woocommerce_account_settings-box_endpoint', 'settings_box_content');
