=== Karmametrix Widget ===
Contributors: christiancannata
Tags: widget, CO2, environmental, shortcode
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.0
Stable tag: 1.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple plugin to add a Karmametrix widget container and script to the footer.

== Description ==
The Karmametrix Widget plugin lets you easily embed environmental tracking widgets on your WordPress site.

**Key features:**
- Add a Karmametrix widget with a customizable theme (light/dark).
- Manage your widget settings directly from the WordPress admin dashboard.
- Dynamically load the widget script only when needed.

== Installation ==

1. Download the plugin ZIP file.
2. Go to **Plugins > Add New** in your WordPress admin dashboard.
3. Click **Upload Plugin** and select the downloaded ZIP file.
4. Click **Install Now** and then **Activate** the plugin.

Alternatively, you can upload the plugin folder to the `wp-content/plugins/` directory and activate it from the Plugins menu.

== Usage ==

1. Go to **Settings > Karmametrix Widget** in the WordPress admin dashboard.
2. Enter your widget code and save the settings.
3. Use the `[karmawidget theme="light"]` shortcode in any post, page, or widget area.
4. Replace `theme="light"` with `theme="dark"` if you want a dark-themed widget.

== Frequently Asked Questions ==

= Can I customize the widget theme? =
Yes, you can use the `theme` attribute in the shortcode to specify either `light` or `dark`.

= What is the widget code? =
The widget code is a unique identifier provided by Karmametrix. You need to enter it in the plugin settings.

= Will the widget slow down my website? =
No, the script is dynamically loaded only when the `[karmawidget]` shortcode is present, ensuring minimal impact on performance.

== Screenshots ==

1. **Widget settings page in the admin dashboard**
   A screenshot of the settings page where you can enter your widget code.

2. **Light-themed widget example**
   A screenshot showing the widget with the light theme applied.

3. **Dark-themed widget example**
   A screenshot showing the widget with the dark theme applied.

== Changelog ==

= 1.2.1 =
* Improved compatibility with WordPress 6.3.
* Enhanced shortcode validation and error handling.

= 1.2 =
* Added support for dynamic themes in the widget shortcode.
* Improved shortcode validation and security.

= 1.1 =
* Added admin settings page for managing the widget code.
* Introduced the `[karmawidget]` shortcode.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.2.1 =
Update to improve compatibility with the latest WordPress version and handle shortcode errors gracefully.

== Additional Information ==

### Performance Optimization
The Karmametrix Widget plugin dynamically loads its script only when the `[karmawidget]` shortcode is present on a page or post. This ensures that your site's performance is not impacted unnecessarily.

### Custom Widget Theme
The widget supports two themes:
- `light`: Optimized for pages with light backgrounds.
- `dark`: Perfect for pages with dark or high-contrast designs.

### Admin Settings
Easily configure your widget's unique code through the **Karmametrix Widget Settings** page in the WordPress admin dashboard.

To access:
1. Go to **Settings > Karmametrix Widget**.
2. Paste your unique widget code and save it.

### Multilingual Support
The plugin is translation-ready and supports `.pot` files. You can translate the plugin to your preferred language using tools like [Poedit](https://poedit.net/) or the [Loco Translate](https://wordpress.org/plugins/loco-translate/) plugin.

### Security Measures
The plugin follows WordPress coding standards and implements:
- Data sanitization for user inputs.
- Nonces for verifying form submissions.
- Restricted script injection to prevent misuse.

### Plugin Requirements
- WordPress version: 5.0 or higher
- PHP version: 7.0 or higher
- Browser support: Modern browsers including Chrome, Firefox, Safari, and Edge.

### Feedback and Support
We value your feedback! If you encounter issues or have feature requests, please:
- Submit a support ticket via the [plugin's support page](https://wordpress.org/support/plugin/karmametrix-widget/).
- Contact the author directly at [support@karmametrix.com](mailto:support@karmametrix.com).

### Future Updates
Upcoming features planned for future releases:
- Enhanced widget customization options.
- Support for additional metrics and integrations.
- Compatibility with major page builders like Elementor and WPBakery.

== License ==

This plugin is licensed under the GPLv2 or later. See the license details at https://www.gnu.org/licenses/gpl-2.0.html.