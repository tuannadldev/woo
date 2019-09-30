=== Woo Title Limit ===
Contributors: DimaW
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X6JSPSAFCXJBW
Tags: woocommerce, product title, title, length, limit, shop
Requires at least: 3.0.1
Tested up to: 5.1
Stable tag: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set a limit for WooCommerce product titles at the frontend of your shop.

== Description ==
Easy set the maximum length of product titles for WooCommerce in the shop and category view. No more broked themes trough too long product titles. Useful for automatically added affiliate products.
= Features: =
* Set max. title length for the shop view
* Set max. title length for the product category view
* Set max. title length for the product view
* Optional: limit title at the end of the upcoming word instead of breaking the title
* Limit product titles in Woocommerce widgets automatically
* Add "..." if product titles are longer then the limit

== Installation ==

1. Upload the plugin directory to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Woo Title Limit screen to configure the plugin and set up your title limits.

== Frequently Asked Questions ==

Send me your questions to wtl@dimitri-wolf.de

== Screenshots ==
1. WooCommerce frontend without a limit for product titles.
2. WooCommerce frontend with a limi for product titles using Woo Title Limit.
3. Woo Title Limit easy to use settings page.

== Changelog ==
= 1.4.4 =
* fix: undefined notice

= 1.4.3 =
* fix: version

= 1.4.2 =
* fix: undefined notices
* tested for wordpress 5.1

= 1.4.1 =
* fix: not working on home page
* tested for wordpress 4.9.4

= 1.4 =
* added: product title settings for home page
* tested: woocommerce 3.0.9

= 1.3 =
* tested: wordpress 4.8
* seperate css to files
* bugfixes

= 1.2.2 =
* tested: wordpress 4.7.1

= 1.2.1 =
* fixed: no title output for Woocommerce shortcodes

= 1.2 =
* added: input fields now required
* added: option to limit title by end of the upcoming word instead of breaking the word at limit
* added: option for automatically limit product titles in Woocommerce widgets (beta)
* added: uninstall routine - after deleting the plugin at backend, plugin options in database deleted too
* updated: translation and languages directory
* fixed: error selecting right post type
* fixed: "..." only added if the title contains more characters then the character limit

= 1.1.1 =
* fixed: error in v1.1

= 1.1 =
* added: new option to add "..." at the end of a shortened title
* added: new tags
* added: better description of the plugin
* added: new screenshot showing the new option
* fixed: translation/spelling errors in description and option window


= 1.0.3 =
* fixed: a php error message at frontend if Woocommerce isnt activated or installed
* added: warning message at Woo Title Limit options page if Woo Title Limit is activated but WooComerce still not installed or activated
* added: text-domain and domain-path to plugin header for localization support
* added: some comments to plugin code for better unterstanding
* added: screenshots, logo (icon) and banner to wordpress.org plugins page

= 1.0.2 =
* fixed: (again) readme and description errors

= 1.0.1 =
* fixed: readme and description errors

= 1.0 =
* Plugin release
