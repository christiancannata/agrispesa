=== URL Coupons for WooCommerce ===
Contributors: wpcodefactory, algoritmika, anbinder, Karzin
Tags: woocommerce, coupons, url coupons, woo commerce
Requires at least: 4.4
Tested up to: 6.1
Stable tag: 1.6.7
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Let your customers apply standard WooCommerce discount coupons via URL.

== Description ==

**URL Coupons for WooCommerce plugin** allows your customers to apply coupons via URL.

= How it Works? =

After you install and activate plugin, all your shop's standard coupons can be applied by your customers by visiting:

`
http://example.com/?apply_coupon=couponcode
`

You can **customize** `apply_coupon` key in plugin's settings.

Also you can optionally **hide standard coupon input field** on the cart and checkout pages, so coupons could be applied by URL only.

= Premium Version =

With [URL Coupons for WooCommerce Pro](https://wpfactory.com/item/url-coupons-woocommerce/) you can:

* Automatically **add coupon's products to the cart** for "Fixed product discount" coupons.
* Set **redirect URL** (redirect to cart, redirect to checkout, redirect to custom URL, set redirect URL per coupon).
* Add **custom notices** (notice per coupon, override the default "Coupon code applied successfully" notice, etc.)

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/url-coupons-woocommerce/).

== Frequently Asked Questions ==
= What can I do if the coupon is not being applied as it should? =
Please try to change one option below at a time and test again:
-  Change **Advanced > Main hook > Hook** option.
-  Set **Advanced > Data storage type** as **Cookie**.
-  Enable **Advanced > Javascript reload** option.

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > URL Coupons".

== Changelog ==

= 1.6.7 - 02/03/2023 =
* Dev - General - New option: Hide coupon field condition.
* WC tested up to: 7.4.
* Tested up to: 6.1.

= 1.6.6 - 13/09/2022 =
* Fix - General - Delay coupon options - Coupon only gets applied if the first product added to cart matches the coupon rules.
* WC tested up to: 6.9.

= 1.6.5 - 19/08/2022 =
* Fix - Improve the way the cookie is removed.
* WC tested up to: 6.8.
* Tested up to: 6.0.

= 1.6.4 - 25/04/2022 =
* Dev - Advanced - Add "Data storage type" option.
* Dev - Advanced - Main hook - Add hook option.
* Dev - Advanced - Add "Javascript reload" option.
* Dev - General - Add "Force session start earlier and everywhere" option.
* Dev - Add `alg_wc_url_coupons_apply_url_coupon_validation` filter.
* Dev - Add `alg_wc_url_coupons_keys_to_remove_on_redirect` filter.
* WC tested up to: 6.4.
* Tested up to: 5.9.
* Add deploy script.

= 1.6.3 - 20/01/2022 =
* Dev - Shortcodes - `[alg_wc_url_coupons_print_notices]` - Now checking if `wc_print_notices()` function exists.
* WC tested up to: 6.1.

= 1.6.2 - 09/12/2021 =
* Dev - Advanced - "Payment request buttons: Apply coupons on single product pages" options added ("WooCommerce Stripe Gateway" and "WooCommerce Payments") (defaults to `no`).

= 1.6.1 - 24/11/2021 =
* Dev - Advanced - "Save on empty cart" option added.
* WC tested up to: 5.9.

= 1.6.0 - 09/08/2021 =
* Dev - Delay coupon - "Delay on non-empty cart" option added.
* Dev - Force session start - Defaults to `yes` now.
* Dev - Admin settings rearranged - New sections added: "Notices", "Advanced". New subsection added: "Delay Coupon Options".
* Dev - Admin settings descriptions updated.
* Dev - Plugin initialized on the `plugins_loaded` action now.
* Dev - Code refactoring.

= 1.5.5 - 30/07/2021 =
* Fix - Possible fatal PHP error on admin widgets page fixed.
* WC tested up to: 5.5.
* Tested up to: 5.8.

