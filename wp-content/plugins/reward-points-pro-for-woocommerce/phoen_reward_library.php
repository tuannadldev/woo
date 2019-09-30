<?php if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head','phoen_rewpts_frontend_func_date_picker');

function phoen_rewpts_frontend_func_date_picker(){
	
	include_once(PHOEN_REWPTSPLUGPATH.'includes/phoen_rewpts_frontend.php');
	
	wp_enqueue_style( 'phoen_reward_datepick_css', PHOEN_REWPTSPLUGURL. "assets/css/datetimepicker.css" );	
	
	wp_enqueue_script( 'phoen_reward_dateadmin_js', PHOEN_REWPTSPLUGURL. "assets/js/phoen_datetimepicker.full.min.js" );
	
	wp_enqueue_script( 'phoen_reward_bobfrontends', PHOEN_REWPTSPLUGURL. "assets/js/phoen_reward_bobfrontend.js" );

}	

add_action('admin_head','phoen_rewpts_backend_func');

function phoen_rewpts_backend_func(){
		
	wp_enqueue_style( 'phoen_rewpts_backend_func_css', PHOEN_REWPTSPLUGURL. "assets/css/phoen_rewpts_backend.css" );	
	
	wp_enqueue_script( 'phoen_rewpts_backend_func_js', PHOEN_REWPTSPLUGURL. "assets/js/phoen_rewpts_backend.js" );	
	
	wp_enqueue_style( 'wp-color-picker');
	
	wp_enqueue_script( 'wp-color-picker');
	
	wp_enqueue_style( 'phoen_rewpts_backend_select2_css', PHOEN_REWPTSPLUGURL. "assets/css/select2.min.css" );	
	
	wp_enqueue_script( 'phoen_rewpts_backend_select2_js', PHOEN_REWPTSPLUGURL. "assets/js/select2.min.js" );	
	
	wp_enqueue_style( 'phoen_reward_datepick_css_backend', PHOEN_REWPTSPLUGURL. "assets/css/datetimepicker.css" );
	
	wp_enqueue_script( 'phoen_reward_dateadmin_js_backend', PHOEN_REWPTSPLUGURL. "assets/js/phoen_datetimepicker.full.min.js" );
	
	wp_enqueue_script( 'phoen_reward_bobfrontends_backend', PHOEN_REWPTSPLUGURL. "assets/js/phoen_reward_bobfrontend.js" );

	wp_enqueue_script( 'jquery-ui-datepicker' );

	// You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
	wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
	
	wp_enqueue_style( 'jquery-ui' );

}
			