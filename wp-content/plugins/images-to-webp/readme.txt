=== Images to WebP ===
Contributors: kubiq
Donate link: https://www.paypal.me/jakubnovaksl
Tags: webp, images, pictures, optimize, convert, media
Requires at least: 3.0.1
Requires PHP: 5.6
Tested up to: 6.5
Stable tag: 4.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert PNG, JPG and GIF images to WebP and speed up your web


== Description ==

Statistics say that WebP format can save over a half of the page weight without losing images quality.
Convert PNG, JPG and GIF images to WebP and speed up your web, save visitors download data, make your Google ranking better.

<ul>
	<li><strong>automated test after plugin activation to make sure it will work on your server</strong></li>
	<li><strong>works with all types of WordPress installations: domain, subdomain, subdirectory, multisite/network</strong></li>
	<li><strong>works on Apache and NGiNX</strong></li>
	<li><strong>image URL will be not changed</strong> so it works everywhere, in &lt;img&gt; src, srcset, &lt;picture&gt;, even in CSS backgrounds and there is no problem with cache</li>
	<li><strong>original files will be not touched</strong></li>
	<li>set quality of converted images</li>
	<li>auto convert on upload</li>
	<li>only convert image if WebP filesize is lower than original image filesize</li>
	<li>bulk convert existing images to WebP ( you can choose folders )</li>
	<li>bulk convert only missing images</li>
	<li>works with `Fly Dynamic Image Resizer` plugin</li>
</ul>

## Hooks for developers

#### itw_extensions
Maybe you want to support also less famous JPEG extension like jpe, jfif or jif

`add_filter( 'itw_extensions', 'extra_itw_extensions', 10, 1 );
function extra_itw_extensions( $extensions ){
	$extensions[] = 'jpe';
	$extensions[] = 'jfif';
	$extensions[] = 'jif';
	return $extensions;
}`

#### itw_sizes
Maybe you want to disable WebP for thumbnails

`add_filter( 'itw_sizes', 'disable_itw_sizes', 10, 2 );
function disable_itw_sizes( $sizes, $attachmentId ){
	unset( $sizes['thumbnail'] );
	return $sizes;
}`

#### itw_htaccess
Maybe you want to modify htaccess rules somehow

`add_filter( 'itw_htaccess', 'modify_itw_htaccess', 10, 2 );
function modify_itw_htaccess( $rewrite_rules ){
	// do some magic here
	return $rewrite_rules;
}`

#### itw_abspath
Maybe you use roots.io/bedrock or other custom folder structure

`add_filter( 'itw_abspath', 'modify_itw_abspath', 10, 2 );
function modify_itw_abspath( $abspath ){
	return trailingslashit( WP_CONTENT_DIR );
}`

#### $images_to_webp->convert_image()
Maybe you want to automatically generate WebP for other plugins

`add_action( 'XXPLUGIN_image_created', 'XX_images_to_webp', 10, 2 );
function XX_images_to_webp( $image_path ){
	global $images_to_webp;
	$images_to_webp->convert_image( $image_path );
}`


== Installation ==

1. Upload `images-to-webp` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Plugin requirements =

It should work almost everywhere ;)
PHP 5.6 or higher
GD or Imagick extension with WebP support
Enabled server modules: `mod_mime`, `mod_rewrite`

= WebP images stored location =

WebP images are generated in same directory as original image. Example:
original img: `/wp-content/uploads/2019/11/car.png`
webp version: `/wp-content/uploads/2019/11/car.png.webp`

= How to get original image from the browser? =

Just add `?no_webp=1` to the URL and original JPG/PNG will be loaded

= How to check if plugin works? =

When you have installed plugin and converted all images, follow these steps:

1. Run `Google Chrome` and enable `Dev Tools` (F12).
2. Go to the `Network` tab click on `Disable cache` and select filtering for `Img` *(Images)*.
3. Refresh your website page.
4. Check list of loaded images. Note `Type` column.
5. If value of `webp` is there, then everything works fine.

= NGiNX and Apache together =

