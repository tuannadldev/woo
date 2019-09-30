<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function wpfm_compatibily_status() {

    if( class_exists( 'WooCommerce' ) ) {
        
        return 1;
        
    } else {
        
        return 0;
        
    }
    
//    if ( defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE== TRUE && is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
//        
//        return 1;
//        
//    } else if ( ( ! defined('WP_ALLOW_MULTISITE') && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) ) {
//
//        return 1;
//        
//    } else {
//
//        return 0;
//        
//    }
    
}