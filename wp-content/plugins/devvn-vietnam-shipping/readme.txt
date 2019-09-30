=== Plugin Name ===
Contributors: levantoan
Tags: district shipping, shipping, tỉnh thành, quận huyện, tính phí ship cho quận huyện, viettel post, VTPost
Requires at least: 4.1
Requires PHP: 5.2.4
Tested up to: 4.9.7
Stable tag: 2.1.4

=========================================================

= Support hiển thị tên tỉnh thành; quận huyện; xã phường khi dùng plugin =
= Advanced Order Export For WooCommerce =
= https://wordpress.org/plugins/woo-order-export-lite/ =

add_filter('woe_get_order_value_billing_state', 'devvn_billing_state_format', 10, 3);
function devvn_billing_state_format($value, $order, $field){
    return vn_shipping()->get_name_city($value);
}

add_filter('woe_get_order_value_billing_city', 'devvn_billing_city_format', 10, 3);
function devvn_billing_city_format($value, $order, $field){
    return vn_shipping()->get_name_district($value);
}

add_filter('woe_get_order_value_billing_address_2', 'devvn_billing_address2_format', 10, 3);
function devvn_billing_address2_format($value, $order, $field){
    return vn_shipping()->get_name_village($value);
}

=========================================================

== Changelog ==

= 1.0.0 - 14.07.2018 =

* Ra mắt Plugin Master Shipping