= 1.5.4 - 20/04/2021 =
* Dev - `[alg_wc_url_coupons_translate]` shortcode added (for WPML and Polylang translations).
* Dev - Minor settings restyling.
* WC tested up to: 5.2.

= 1.5.3 - 07/04/2021 =
* Dev - General Options - Delay coupon - "Check product" option added.

= 1.5.2 - 07/04/2021 =
* Dev - General Options - Delay coupon - Redirect action is now moved to the initial coupon application function `Alg_WC_URL_Coupons_Core::apply_url_coupon()`.
* Dev - `alg_wc_url_coupons_coupon_applied` action added.
* Dev - Code refactoring.

= 1.5.1 - 06/04/2021 =
* Dev - General Options - Delay coupon - Filter priority increased.

= 1.5.0 - 06/04/2021 =
* Dev - General Options - "Delay coupon" options added.
* WC tested up to: 5.1.
* Tested up to: 5.7.

= 1.4.0 - 12/01/2021 =
* Dev - General Options - Add products to cart - "Empty cart" option added.
* Dev - "Notice" options added ("Custom notice", "Notice per coupon", "Notice method", "Override default notice", etc.). "Delay notice" option moved to this new section.
* Dev - Notice Options - Delay notice - Now delaying notice only if coupon was successfully applied.
* Dev - Notice Options - `[alg_wc_url_coupons_print_notices]` shortcode added.
* Dev - Redirect Options - "Redirect URL per coupon" option added.
* Dev - Redirect Options - Now redirecting only if coupon was successfully applied.
* Dev - Localisation - `load_plugin_textdomain` moved to the `init` action.
* Dev - Admin settings restyled. Subsections added.
* Dev - Code refactoring.
* WC tested up to: 4.9.

= 1.3.2 - 22/12/2020 =
* Dev - Advanced - "Force coupon redirect" option added.
* Tested up to: 5.6.
* WC tested up to: 4.8.

= 1.3.1 - 28/10/2020 =
* Dev - Advanced - "Hook priority" option added.
* Dev - Advanced - "Remove 'add to cart' key" option added.
* WC tested up to: 4.6.

= 1.3.0 - 09/10/2020 =
* Dev - Code refactoring.

= 1.2.8 - 10/09/2020 =
* Dev - Advanced - "WP Rocket: Disable empty cart caching" option added.
* WC tested up to: 4.5.

= 1.2.7 - 07/09/2020 =
* Dev - "Extra cookie" options added.

= 1.2.6 - 31/08/2020 =
* Fix - Delay notice - Checking for empty cart now.
* Dev - Code refactoring.

= 1.2.5 - 31/08/2020 =
* Dev - "Delay notice" option added.

= 1.2.4 - 31/08/2020 =
* Dev - `alg_wc_url_coupons_before_coupon_applied` and `alg_wc_url_coupons_after_coupon_applied` actions added.
* Dev - Code refactoring.

= 1.2.3 - 20/08/2020 =
* Dev - "Force session start" option added.
* Tested up to: 5.5.
* WC tested up to: 4.4.

= 1.2.2 - 02/04/2020 =
* Dev - Removing `add-to-cart` query argument on redirect now.

= 1.2.1 - 01/04/2020 =
* Dev - "Hide coupon on checkout page" option added.
* Tested up to: 5.4.

= 1.2.0 - 27/03/2020 =
* Fix - "Reset settings" admin notice fixed.
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.0.
* Tested up to: 5.3.

= 1.1.0 - 27/07/2019 =
* Dev - Code refactoring.
* Dev - Admin settings - Settings restyled; descriptions updated; "Your settings have been reset" notice added.
* Localisation domain name fixed.
* Plugin URI updated.
* WC tested up to: 3.6.
* Tested up to: 5.2.

= 1.0.0 - 15/08/2017 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
