<?php if ( ! defined( 'ABSPATH' ) ) exit;

$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);

$gen_settings = get_option('phoe_rewpts_page_settings_value',true);
	
$enable_plugin=isset($gen_settings['enable_plugin'])?$gen_settings['enable_plugin']:'';
	
$phoen_rewpts_notification_cart_apply_box = isset($phoen_rewpts_notification_data['apply_box_enable_on_cart'])?$phoen_rewpts_notification_data['apply_box_enable_on_cart']:'1';

if($enable_plugin ==1){
	
	if(isset($phoen_rewpts_notification_data['apply_box_enable_on_cart']) && $phoen_rewpts_notification_data['apply_box_enable_on_cart']==1){
		// shows number of points to get on cart page
		add_action( 'woocommerce_before_cart', 'phoen_reward_apply_point_box', 10, 0);
			
	}
	
}

function phoen_reward_apply_point_box(){
	
	$phoen_rewpts_custom_btn_styling=get_option('phoen_rewpts_custom_btn_styling');
		
	$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);		

	$phoen_apply_pointss = isset($_SESSION["phoen_favcolor"])?$_SESSION["phoen_favcolor"]:'';
		
	$reward_point_value_data = phoen_reward_point_value();
	
	extract($reward_point_value_data);
	
	$first_login_points_val=isset($phoen_rewpts_set_point_data['first_login_points'])?$phoen_rewpts_set_point_data['first_login_points']:'';

	$first_order_points_val=isset($phoen_rewpts_set_point_data['first_order_points'])?$phoen_rewpts_set_point_data['first_order_points']:'';

	$curreny_symbol = get_woocommerce_currency_symbol();

	$bill_price = phoen_rewards_cart_subtotal();
	$bill_price = decimal_and_thousand_seprator($bill_price,0);	
	$current_user = wp_get_current_user();

	global $woocommerce;
	
	$curr=get_woocommerce_currency_symbol();
	
	$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
	
	$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);
	
	$phoen_apply_box_notification_cart_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_cart_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_cart_page']:'You Will get {points} Points On Completing This Order';
	
	$total_point_reward=phoen_rewpts_user_reward_point();
	
	if($total_point_reward<0){
		$total_point_reward=0;
	}
	
	$limit_points_use_oneorder=isset($phoe_rewpts_page_settings_value['limit_use_points'])?$phoe_rewpts_page_settings_value['limit_use_points']:'';
	
	//limit of points to use 
	if($limit_points_use_oneorder !='')
	{	
 
		if($total_point_reward <=$limit_points_use_oneorder)
		{
			
			$total_point_reward =$total_point_reward;
		
		}else{
			
			$total_point_reward =$limit_points_use_oneorder;
		}
		
	}

	$amt=round($total_point_reward,2);
	if($bill_price>=$amt){
		$reward_apply_limit = $total_point_reward;
	}else{
		
		$reward_apply_limit = round($bill_price);
	}
	if(is_user_logged_in() ) {
 
		if((round($total_point_reward)!=0)||((int)$total_point_reward>0))
		{
			
			if($phoen_apply_pointss!='')
			{
				$total_point_reward=($total_point_reward-$phoen_apply_pointss);
				$amts=round($phoen_apply_pointss,2);
				$amt=($amt-$amts);
			}
			?>
			<div class="phoen_rewpts_pts_link_div_main">
			<?php 
			if($bill_price>=$amt)
			{	
			
				$phoen_rewards_point = round($total_point_reward);
				$phoen_apply_box_notification_cart_page = str_replace("{points}","$phoen_rewards_point",$phoen_apply_box_notification_cart_page);
				$phoen_rewards_point = str_replace("{price}","$amt$curreny_symbol",$phoen_apply_box_notification_cart_page);
				
				echo "<div class='phoen_rewpts_redeem_message_on_cart'>".$phoen_rewards_point."</div>";
			
			}
			else if ($bill_price<=$amt)
			{
				
				$phoen_rewards_point = round($total_point_reward);
				$phoen_apply_box_notification_cart_page = str_replace("{points}","$phoen_rewards_point",$phoen_apply_box_notification_cart_page);
				$phoen_rewards_point = str_replace("{price}","$amt$curreny_symbol",$phoen_apply_box_notification_cart_page);
				echo "<div class='phoen_rewpts_redeem_message_on_cart'>".$phoen_rewards_point."</div>";
				
			}

			$apply_btn_title    = (isset($phoen_rewpts_custom_btn_styling['apply_btn_title']))?( $phoen_rewpts_custom_btn_styling['apply_btn_title'] ):'APPLY POINTS';
			$remove_btn_title    = (isset($phoen_rewpts_custom_btn_styling['remove_btn_title']))?( $phoen_rewpts_custom_btn_styling['remove_btn_title'] ):'REMOVE POINTS';
						
			?>
			<div class="phoen_rewpts_pts_link_div phoen_edit_points">
		
				<form method="post" action="">
					
					<input type="number" name="phoen_points_use_edit" value="" min="0" max="<?php echo $reward_apply_limit.'000' ; ?>" class="phoen_edit_points_input">
					
					<input type="hidden" name="phoen_points_use_hidden" value="<?php echo $total_point_reward ; ?>" max="<?php echo $total_point_reward ; ?>">
					
					<?php
					
					global $woocommerce;
						
					$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
										
					$apply_reward_amount_title_val = (isset($gen_settingsddd['apply_reward_amount_title']) && $gen_settingsddd['apply_reward_amount_title']!='') ?$gen_settingsddd['apply_reward_amount_title']:'Reward Amount';
					
					$coupon_id = strtolower($apply_reward_amount_title_val);
					
					if(in_array($coupon_id, $woocommerce->cart->get_applied_coupons())){
						?>
							<input type="submit" class="button primary remove_points_value"  value="<?php echo $remove_btn_title; ?>" name="remove_points"> 
						<?php
					}else{
						
						if($total_point_reward >0): ?>
							
							<input type="submit" class="button primary"  value="<?php echo $apply_btn_title; ?>" name="phoen_apply_points">&nbsp; <?php 
						
						endif; ?>
							
							<input type="submit" class="button primary remove_points_value"  value="<?php echo $remove_btn_title; ?>" name="remove_points"> <?php
					} ?>

				</form>
		
			</div>
		 </div>
		<style>
		.phoen_rewpts_redeem_message_on_cart {
			float: left;
			width: 100%;
		}
							
		.phoen_rewpts_pts_link_div.phoen_edit_points {
				float: left;
				width: 100%;
		}
		</style>
			<?php  
		}
	
	}
	
}