<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/***********************************************************************************************/
/* Menus */
/***********************************************************************************************/

function wpfm_add_options_page() {
    
    add_submenu_page(
            'edit.php?post_type=bwl-woo-faq-manager',             //sub page to settings page
            __( 'About BWL Woo FAQ Manager', 'bwl-wpfm'), // The Text to be display in bte browser bar.
            __( 'Plugin Info', 'bwl-wpfm'), // The Text to be display in bte browser bar.
            'manage_options', // permission
            'bwl-woo-faq-manager-welcome', //slug
            'bwl_woo_faq_options_display' //callback
            );
    
    add_submenu_page(
            'edit.php?post_type=bwl-woo-faq-manager',             //sub page to settings page
            __( 'About BWL Woo FAQ Manager', 'bwl-wpfm'), // The Text to be display in bte browser bar.
            __( 'Plugin Add-ons', 'bwl-wpfm'), // The Text to be display in bte browser bar.
            'manage_options', // permission
            'bwl-woo-faq-manager-addon', //slug
            'bwl_woo_faq_addon_page_display' //callback
            );
    
}


function bwl_woo_faq_options_display() {

    require_once 'welcome-page.php';
}

function bwl_woo_faq_addon_page_display() {

    require_once 'wpfm-addon.php';
}

add_action('admin_menu', 'wpfm_add_options_page');