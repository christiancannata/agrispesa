=== Use Memcached ===
Contributors: edwardbock, stefanpejcic
Donate link: http://palasthotel.de/
Tags: cache, performance, memcache, memcached, caching, object cache
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 1.0.5
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl

WP_Object_Cache implementation with Memcached.

== Description ==

Use this to optimize your website performance with Memcached instances.

Simply install the plugin, add your Memcached server and port in the plugin settings *(default are **127.0.0.1:11211**) and click on the **Enable memcached!** button.




For more free WordPress plugins please visit [♣️ plugins.club](https://plugins.club/).

== Installation ==

1. Upload `use-memcached.zip` to the `/wp-content/plugins/` directory
2. Extract the Plugin to a `use-memcached` Folder
3. Activate the plugin through the 'Plugins' menu in WordPress
4. The plugin will copy a object-cache.php file to /wp-content/ folder
5. You can provide custom memcached server and port in plugin settings page

== Frequently Asked Questions ==

= Do I have to configure something? =

If you are using a Memcached service with default values host 127.0.0.1 (localhost) and port 11211 or you are hosting with freistil.it than you don't need to configure anything.

With other hosters or service settings you need to set the server and port in the plugin settings page.

= How to purge cache? =

Click on the "Purge Cache" link in the topbar or using WP-CLI:
`
wp memcache flush
`

= How to use multiple Memcached servers? =

Currently the only way to use more than one Memcached server requires to edit the object-cache.php file:

`
$memcached_servers = array(
	                '10.10.10.20:11211',
	                '10.10.10.30:11217'
	);
`

== Screenshots ==

1. Add Memcached server hostname and port
2. Preview cached data and monitor resource usage
3. Cache warmup using sitemap file


== Changelog ==

= 1.0.5 =

* Added a settings page where user can set memcached server and port
* Added a settings page that allows user to view cached information

= 1.0.4 =
* process logs will only be written on WP_DEBUG = true or USE_MEMCACHED_PROCESS_LOG = true sessions

= 1.0.3 =
* ignoring alloptions and notoptions key for performance reasons
* logging with ProcessLog

= 1.0.2 =
* deleted var_dump output

= 1.0.1 =
* First release

= 1.0.0 =
* Submitted to wordpress.org plugin repo version

== Upgrade Notice ==


== CREDITS ==

Originally developed by [Palasthotel (in person: Edward Bock)](https://palasthotel.de/)
