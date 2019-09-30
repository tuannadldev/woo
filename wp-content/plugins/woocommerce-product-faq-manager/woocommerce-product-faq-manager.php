<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
Plugin Name: WooCommerce Product Faq Manager
Plugin URI: http://codecanyon.net/item/woocommerce-product-faq-manager/8011992?ref=xenioushk
Description: BWL WooCommerce Product Faq Manager provide you a great way to create unlimited no of faq questions and answers for WooCommerce product. You can easily add unlimited no of FAQ's in product tab. Super easy and flexible interface gives you great user experience to create FAQ items.
Author: Md Mahbub Alam Khan
Version: 1.1.9
WP Requires at least: 4.3+
Author URI: http://www.bluewindlab.net
Text domain:bwl-wpfm
*/

 
Class BWL_Woo_Faq_Manager{
    
    function __construct() {
        
         /*------------------------------ PLUGIN COMMON CONSTANTS ---------------------------------*/
        define( "BWL_WPFM_PLUGIN_TITLE", 'WooCommerce Product Faq Manager');
        define( "BWL_WPFM_PLUGIN_DIR", plugins_url() .'/woocommerce-product-faq-manager/' );
        define( "BWL_WPFM_PLUGIN_VERSION", '1.1.9');
        
        // Call Immediatly Initialized.      
        include_once dirname(__FILE__) . '/includes/bwpfm-check-compatibility.php';
        
        $wpfm_compatibily_status = wpfm_compatibily_status();
        
        if ( $wpfm_compatibily_status == 1 ) {            
            
            // Announce Notice For FAQ Collector - Addon For Product Faq Manager 
  
            if( ! class_exists( 'BWL_Wpfm_Fc_Addon' ) ) {
                add_action('admin_notices', array( $this, 'wpfm_recom_addon_notice' ) );
                add_action('admin_init', array( $this, 'wpfm_recom_ignore_admin_notice' ) );
            }
        
            // Introducing Global QA in version 1.0.1
            
            $this->register_post_type();
            $this->taxonomies();
            $this->included_files();
            $this->enqueue_plugin_scripts();
            
            //@Description: Load Only Admin Scripts.
            //@Since: 1.0.6
            add_action('admin_enqueue_scripts', array( $this, 'wpfm_admin_load_scripts' ));
            
            $this->wpfm_cau();
            
        } else {
            
            $this->wpfm_compatibily_notice();
            
        }
        
    }
    
    /* Display a notice that can be dismissed */

    function wpfm_recom_addon_notice() {

        global $current_user;
        $user_id = $current_user->ID;

        $location = $_SERVER['HTTP_REFERER'];

        /* Check that the user hasn't already clicked to ignore the message */
        if (!get_user_meta($user_id, 'wpfm_recom_addon_notice')) {
            echo '<div class="notice notice-info"><p>';
            printf(__('<b>Recommendation: </b>Try <a href="http://codecanyon.net/item/faq-collector-addon-for-product-faq-manager/9992576?ref=xenioushk" target="_blank">FAQ Collector - Addon For Product Faq Manager</a></b> to collect questions directly from users. <a href="%1$s"  class="page-title-action" style="position: relative; top: 1px;"><strong>No Thanks</strong></a>'), '?wpfm_recom_ignore_admin_notice=0&location=' . $location);
            echo "</p></div>";
        }
    }

    function wpfm_recom_ignore_admin_notice() {
        global $current_user;
        $user_id = $current_user->ID;
         
        /* If user clicks to ignore the notice, add that to their user meta */
        if (isset($_GET['wpfm_recom_ignore_admin_notice']) && '0' == $_GET['wpfm_recom_ignore_admin_notice']) {
            add_user_meta($user_id, 'wpfm_recom_addon_notice', 'true', true);
            wp_safe_redirect($_GET['location']);
            exit();
        }
    }

    function wpfm_requirement_admin_notices() {
        echo '<div class="updated"><p>You need to download & install <b><a href="http://downloads.wordpress.org/plugin/woocommerce.zip" target="_blank">WooCommerce Plugin</a></b> to use <b>Woo Commerce Product FAQ Manager</b> !</p></div>';
    }
    
    function wpfm_compatibily_notice() {

        add_action('admin_notices', array( $this, 'wpfm_requirement_admin_notices') );
        
    }
    
    public function wpfm_cau(){
        
        $bwpfm_data = get_option('bwpfm_options');
        
        if( is_admin() ) {
        
           include_once dirname(__FILE__) . '/includes/bwpfm-update-notifier.php';  // INTEGRATE AUTO UPDATER [VERSION: 1.0.2]
        
        }
        
        
    }
    
    function included_files() {
        
         /*------------------------------ INTEGRATE SHORTCODE   ---------------------------------*/

        include_once dirname(__FILE__) . '/includes/wpfm-filters.php';
        include_once dirname(__FILE__) . '/shortcode/wpfm-shortcodes.php';
       include_once dirname(__FILE__) . '/includes/bwpfm-custom-theme.php';
        
        if( is_admin() ) {
            
            include_once dirname(__FILE__) . '/includes/wpfm-cmb-framework/wpfm-custom-meta-box.php';
            include_once dirname(__FILE__) . '/includes/bwpfm-fsa-sorting.php';
            include_once dirname(__FILE__) . '/includes/bwpfm-custom-column.php';
            include_once dirname(__FILE__) . '/includes/bwpfm-quick-edit.php';
            include_once dirname(__FILE__) . '/includes/bwpfm-global-faq-quick-edit.php';
            include_once dirname(__FILE__) . '/option-panel/plugin-option-panel-settings.php';
            include_once dirname(__FILE__) . '/option-panel/wpfm-admin-settings.php';
            
        } else {
            
            $wpfm_display_faq = 1;

            if ($wpfm_display_faq == 1) {

                add_filter('woocommerce_product_tabs', array($this, 'bwpfm_add_custom_product_tab'));
            }
            
        }
        
    }
    
    function enqueue_plugin_scripts(){
        
        $bwpfm_data = get_option('bwpfm_options');
        
         /*------------------------------ Load Custom Styles ---------------------------------*/
        
        if( ! is_admin() ){
        
            wp_register_style( 'wpfm-accordion-styles' , plugins_url( 'css/bwl_accordion.css' , __FILE__ ), array(), BWL_WPFM_PLUGIN_VERSION );
            wp_register_style( 'wpfm-animate-styles' , plugins_url( 'css/animate.css' , __FILE__ ), array(), BWL_WPFM_PLUGIN_VERSION );
            wp_register_style( 'wpfm-font-awesome-styles' , plugins_url( 'css/font-awesome.min.css' , __FILE__ ), array(), BWL_WPFM_PLUGIN_VERSION );
            
            //@ Load Plugin Required Stylesheet
             wp_enqueue_style( 'wpfm-accordion-styles' );
             wp_enqueue_style( 'wpfm-animate-styles' );

             //@Load Font-Awesome Stylesheet.
             if( ! isset( $bwpfm_data['bwpfm_load_font_awesome'] ) || $bwpfm_data['bwpfm_load_font_awesome'] == 1 ) {
               wp_enqueue_style( 'wpfm-font-awesome-styles');
             }

        }
        
        //@Load RTL Stylesheet.
        if (is_rtl()) {
            wp_register_style('wpfm-rtl-styles', plugins_url('css/wpfm-rtl.css', __FILE__), array(), BWL_WPFM_PLUGIN_VERSION); // Since Version 1.0.4
            wp_enqueue_style('wpfm-rtl-styles');
        }

        /*------------------------------ Load Custom Scripts ---------------------------------*/
        
        if( ! is_admin() ){
            
            wp_register_script( 'wpfm-highlight-regx-script', plugins_url( 'js/highlight-regx.js' , __FILE__ ) , array( 'jquery'), BWL_WPFM_PLUGIN_VERSION, TRUE );
            wp_register_script( 'wpfm-bwl-wpfm-script', plugins_url( 'js/bwl_accordion.js' , __FILE__ ) , array( 'jquery'), BWL_WPFM_PLUGIN_VERSION, TRUE );
        
        }
        
    }
    
    //@Load Only Admin Panel Scripts.
    
    // @Description: Load Plugin Admin Panel Required Custom JS & CSS Files.
        // @Since: version 1.0.0

        function wpfm_admin_load_scripts( $hook ) {

            // Check point To Load JS & CSS files in admin.
            // We only load Plugin required JS & CSS files where it acutally required.

           $current_post_type = "";         

           if ( get_post_type() == "bwl-woo-faq-manager") {
               
               $current_post_type = "bwl-woo-faq-manager";
               
           } else if (isset( $_GET['post_type'] ) && $_GET['post_type'] == "product") {

                $current_post_type = "product";


            } else if ( isset($_GET['post'] ) && get_post_type( $_GET['post'] ) === 'product' ) {

                 $current_post_type = "product";

             } else {

                  $current_post_type = "";
             }

            // Load live Font Awesome icon changes only in product pages.
             
            

            if ( $current_post_type == "product" ) {
                
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_style( 'wpfm-admin-custom-styles' , plugins_url( 'css/admin_custom_css.css' , __FILE__ ) );
                wp_register_script( 'wpfm-admin-custom-scripts', plugins_url( 'js/admin-custom-scripts.js' , __FILE__ ) , array( 'jquery', 'jquery-ui-core'), BWL_WPFM_PLUGIN_VERSION, TRUE );
                wp_enqueue_script( 'wpfm-admin-custom-scripts' );
                wp_register_script( 'wpfm-quick-bulk-edit-scripts', plugins_url( 'js/wpfm-quick-bulk-edit.js' , __FILE__ ) , array( 'jquery'), BWL_WPFM_PLUGIN_VERSION, TRUE );
                wp_enqueue_script( 'wpfm-quick-bulk-edit-scripts' );

            } else if ( $current_post_type == "bwl-woo-faq-manager" ) {
                
                wp_register_script( 'wpfm-global-faq-quick-bulk-edit-scripts', plugins_url( 'js/wpfm-global-faq-quick-bulk-edit.js' , __FILE__ ) , array( 'jquery'), BWL_WPFM_PLUGIN_VERSION, TRUE );
                wp_enqueue_script( 'wpfm-global-faq-quick-bulk-edit-scripts' );
                
            } else {
                
                // Do nothing.
                
            }
            
            

        }
    
    
    function bwpfm_add_custom_product_tab($tabs){
        
        global $product;
        
        $bwpfm_data = get_option('bwpfm_options');
        
        $bwpfm_faq_tab_title = __( "FAQ ", 'bwl-wpfm');
        
         if( isset( $bwpfm_data['bwpfm_faq_tab_title'] ) && $bwpfm_data['bwpfm_faq_tab_title'] != "" ) {
        
            $bwpfm_faq_tab_title = trim( $bwpfm_data['bwpfm_faq_tab_title'] );
        
        }
        
        $bwpfm_faq_tab_position = 100; // Set faq tab in last position.
        
        if( isset( $bwpfm_data['bwpfm_faq_tab_position'] ) && is_numeric( $bwpfm_data['bwpfm_faq_tab_position'] ) ) {
        
            $bwpfm_faq_tab_position =  trim( $bwpfm_data['bwpfm_faq_tab_position'] );
        
        }
        
        
        $wpfm_display_faq_status = get_post_meta( $product->get_id(), 'wpfm_display_faq', true );
        
        if ( isset($wpfm_display_faq_status) && $wpfm_display_faq_status == 1 ) {
        
            return $tabs;
        
        }
        
        
        //@Description: Search box show/hide
        //@Since: Version 1.0.9
        
        // @Description: Set Question Details Status & Details Minimum Length.
        // @Since: Version 1.1.2.
        
        $wpfm_sbox_status = 1;
        $wpfm_min_faqs_for_sbox  = 5;
        
         if( isset( $bwpfm_data ['wpfm_search_box_conditinal_fields']['enabled']) && $bwpfm_data['wpfm_search_box_conditinal_fields']['enabled'] == 'on' ){
             
             $wpfm_sbox_status = 0;
             
              if( isset( $bwpfm_data['wpfm_search_box_conditinal_fields']['wpfm_min_faqs_for_sbox'] ) && 
                $bwpfm_data['wpfm_search_box_conditinal_fields']['wpfm_min_faqs_for_sbox'] != "" && 
                is_numeric( $bwpfm_data['wpfm_search_box_conditinal_fields']['wpfm_min_faqs_for_sbox'] ) ){

                    $bwpfm_total_faqs = ( int ) count( apply_filters('wpfm_process_meta_info', $product->get_id()) ) ;
                    $wpfm_min_faqs_for_sbox  = $bwpfm_data['wpfm_search_box_conditinal_fields']['wpfm_min_faqs_for_sbox'];
                    
                    //Checking the conditions.
                    // If total faqs is greater than or equal to the minimum faq then we display search box.
                    
                    $wpfm_sbox_status = ($bwpfm_total_faqs >= $wpfm_min_faqs_for_sbox ) ? 1 : 0;

               }
         
         }
        
        
        
//        $wpfm_total_faqs_string = '<span class="wpfm_faq_counter"></span>';
        $wpfm_total_faqs_string = '';

        $tabs['bwpfm_tab'] = array(
            'title' => $bwpfm_faq_tab_title. $wpfm_total_faqs_string,
            'priority' => $bwpfm_faq_tab_position, // Always display at the end of tab :)
            'callback' => array($this, 'bwpfm_custom_tab_panel_content'),
            'content' => do_shortcode('[bwpfm_faq product_id="'.$product->get_id().'" sbox='.$wpfm_sbox_status.' /]') // custom field
        );

        return $tabs;
        
    }
 
    function bwpfm_custom_tab_panel_content( $key, $tab ) {

        // allow shortcodes to function
        $content = apply_filters( 'the_content',  $tab['content']);
        $content = str_replace( ']]>', ']]&gt;', $content );
//        echo apply_filters( 'woocommerce_custom_product_tabs_lite_heading', '<h2>' . $tab['title'] . '</h2>', $tab );
        echo apply_filters( 'woocommerce_custom_faq_tab_content', $content, $tab );
        
    }
    
    
    /*------------------------------ Define Custom Post Type  ---------------------------------*/
    
    function register_post_type() {
        
        /*
         * Custom Slug Section.
         */        
        
        $wpfm_options = get_option('wpfm_options');
        
        $wpfm_custom_slug = "bwl-wpfm";
        
        $labels = array(
            'name'                         => __('Global FAQs', 'bwl-wpfm'),
            'singular_name'            => __('Global FAQ', 'bwl-wpfm'),
            'add_new'                    => __('Add New FAQ', 'bwl-wpfm'),
            'add_new_item'           => __('Add New FAQ', 'bwl-wpfm'),
            'edit_item'                   => __('Edit FAQ', 'bwl-wpfm'),
            'new_item'                  => __('New FAQ', 'bwl-wpfm'),
            'all_items'                    => __('Global FAQs', 'bwl-wpfm'),
            'view_item'                  => __('View FAQs', 'bwl-wpfm'),
            'search_items'             => __('Search FAQs', 'bwl-wpfm'),
            'not_found'                  => __('No FAQ found', 'bwl-wpfm'),
            'not_found_in_trash'    => __('No FAQs found in Trash', 'bwl-wpfm'),
            'parent_item_colon'     => '',
            'menu_name'              => __('BWL Woo FAQ', 'bwl-wpfm')
        );
        

        $args = array(
            'labels'                       => $labels,
            'query_var'                => 'bwl_wpfm',    
            'show_in_nav_menus' => true,
            'public'                       => true,        
            'show_ui'                   => true,
            'show_in_menu'         => true,
            'rewrite'                     => array(
                                                 'slug' => $wpfm_custom_slug
                                                ),
            'publicly_queryable'     => true,
            'capability_type'          => 'post',
            'has_archive'              => true,
            'hierarchical'               => false,
            'show_in_admin_bar'  => true,
            'supports'                   => array('title', 'editor'),
            'menu_icon'                => BWL_WPFM_PLUGIN_DIR . 'images/wpfm_menu_icon.png'
        );        
      
      
        register_post_type('bwl-woo-faq-manager', $args); // text domian
        
    }
    
    function taxonomies() {

        /*
         * Custom Slug Section.
         */        
        
        $wpfm_options = get_option('wpfm_options');
        
        $wpfm_custom_slug = "bwl-wpfm";
        
        if( isset($wpfm_options['wpfm_custom_slug']) && $wpfm_options['wpfm_custom_slug'] != "" ) {
        
            $wpfm_custom_slug = trim( $wpfm_options['wpfm_custom_slug'] );

        }
        
        $taxonomies = array();  
        
        $this->register_all_taxonomies($taxonomies);
        
    }
    
    function register_all_taxonomies($taxonomies) {
        
        foreach ($taxonomies as $name=> $arr) {
            register_taxonomy($name, array('bwl-woo-faq-manager'), $arr);
        }
        
    }
    
}

/*------------------------------ Initialization ---------------------------------*/

function init_bwl_woo_faq_manager() {
    new BWL_Woo_Faq_Manager();
}

add_action('init', 'init_bwl_woo_faq_manager');


/*------------------------------  TRANSLATION FILE ---------------------------------*/

load_plugin_textdomain('bwl-wpfm', FALSE, dirname(plugin_basename(__FILE__)) . '/lang/');