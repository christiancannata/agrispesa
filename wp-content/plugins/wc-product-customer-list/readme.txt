=== Product Customer List for WooCommerce ===
Contributors: kokomoweb, freemius
Tags: woocommerce, customer list, who bought, admin order list, product-specific, export customers to csv, email customers, customer list, customer, list, print, front-end, tickets, shows, courses, customers, shortcode
Requires at least: 5.0
Tested up to: 6.3.1
Stable tag: 3.1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a list of customers who bought a specific product at the bottom of the product edit page in WooCommerce and send them e-mails.

== Description ==

A plugin that simply displays a list of customers who bought a specific product at the bottom of the WooCommerce product edit page or as a shortcode. You can also send an email to the list of customers, print the list or export it as a CSV, PDF or Excel file. Requires WooCommerce 2.2+ to be installed and activated. 

Great for sending out e-mails or getting a list of customers for courses, for shows or for product recalls.

= Features: =

* Support for variable products
* Options page to select which info columns to display
* Displays customer name, email, phone number, address, order number, order date, shipping method, order total and quantity for each product
* Shortcode to display orders in the front-end. You can select which information to display using attributes
* Button to e-mail all customers for a specific product using your favorite e-mail client (b.c.c.)
* Email selected customers
* Export the customer list to CSV (great for importing into Mailchimp!)
* Export the customer list to Excel
* Export the customer list to PDF (choose your orientation and page size in the settings)
* Copy the customer list to clipboard
* Print the list of customers
* Search any column in the list
* Sort by any column in the list
* Drag and drop columns to reorder them
* Localized and WPML / Polylang ready (.pot file included)
* Included translations: French, French (France), French (Canada), Spanish, Dutch, Dutch (Netherlands), Dutch (Belgium).
* All functions are pluggable
* Performance oriented
* Responsive
* Multisite compatible
* Support for custom statuses
* Support for High Performance Order Storage (HPOS)

= Premium version: =

* Support for Custom Fields
* Support for User meta
* Support for WooCommerce Custom Fields (RightPress)
* Support for WooTours
* Support for WooEvents
* Support for YITH WooCommerce Product Add-ons
* Support for Conditional Woo Checkout Field Pro
* Support for Checkout Field Editor for WooCommerce (Themehigh)
* Support for WooCommerce Checkout Field Editor (WooCommerce)
* Support for WooCommerce Product Add-ons
* Support for WooCommerce Subscriptions (WooCommerce)
* Support for WooCommerce Product Bundles (WooCommerce)
* Shortcode by variation ID
* Datatables functionalities for the shortcode (export PDF, export CSV, print, email customers, search, paging, etc...).
* Change default sorting column
* Premium support
* Premium updates
* Much more coming soon!