If you have some proxy setup or some other combination of NGiNX and Apache on your server, then probably .htaccess changes won't work and you will need to ask your hosting provider to disable NGiNX direct processing of image static files.

= Apache .htaccess =

Plugin should automatically update your .htaccess with needed rules.
In case it's not possible to write them automatically, screen with instructions will appear.
Anyway, here is how it should look like:

`<IfModule mod_mime.c>
	AddType image/webp .webp
</IfModule>

<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{HTTP_ACCEPT} image/webp
	RewriteCond %{REQUEST_FILENAME} "/"
	RewriteCond %{REQUEST_FILENAME} "\.(jpg|jpeg|png|gif)$"
	RewriteCond %{REQUEST_FILENAME}\.webp -f
	RewriteCond %{QUERY_STRING} !no_webp
	RewriteRule ^(.+)$ $1\.webp [NC,T=image/webp,E=webp,L]
</IfModule>`

= NGiNX config =

After you activate plugin, screen with instructions will appear.
Anyway, here is how it should look like:

You need to add this map directive to your http config, usually nginx.conf ( inside of the http{} section ):

`map $arg_no_webp $no_webp{
	default "";
	"1" "no_webp";
}

map $http_accept $webp_suffix{
	default "";
	"~*webp" ".webp";
}`

then you need to add this to your server block, usually site.conf or /nginx/sites-enabled/default ( inside of the server{} section ):

`location ~* ^/.+\.(png|gif|jpe?g)$ {
	add_header Vary Accept;
	try_files $uri$webp_suffix$no_webp $uri =404;
}`

= ISP Manager =

Are you using ISP Manager? Then it's probably not working for you, but no worries, you just need to go to `WWW domains` and delete `jpg|jpeg|png` from the `Static content extensions` field.

= Delete all generated WebP images =

There is no button to do that and it will also not delete generated WebPs automatically when you deactivate the plugin, but if you really need this, you can run some shell command to achieve this:

`find . -type f -name "*.webp" -exec bash -c 'if [ -f "${1%.webp}" ]; then echo "Deleting $1"; rm "$1"; fi' _ {} \;`

This will find all the files with a `.webp` extension and if there is similar file with the exact filename, but without the `.webp` extension, then it will delete it.



== Changelog ==

= 4.7 =
* Tested on WP 6.5

= 4.6 =
* Fix for "Find and convert MISSING images" button

= 4.5 =
* Tested on WP 6.4
* added FAQ section "Delete all generated WebP images"

= 4.4 =
* Tested on WP 6.3
* added FAQ section "NGiNX and Apache together"
* make configs error messages more descriptive

= 4.3 =
* Tested on WP 6.2
* new filter itw_abspath for WP installations with customized folder structure like Bedrock

= 4.2 =
* make convert old works also for local installations

= 4.1 =
* fix - convert also all subdirectories

= 4.0 =
* lazy load folders in convert tab
* make it works for local installations like XAMPP or Flywheel Local
* try-catch conversion errors
* updated jstree library

= 3.1 =
* add ?no_webp=1 to URL to receive original image content from Nginx server

= 3.0 =
* Tested on WP 6.1
* added support for Better image sizes plugin
* add ?no_webp=1 to URL to receive original image content - works only on Apache and only with direct image URL

= 2.0 =
* Tested on WP 6.0
* convert and serve WebP images anywhere - not only in wp-content folder
* option to delete original images after conversion

= 1.9.1 =
* Tested on WP 5.9

= 1.9 =
* Tested on WP 5.8
* added some nonce checks and more security validations
* better nginx instructions

= 1.8 =
* Tested on WP 5.7
* add more CURL options
* fix backslashes for localhosts

= 1.7 =
* Tested on WP 5.6
* fixed problem on some multisites

= 1.6 =
* Tested on WP 5.4
* added support for Fly Dynamic Image Resizer plugin

= 1.5 =
* notice when test image is not accessible

= 1.4 =
* new test method

= 1.3 =
* fixed text domain for translations

= 1.2 =
* added instructions for NGiNX

= 1.1 =
* make it works in multisite and subdirectory installs

= 1.0 =
* First version