=== WP Sort Order ===
Contributors: fahadmahmood, kiranzehra
Tags: taxonomy order, user order, plugins order, post order
Requires at least: 3.5.0
Tested up to: 6.2
Stable tag: 1.3.1
Requires PHP: 7.0
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Order terms (Users, Posts, Pages, Custom Post Types and Custom Taxonomies) using a Drag and Drop with jQuery ui Sortable.

== Description ==

* Author: [Fahad Mahmood](https://www.androidbubbles.com/contact)

* Project URI: <http://androidbubble.com/blog/wordpress/plugins/wp-sort-order>

* License: GPL 3. See License below for copyright jots and titles.


Order terms (Users, Posts, Pages, Custom Post Types and Custom Taxonomies) using a Drag and Drop with jQuery ui Sortable.

Select sortable items from 'WP Sort Order' menu of Setting menu in WordPress.

In addition, You can re-override the parameters of 'orderby' and 'order', by using the 'WP_Query' or 'pre_get_posts' or 'query_posts()'.<br>
The 'get_posts()' is excluded.

At a glance by WordPress Mechanic:
[youtube http://www.youtube.com/watch?v=4ZiHUSBDJwY]

== Installation ==

1. Upload 'wp-sort-order' folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Select sortable items from 'WP Sort Order' menu of Setting menu in WordPress.

== Screenshots ==
1. How it works?
2. Settings Page
3. Installation & Activation
4. Settings Page - Full


== Frequently Asked Questions ==

= How does it work with custom taxonomies and custom post types? =

[youtube http://www.youtube.com/watch?v=-pLHSAALbGw]

= How does it work with WordPress Categories? =

[youtube http://www.youtube.com/watch?v=EKkf_54kUVU]

= How to enable sorting for posts/pages and taxonomies with this plugin? =

On settings page you can select posts/pages and taxonomies to enable sorting.

= Can users be sorted with this plugin? =

Yes, you can sort. And there is a shortcode to list users under taxonomy, terms and children. [WPSO_USERS slug="taxonomy or term slug" id="taxonomy or term id"]. This shortcode will list users on front-end with your sorted order. No need to write another query for it.

= How to re-override the parameters of 'orderby' and 'order' =

<strong>Sub query</strong>

By using the 'WP_Query', you can re-override the parameters.

* WP_Query

`
<?php $query = new WP_Query( array(
	'orderby' => 'date',
	'order' => 'DESC',
) ) ?>
`

<strong>Main query</strong>

By using the 'pre_get_posts' action hook or 'query_posts()', you can re-override the parameters.

* pre_get_posts

`
function my_filter( $query )
{
	if ( is_admin() || !$query->is_main_query() ) return;
	if ( is_home() ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
		return;
	}
}
add_action( 'pre_get_posts', 'my_filter' );
`

* query_posts()

`
<?php query_posts( array(
	'orderby' => 'rand'
) ); ?>
`

== Changelog ==
= 1.3.1 =
* Updated version for WordPress. [24/05/2023]
= 1.3.0 =
* Fix: WP Sort Order messing with posts date/order. [Thanks to Gonzalo/@jamposta][11/11/2022]
= 1.2.9 =
* Fix: Categories sort order keeps reverting. [Thanks to saschaprinzip][14/07/2022]
= 1.2.8 =
* Compatibility added for Stock Locations for WooCommerce. [02/03/2022]
= 1.2.7 =
* Ordering not working for taxonomies - undefined offset fixed in hooks.php file. [Thanks to apapadakis][12/01/2022]
= 1.2.6 =
* Bootstrap and Fontawesome libraries are updated. [26/10/2021]
= 1.2.5 =
* Updated version for WordPress.
= 1.2.4 =
* On activation it was taking up some space at the header of the website pages - Fixed. [Thanks to code naira]
= 1.2.3 =
* PHP warning fixed. [Thanks to @hastibe]
= 1.2.2 =
* New post menu_order implemented with 6 layers check. [Thanks to Fahad Mahmood & Abdul Razzaq]
= 1.2.1 =
* New post menu_order and page refresh issue resolved. [Thanks to @thisleenoble]
= 1.2.0 =
* get_current_screen() used. [Thanks to ricjoh]
= 1.1.9 =
* Languages added. [Thanks to Abu Usman]
= 1.1.8 =
* User sort order refined for decimals. [Thanks to Joe Garcia]
= 1.1.7 =
* Table prefix issue has been fixed. [Thanks to Columbird]
= 1.1.6 =
* User sort order refined. [Thanks to Joe Garcia]
= 1.1.4 =
* Sanitized input and fixed direct file access issues.
= 1.1.3 =
* Plugins can be sorted as well.
= 1.1.1 =
* A few improvements related to WordPress 4.6.
= 1.1.0 =
* A few improvements related to WordPress 4.5.0.
Initial Release

== Upgrade Notice ==
= 1.3.1 =
Updated version for WordPress.
= 1.3.0 =
Fix: WP Sort Order was messing with posts date/order.
= 1.2.9 =
Fix: Categories sort order keeps reverting.
= 1.2.8 =
Compatibility added for Stock Locations for WooCommerce.
= 1.2.7 =
Ordering not working for taxonomies - undefined offset fixed in hooks.php file.
= 1.2.6 =
Bootstrap and Fontawesome libraries are updated.
= 1.2.5 =
Updated version for WordPress.
= 1.2.4 =
On activation it was taking up some space at the header of the website pages - Fixed.
= 1.2.3 =
Updated assets.
= 1.2.2 =
New post menu_order implemented with 6 layers check.
= 1.2.1 =
New post menu_order and page refresh issue resolved.
= 1.2.0 =
get_current_screen() used.
= 1.1.9 =
Languages added.
= 1.1.8 =
User sort order refined for decimals.
= 1.1.7 =
Table prefix issue has been fixed.
= 1.1.6 =
User sort order refined.
= 1.1.4 =
Sanitized input and fixed direct file access issues.
= 1.1.3 =
Plugins can be sorted as well.
= 1.1.1 =
A few improvements related to WordPress 4.6.
= 1.1.0 =
A few improvements related to WordPress 4.5.0.

== License ==
This WordPress Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This free software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.