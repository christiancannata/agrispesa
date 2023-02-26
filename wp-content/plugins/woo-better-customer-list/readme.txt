=== Better Customer List for WooCommerce ===
Contributors: blazeconcepts
Donate link: https://www.paypal.me/blazeconcepts
Tags: woocommerce, better, customer, list, user, management, data, status, orders, report, value
Requires at least: 4.7
Tested up to: 5.5.1
Stable tag: 1.2.2
WC requires at least: 2.5.5
WC tested up to: 4.6.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Displays summarised WooCommerce order information by individual customers in a more friendly table view.

== Description ==

Better Customer List for WooCommerce improves on the existing customer list provided by WooCommerce (under Reports) which is painfully slow loading when your customer database gets large. This plugin is fast loading and allows you to view order history by customer.

= Table information includes: =
* **Customer Name** - The customer's full name
* **Customer Status** - Displays if the customer has been active during a period of time. See below on how to change this time period.
* **Customer Email** - The customer's email address
* **Avg Order Rate** - Gives an average of how often the customer orders from the website
* **Last Order** - The last order made by the customer with a link to the order
* **Total Orders** - The number of orders the customer has made since the beginning
* **Total Spend** - The total spent by the customer since the beginning

Order the results by column on Full Name, Average Order Rate, Total Orders, and Total Spend. Further refine the results by filtering customers by Active/Inactive (for your specified last order time period), Average Order Rate Set/Not Set and Ordered/Not Ordered.

The Total Orders and Total Spend fields are calculated after page load via AJAX to ensure the table loads quickly. This is essential for websites with large customer databases. This has been tested on sites with over 25,000 customers and is capable of handling more.

= Set Customer Status Time Period =
The Customer List states whether the customer is Active or Inactive based on their last order made. The default time period used to set this status is 31 days. You can change this period by:

1. From the WordPress Admin Dashboard go to WooCommerce -> Settings
2. Click the Better Customer List tab.
3. Type in the number of days for the system to check against and save the settings. Leave the field blank to use the default setting.

== More features coming soon ==
* Filtering by user role (not just 'Customer')
* Filtering by 'Guest' users
* Advanced search
* Export current results to CSV file

Install now to ensure you don't miss out on these features when added to future updates!

== Installation ==
**Better Customer List for WooCommerce** requires the [WooCommerce](https://wordpress.org/plugins/woocommerce/ "WooCommerce") plugin (at least version 2.5.5) to be installed. Tested from WooCommerce 2.5.5 to 3.6.5.

= Via WordPress =
1. From the WordPress Dashboard, go to Plugins > Add New
2. Search for 'Better Customer List for WooCommerce' and click Install. Then click Activate.
3. Click 'Customers' in the Admin sidebar to see the Customer List.

= Manual =
1. Upload the folder /woo-better-customer-list/ to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click 'Customers' in the Admin sidebar to see the Customer List.

== Frequently Asked Questions ==

= How do I set the Customer Status time period? =

1. From the WordPress Admin Dashboard go to WooCommerce -> Settings
2. Click the Better Customer List tab.
3. Type in the number of days for the system to check against and save the settings. Leave the field blank to use the default setting.

== Screenshots ==

1. Customer List table
2. Calculating the Customer List table
3. Set the Customer Status time period

== Changelog ==

= 1.0 =
* Initial release

= 1.1 =
* Add column ordering
* Add customer filtering

= 1.1.1 =
* Fix pagination issue with filtered results

= 1.1.2 =
* Small tweak to filtering query

= 1.1.3 =
* Fix for undefined variable error

= 1.1.4 =
* Compatibility updates and review link

= 1.1.5 =
* Fix for compatibility with custom WordPress prefixes

= 1.2.0 =
* Adding languages

= 1.2.1 =
* Fix from adding languages

= 1.2.2 =
* Compatibility version updates and name change