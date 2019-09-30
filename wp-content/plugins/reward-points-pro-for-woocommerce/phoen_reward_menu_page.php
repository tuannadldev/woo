<?php if ( ! defined( 'ABSPATH' ) ) exit;

	//Admin Menu
	
	add_action('admin_menu', 'phoe_rewpts_menu_booking');
	
	function phoe_rewpts_menu_booking() {
		
		add_menu_page('phoeniixx_Reward_pts',__( 'Reward Points', 'phoen-rewpts' ) ,'nosuchcapability','phoeniixx_Reward_pts',NULL, PHOEN_REWPTSPLUGURL.'assets/images/aa2.png' ,'57.1');
		
		add_submenu_page( 'phoeniixx_Reward_pts', 'phoeniixx_reward_settings',__( 'Settings', 'phoen-rewpts' ),'manage_options', 'phoeniixx_reward_settings',  'Phoeniixx_reward_settings_func' );
		
		add_submenu_page( 'phoeniixx_Reward_pts', 'phoeniixx_reward_order', __('Customers Report', 'phoen-rewpts' ),'manage_options', 'phoeniixx_reward_order',  'phoeniixx_rewpts_check_order_admin' );

	}
	
	//setting Tab
	
	function Phoeniixx_reward_settings_func(){ 
		
		if(isset($_GET['tab'])): $tab = sanitize_text_field( $_GET['tab'] );	else: $tab=""; endif;
			?>
			<div id="profile-page" class="wrap">
				
				<h2> <?php _e('Reward Points For Woocommerce','phoen-rewpts'); ?></h2>
				
				<?php $tab = (isset($_GET['tab']))?$_GET['tab']:'';?>
				
				<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				
					<a class="nav-tab <?php if($tab == 'phoen_rewpts_setting' || $tab == ''){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoeniixx_reward_settings&amp;tab=phoen_rewpts_setting"><?php _e('Settings','phoen-rewpts'); ?></a>
					<a class="nav-tab <?php if($tab == 'phoen_rewpts_set_points'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoeniixx_reward_settings&amp;tab=phoen_rewpts_set_points"><?php _e('Set Points','phoen-rewpts'); ?></a>
					<a class="nav-tab <?php if($tab == 'phoen_rewpts_roles'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoeniixx_reward_settings&amp;tab=phoen_rewpts_roles"><?php _e('Roles','phoen-rewpts'); ?></a>
					<a class="nav-tab <?php if($tab == 'phoen_rewpts_notification'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoeniixx_reward_settings&amp;tab=phoen_rewpts_notification"><?php _e('Notification','phoen-rewpts'); ?></a>
					<a class="nav-tab <?php if($tab == 'phoen_rewpts_styling'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoeniixx_reward_settings&amp;tab=phoen_rewpts_styling"><?php _e('Styling','phoen-rewpts'); ?></a>
					
				</h2>
			
			</div>
		  
		<?php
		
		if($tab == 'phoen_rewpts_setting'|| $tab == ''):
			include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/phoeniixx_reward_pagesetting.php');
		elseif($tab == 'phoen_rewpts_set_points'):				
			include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/phoeniixx_reward_set_points.php');
		elseif($tab == 'phoen_rewpts_roles'):
			include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/phoeniixx_reward_roles.php');
		elseif($tab == 'phoen_rewpts_notification'):				
			include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/phoeniixx_reward_notification.php');				
		elseif($tab == 'phoen_rewpts_styling'):
			include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/phoeniixx_reward_styling.php');
		endif;
	}
	
	// Admin Submenu function
	function phoeniixx_rewpts_check_order_admin()
	{	
		wp_enqueue_script( 'phoen-reward-pagination-scripts', plugin_dir_url(__FILE__)."assets/js/pagination.js", array( 'jquery' ),true );
		
		include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/phoeniixx_rewpts_admin_panel.php');

	}
?>