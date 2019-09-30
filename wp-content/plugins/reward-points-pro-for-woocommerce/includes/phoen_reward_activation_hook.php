<?php if ( ! defined( 'ABSPATH' ) ) exit;
	
	// activation hook function		
	
	function phoe_rewpts_activation_func() {
		
		$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
		
		if($phoe_rewpts_page_settings_value==''){
			
			$phoe_rewpts_page_settings_value = array(
		
				'enable_plugin'=>1,
				
				'enable_plugin_myaccount'=>1,
				
				'enable_plugin_dob_date'=>1,
				
				'enable_plugin_reff_code'=>1,
				
				'phoen_points_expiry_month'=>'',
				
				'phoen_points_assignment_date'=>'',
				
				'limit_use_points'=>'100',
			);
				
			update_option('phoe_rewpts_page_settings_value',$phoe_rewpts_page_settings_value);
			
		}
		
		$phoe_set_point_value = get_option('phoe_set_point_value');
		
		if($phoe_set_point_value==''){
			
			$phoe_set_point_value = array(
			
				'point_type'=>'fixed_price',
				
				'phoen_percentage_wide_rewar_points_global'=>0,
				
				'reward_money'=>1,
				
				'reward_point'=>1,
				
				'reedem_point'=>1,
				
				'reedem_money'=>1,
				
				'first_login_points'=>10,
				
				'first_order_points'=>10,
				
				'first_review_points'=>10,
				
				'use_payment_gateway'=>10,
				
				'gift_birthday_points'=>10,
				
				'link_referral_points'=>10,
				
			);
			
			update_option('phoe_set_point_value',$phoe_set_point_value);
			
		}
		
		$phoen_rewpts_notification_settings = get_option('phoen_rewpts_notification_settings');
		
		if($phoen_rewpts_notification_settings==''){
			
			$phoen_rewpts_notification_settings=array(
				'enable_plugin_product_page'=>1 ,
				'phoen_rewpts_notification_product_page'=>'You Will get {points} Points On Completing This Order.'  ,
				'enable_plugin_cart_page'=>1 ,
				'phoen_rewpts_notification_cart_page'=>'You Will get {points} Points On Completing This Order.'  ,
				'enable_plugin_checkout_page'=>1 ,
				'phoen_rewpts_notification_checkout_page'=>'You Will get {points} Points On Completing This Order.' ,
				'apply_box_enable_on_cart'=>1 ,
				'phoen_apply_box_notification_cart_page'=>'You can apply {points} Points to get {price} Discount.' ,
				'apply_box_enable_on_checkout'=>1 ,
				'phoen_apply_box_notification_checkout_page'=>'You can apply {points} Points to get {price} Discount.' ,
				'phoen_apply_box_notification_bonus_page'=>'You Will get {points} Bonus Points On Completing This Order.',
				'phoen_points_expiry_before'=>0 ,
				'phoen_points_notification_before'=>0 ,
		
			);
			
			update_option('phoen_rewpts_notification_settings',$phoen_rewpts_notification_settings);
		}
		
		$btn_settings = get_option('phoen_rewpts_custom_btn_styling');	
		
		if($btn_settings==''){
			
			$btn_settings=array(
				
					'apply_btn_title'		=>'APPLY POINTS',
					
					'apply_topmargin'		=>'8',
					
					'apply_rightmargin'	=>'10',
					
					'apply_bottommargin'	=>'8',
					
					'apply_leftmargin'	=>'10',
					
					'apply_btn_bg_col'	=>'',
					
					'apply_btn_txt_col'	=>'#000000',
					
					'apply_btn_txt_hov_col'=>'',
					
					'apply_btn_hov_col'	=>'',
					
					'apply_btn_border_style'=>	'none',
					
					'apply_btn_border'	=>'0',
					
					'apply_btn_bor_col'	=>'',
					
					'apply_btn_rad'		=>'0',
					
					'remove_btn_title'	=>'REMOVE POINTS',
					
					'div_bg_col'		=>'#fff',
					
					'div_border_style'	=>'solid',
					
					'div_border'		=>'1',
					
					'div_bor_col'		=>'#ccc',
					
					'div_rad'=>'0',
					
					'apply_reward_amount_title'=>'Reward Amount',
					
					'phoen_select_text'=>'below_cart',
					
					'phoen_apply_select_text'=>'apply_above_cart',
					
					'phoen_select_text_product_page'=>'below_add_cart',
					
					
			);
			
			update_option('phoen_rewpts_custom_btn_styling',$btn_settings);	
			
		}	
	
	}
?>