To upgrade the plugin to the premium version, simply click on "upgrade" under the plugin title in the plugin list page, or [purchase it here](https://checkout.freemius.com/mode/dialog/plugin/2009/plan/2994/).

= Documentation = 
Please see documentation [here](https://www.kokomoweb.com/docs/).

= Contributors: =, freemius
* Support for variable products: [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/)
* Dutch translation: [pieterclaesen](https://wordpress.org/support/profile/pieterclaesen)
* Portuguese (Brazil) translation: [Marcello Ruoppolo](https://profiles.wordpress.org/mragenciadigital)

== Installation ==

1. Upload the plugin files to the "/wp-content/plugins/wc-product-customer-list" directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Edit any WooCommerce product to view the list of customers that bought it.
4. Make sure that the 'Product Customer List for WooCommerce’ checkbox is ticked in your screen options.
5. Access the settings page in WooCommerce / Settings / Products / Product Customer List


== Frequently Asked Questions ==

= How do I use the shortcode? =

Please see documentation [here](https://www.kokomoweb.com/docs/shortcodes-free-version/).

= Why doesn't the customer list appear when I edit a product? =

Make sure that the 'Product Customer List for WooCommerce’ checkbox is ticked in your screen options.

= Where can I select which columns to display =

You can access the settings page under WooCommerce -> Product Customer List

= How can I reorder the columns? = 

You can reorder the columns by dragging them and dropping them in the order you want. The browser will remember your selection. You can press the "Reset column order" button at any time to reset the order to its initial state.

= Available hooks and filters = 

Please see documentation [here](https://www.kokomoweb.com/docs/).

== Screenshots ==

1. The customer list in the product edit page.
2. The settings page.

== Changelog =

= 3.1.6 =
* Support for High Performance Order Storage (HPOS)
* Fix missing closing html tag in settings page.

= 3.1.5 =
* Updated freemius SDK

= 3.1.4 =
* Updated freemius SDK
* Added avatar column

= 3.1.3 =
* Fixes for WooCommerce subscription
* Bug fix: Removed echo in no customers function
* Put Index column back for free users
* Updated freemius SDK

= 3.1.2 =
* WooCommerce Subscriptions: changed billing period for billing interval instead (premium only).
* Fixed next payment error for WooCommerce Subscriptions.
* Added compatibility for WooCommerce Product Bundles (premium only).
* Added order tax total column.

= 3.1.1 =
* Changed default value of order_status to wc-completed and wc-processing.
* Compatibility with WooCommerce subscriptions (premium version only).

= 3.1.0 =
* Added option to select either comma or semicolon for email list when emailing customers.
* Updated freemius SDK.


= 3.0.9 =
* Changed wording when there are no customers so that users know where to change their settings.
* Added details on how to multiple select order statuses.

= 3.0.8 =
* Fix for order number url in admin.

= 3.0.7 =
* Display order number url in frontend if user has order edit capabilities.

= 3.0.6 =
* Fix get_current_screen fatal error.
* Add sorting functionalities to shortcode in free version.

= 3.0.5 =
* Fix defaults for pro and free versions.
* Fix issue with custom fields in premium version.
* Performance optimisation.

= 3.0.4 =
* Fix issue where shortcode defaults were ignored.

= 3.0.3 =
* More performance optimisations.
* Fix for order total quantity column settings.

= 3.0.2 =
* Performance optimisation

= 3.0.1 =
* Restored email and export  print capabilities for free users. Sorry guys!

= 3.0.0 =
* WARNING: Please test throughly on a staging site before pushing live. This is a major version.
* Re-wrote the shortcode system so it works with the same options as the admin version.
* New shortcode parameters (see documentation)
* New hooks (see documentation)
* Updated freemius SDK - Security fix
* Settings menu can now be found directly as a sub-item of the WooCommerce menu
* New snazzy visuals
* Updated .pot file

= 2.9.3 =
* Updated freemius SDK

= 2.9.2 =
* Fix BCC for emails

= 2.9.1 =
* Fix for email all customers

= 2.9.0 =
* Switched from REST to ajax
* Fixed partial refunds option
* Pro: Added User meta columns

= 2.8.9 =
* Fixed broken PDF button
* Added the IntersectionObserver API and a few extra class and method safety checks in PHP.

= 2.8.8 =
* Updated the Freemius SDK to remove an error that was introduced in WordPress 5.2
* Added missing PDFmake map file

= 2.8.7 =
* Fix WPML issues

= 2.8.6 =
* Fix quantity count
* Variation cleanup

= 2.8.5 =
* Fix missing e-mail button and total line
* Visual fixes

= 2.8.4 =
* Performance improvement: Orders on the backend now load using ajax
* Fixed issue with multiple products in shortcode

= 2.8.3 =
* Assets reupload

= 2.8.2 =
* Updated Freemius SDK to fix PHP notice.
* Removed CDN for chinese users
* Premium: fixed checkout field display issue

= 2.8.1 =
* Feature: Added option to add SKU to PDF titles

= 2.8.0 =
* Fixed issue with Wootours/Wooevents (premium)
* Fixed issue emailing all customers
* Security fix

= 2.7.9 =
* Fixed "Undefined variable: split_rows" PHP notice
* Premium: Added an index column for shortcode (coming soon for admin page)

= 2.7.8 =
* Fix for shortcode on WPML
* Add support for RightPress
* Add "split by row" option for RightPress

= 2.7.7 =
* Updated datatables to latest version
* Simplified the customer email selection
* Updated .pot file
* Updated freemius to the latest version
* Added setting to select the default column to order by (Pro)
* Added setting to enable/disable state save (Pro).

= 2.7.6 =
* Fixed unicode character related errors.

= 2.7.5 =
* Added customer_display_name in shortcode
* Added table_title in shortcode
* Added Customer display name column in admin
* Updated .pot file
* Premium: Added support for WooEvents
* Premium: Fixed issue with custom fields in shortcode
* Premium: Fixed issue with email_all in shortcode

= 2.7.4 =
* Updated .pot file and re-uploaded french files
* Freemius GDPR compliance
* Compatibility with YITH WooCommerce Product Add-ons

= 2.7.3 =
* Premium: Added function wpcl_product_sales($product, $status) to return actual sales.
* Free: Fixed variable column

= 2.7.2 =
* Fixed other bug with Freemius

= 2.7.1 =
* Fixed bug with Freemius
* Updated .pot file

= 2.7.0 =
* Fixed issue with billing email in shortcode
* Premium version: Added support for shortcode by variation ID.

= 2.6.9 =
* Fixed issue with settings page (again)

= 2.6.8 =
* Fixed issue with settings page

= 2.6.7 =
* Added support for Preemius / licensing system

= 2.6.6 =
* Added support for Pro version
* Added multiple hooks and filters (documentation to come)
* Added style for shortcode
* Added variations settings for admin
* Added variations settings for shortcode
* Updated shortcode documentation

= 2.6.5 =
* Fixed shameful PHP notice.

= 2.6.4 =
* Fixed duplicate order_status option in shortcode (please use order_status_column to display the order status column.
* Added a few more shortcode options (please see FAQ on how to use the shortcode).

= 2.6.3 =
* Returning shortcode output instead of echo (thanks to aerobass)

= 2.6.2 =
* Fixed rogue '</div>' at the end of the shortcode (thanks to aerobass)

= 2.6.1 =
* Added shortcode attributes for all columns

= 2.6.0 =
* Fixed compatibility bug in PHP 7.1 (Thanks to mmagnani)

= 2.5.9 =
* Added username column

= 2.5.8 =
* Fixed partially refunded orders

= 2.5.7 =
* Added billing company column
* Added shipping company column
* Added coupons used

= 2.5.6 =
* Added compatibility with Avada theme and The events calendar plugin
* Changed payment output to title instead of slug
* Added option to hide partially refunded orders

= 2.5.5 =
* Fixed datatables related javascript errors
* Added missing translation in settings page

= 2.5.4 =
* Fixed bug where some variations wouldn’t display (again!)

= 2.5.3 =
* Fixed bug where some variations wouldn’t display
* Added row selection for emails
* Added shipping method column
* Updated screenshots

= 2.5.2 =
* Added dropdown to select list length

= 2.5.1 =
* Added hook “wpcl_after_email_button” to display content after the email button.
* Fixed variation display.

= 2.5.0 =
* Fixed issue where the email list would be incomplete.

= 2.4.9 =
* Added support for custom statuses

= 2.4.8 =
* Fixed deprecation notices and bugs in variable products

= 2.4.7 =
* Script optimizations

= 2.4.6 =
* Fixed settings text mismatch

= 2.4.5 =
* Fixed bug where current date would be show instead of the order date
* Added plugin action links
* Added order total column
* Added translations for order statuses

= 2.4.4 =
* WooCommerce 3.0+ compatibility
* Script optimizations (thanks to [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/))
* Code optimization
* Improved multisite compatibility
* Updated .pot file

= 2.4.3 =
* Added Customer ID column
* Fixed wpdb notice (thanks to [Michal Bluma](https://profiles.wordpress.org/michalbluma))

= 2.4.2 =
* Fixed multisite compatibility

= 2.4.1 =
* Fixed compatibility issue with plugin “WooCommerce Amazon S3 storage”

= 2.4.0 =
* Added multisite compatibility

= 2.3.9 =
* Added the option for city in the settings

= 2.3.8 =
* Fixed bug where quantity would not show up in shortcode

= 2.3.7 =
* Added compatibility with WPML

= 2.3.6 =
* Fixed PDF orientation and size.
* Added payment method column and option.

= 2.3.5 =
* Added settings for PDF orientation and size.

= 2.3.4 =
* Fixed bug where refunds would appear in the list.
* Removed old unused code.

= 2.3.3 =
* Fixed trailing slash in scripts and stylesheet urls which could prevent them to load on certain servers.

= 2.3.2 =
* Fixed bug where featured image uploader wouldn’t work when activated.
* Updated PDFMake script to latest version (local)

= 2.3.1 =
* Added column reordering and state save
* Fixed javascript localization handling (wp_localize_script)

= 2.3.0 =
* Changed print and export system to reflect filters and order
* Added export to excel
* Added export to PDF
* Added copy to clipboard

= 2.2.9 =
* Added all missing order statuses in settings

= 2.2.8 =
* Fixed bug where shipping postal code wouldn’t be displayed in CSV export

= 2.2.7 =
* Fixed bug where two extra columns would appear while printing
* Fixed bug where there would be an error if you delete a variation after it is purchased

= 2.2.6 =
* Added Portuguese (Brazil) translation (thanks to [Marcello Ruoppolo](https://profiles.wordpress.org/mragenciadigital))
* Fixed alignment shortcode bug and added default product as current product

= 2.2.5 =
* Added support for variable products (thanks to [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/))
* Bug fixes & optimisation

= 2.2.4 =
* Fixed Urls for wordpress subdirectory installs

= 2.2.3 =
* Fixed issue where columns would shift when printing

= 2.2.2 =
* Added front-end shortcode
* Fixed default order type in settings

= 2.2.1 =
* Added date column
* Added compatibility with Wordpress 4.5
* Fixed some bugs

= 2.2.0 =
* Added settings tab section
* Added support for horizontal scrolling
* Loaded datatables CSS and JS via CDN

= 2.1.2 =
* Fixed undefined object error when there are no customers
* Fixed text domain to match plugin slug
* Added Dutch (Belgium) translation

= 2.1.1 =
* Fixed issue where the plugin would prevent WooCommerce from displaying or saving product attributes (price & stock)

= 2.1.0 =
* Added pagination
* Added search
* Added sortable columns
* Added Dutch (Netherlands) translation (thanks to [pieterclaesen](https://wordpress.org/support/profile/pieterclaesen))
* Added row actions
* Fixed empty table notice
* Cleaned code

= 2.0.4 =
* Fixed other “cannot send session cache limiter” warning 

= 2.0.3 =
* Fixed bug where variations wouldn’t be added to the quantity column sum

= 2.0.2 =
* Fixed “session_start(): Cannot send session cookie” warning
* Fixed “session_start(): Cannot send session cache limiter” warning

= 2.0.1 =
* Fixed quantity bug

= 2.0.0 =
* Added “export to CSV” button
* Added print button

= 1.11 =
* Improved table styling
* Added Spanish translation
* Optimized code: now even lighter files!

= 1.1 =
* Added quantity column
* Fixed and optimized WooCommerce plugin check
* Improved code readability
* Updated translations

= 1.02 =
* Fixed email button

= 1.01 =
* Updated deprecated WooCommerce order statuses
* Added pluggable functions
* Optimized code

= 1.0 =
* First stable version