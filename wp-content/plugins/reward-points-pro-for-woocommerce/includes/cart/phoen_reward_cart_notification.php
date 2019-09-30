<?php if ( ! defined( 'ABSPATH' ) ) exit;

$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);

$gen_settings = get_option('phoe_rewpts_page_settings_value',true);
	
$phoen_rewpts_custom_btn_styling=get_option('phoen_rewpts_custom_btn_styling');
			
$phoen_select_text = isset($phoen_rewpts_custom_btn_styling['phoen_select_text'])?$phoen_rewpts_custom_btn_styling['phoen_select_text']:'below_cart';	
	
$enable_plugin=isset($gen_settings['enable_plugin'])?$gen_settings['enable_plugin']:'';

if($enable_plugin ==1){
	
	if(isset($phoen_rewpts_notification_data['enable_plugin_cart_page']) && $phoen_rewpts_notification_data['enable_plugin_cart_page']==1){
		// shows number of points to get on cart page
		//
		if($phoen_select_text == "below_cart"){
			add_action('woocommerce_after_cart_table', 'phoen_rewpts_action_get_reward_points', 10, 0);
		}else{
			add_action( 'woocommerce_before_cart_table', 'phoen_rewpts_action_get_reward_points', 10, 0);
		}
			
	}
	
}

 // shows number of points to get on cart page
function phoen_rewpts_action_get_reward_points() {
	
	$gen_settings=get_option('phoen_rewpts_custom_btn_styling');
	
	$phoen_apply_select_text = isset($gen_settings['phoen_apply_select_text'])?$gen_settings['phoen_apply_select_text']:'apply_above_cart';
	
	$phoen_apply_pointss = isset($_SESSION["phoen_favcolor"])?$_SESSION["phoen_favcolor"]:'';
	
	$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);		
	
	$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
	
	$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);
	
	$enable_plugin_cart_page = isset($phoen_rewpts_notification_data['enable_plugin_cart_page'])?$phoen_rewpts_notification_data['enable_plugin_cart_page']:'1';
		
	$phoen_rewpts_notification_cart_page = isset($phoen_rewpts_notification_data['phoen_rewpts_notification_cart_page'])?$phoen_rewpts_notification_data['phoen_rewpts_notification_cart_page']:'You Will get {points} Points On Completing This Order';
	
	$phoen_apply_box_notification_bonus_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page']:'You Will get {points} Bonus Points On Completing This Order';
	
	$reward_point_value_data = phoen_reward_point_value();
	
	extract($reward_point_value_data);
	
	$user_order_count = phoen_reward_check_first_order();
	
	$first_login_points_val=isset($phoen_rewpts_set_point_data['first_login_points'])?$phoen_rewpts_set_point_data['first_login_points']:'';
	
	$first_order_points_val=isset($phoen_rewpts_set_point_data['first_order_points'])?$phoen_rewpts_set_point_data['first_order_points']:'';
	
	$curreny_symbol = get_woocommerce_currency_symbol();
	
	$bill_price = phoen_rewards_cart_subtotal();
	
	$phoen_select_text = isset($gen_settings['phoen_select_text'])?$gen_settings['phoen_select_text']:'below_cart';
	
	 $gen_val = get_option('phoe_rewpts_value');
	
	$phoen_current_date = new DateTime();
	
	$phoen_current_dates = $phoen_current_date->format('d-m-Y');
	
	$phoen_points_assignment_date_val=isset($phoe_rewpts_page_settings_value['phoen_points_assignment_date'])?$phoe_rewpts_page_settings_value['phoen_points_assignment_date']:'';
	
	$phoen_points_assignment_date_vals = date("d-m-Y", strtotime($phoen_points_assignment_date_val));
	
	$phoen_current_datess=strtotime($phoen_current_dates);				
	
	$phoen_expiry_datse_to_assign_points=strtotime($phoen_points_assignment_date_vals);
	
	if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
	{
	
			$enable_plugin_product_price=isset($gen_val['enable_plugin_product_price'])?$gen_val['enable_plugin_product_price']:'';
			
			$enable_plugin_product_percentage=isset($gen_val['enable_plugin_product_percentage'])?$gen_val['enable_plugin_product_percentage']:'';

			$reward_product_point_price_type=isset($phoen_rewpts_set_point_data['point_type'])?$phoen_rewpts_set_point_data['point_type']:'fixed_price';	 
		
			global $woocommerce,$product ,$post, $wp_query;
			
			$product_purchase_points_vasl=0;
			
			$term_cat_id=array();
			
			$quantity_count=0;
			
			$product_id_count=0;
			
			foreach( $woocommerce->cart->get_cart() as $cart_item ){
			
				$product_id = $cart_item['product_id'];
				
				$product_id_count+=$cart_item['product_id'];
				
				$quantity = $cart_item['quantity'];
				
				$quantity_count+=$cart_item['quantity'];
				
				 $product_purchase_points_vaslss = get_post_meta( $product_id, '_product_reward_point_val', true );
				 
				if($product_purchase_points_vaslss!='')
				{
					if($product_id_count=='1')
					{
						$product_purchase_points_vasl= get_post_meta( $product_id, '_product_reward_point_val', true );
					}else{
						 $product_purchase_points_vasl+= get_post_meta( $product_id, '_product_reward_point_val', true );
					}
					
				 
					$product_purchase_points_vasl =($product_purchase_points_vasl*$quantity);
				}
				 
				 if($product_purchase_points_vaslss=='')
				{
					$terms_post = get_the_terms( $product_id , 'product_cat' );
					
					if(!empty($terms_post))
					{
						foreach ($terms_post as $keys=>$term_cat) {
								
							$term_cat_id[$keys] = $term_cat->term_id; 
						
						}
					}
					
				}
			
			}
			
		
		$total_percentage_points = 0;
		$used_reward_amount=0;
		
		if(is_user_logged_in() ) {
			
			if($phoen_apply_pointss!='')
			{
				$used_reward_amount=round($phoen_apply_pointss/$reedem_value,2);
			}
			
			if($used_reward_amount!='')
			{
				$used_reward_amount = $used_reward_amount;
				
			}else{
				
				$used_reward_amount = 0;
					
			}
		}	
		
		$curreny_symbol = get_woocommerce_currency_symbol();
		
		$discount_price = phoen_reward_redeem_point();
		
		$bill_price = decimal_and_thousand_seprator($bill_price,$discount_price);
		
		$bill_cart_shipping_total=str_replace("$curreny_symbol","",strip_tags($woocommerce->cart->get_cart_shipping_total()));
		
		$bill_cart_shipping_total=str_replace("(ex. VAT)","",strip_tags($bill_cart_shipping_total));
			
		if($reward_product_point_price_type=='percentage_price')
		{	 
			$phoen_totle_percentage_val = get_post_meta( $product_id, '_product_percentage_point_val',true);
			
			if($phoen_totle_percentage_val!='')
			{
			
				$phoen_total_percentage=0;
				
					foreach( $woocommerce->cart->get_cart() as $cart_item ){
		
					$product_id_for_per = $cart_item['product_id'];
					
					$_product = wc_get_product( $product_id_for_per );
					
					$pro_id = $_product->get_id();
					
					if($product_id_count=='1')
					{ 	
						$phoen_get_price = $_product->get_price();
						
						$phoen_total_percentageval = get_post_meta( $product_id_for_per, '_product_percentage_point_val',true);
						
						$product_qty_for_per = $cart_item['quantity'];

						$phoen_data = ($phoen_get_price*$product_qty_for_per);
						
						$phoen_data = phoen_reward_redeem_point($phoen_data);
						
						$total_percentage_points = round($phoen_data*$phoen_total_percentageval/100);
						 
					}else{
						
						
						if($product_id_for_per==$pro_id)
						{	 
							$product_qty= $cart_item['quantity'];
							
							$phoen_get_price = $_product->get_price();
							
							$pro_id = $_product->get_id();
							
							$phoen_data = ($phoen_get_price*$product_qty);
							
							$phoen_data = phoen_reward_redeem_point($phoen_data);
							
							$phoen_total_percentages = get_post_meta( $pro_id, '_product_percentage_point_val',true);
							
						}
					
						$total_percentage_pointss = round($phoen_data*$phoen_total_percentages/100); 
						
						$total_percentage_points+=$total_percentage_pointss;
					
					}
				
				}
		
			}else{
				$product_id_countt='';
				$product_percentage_points=isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'])?$phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']:'';
				
				if($product_percentage_points!='')
				{
			
				}
				
				$total_percentage_points_value = $bill_price;
				
				$total_percentage_points = round($bill_price*$product_percentage_points/100);
				
			}
		}	
		
		
			$points_range='';
			$phoen_range_points_data = get_option('phoen_points_range_array');
			if(!empty($phoen_range_points_data))
			{
				foreach($phoen_range_points_data as $keyss=>$phoen_range_data)
				{
					$form_range = $phoen_range_data['form_range'];
					$to_range = $phoen_range_data['to_range'];
					
					if($bill_price>=$form_range && $bill_price<$to_range)
					{
						$points_range = $phoen_range_data['points_range'];
					
					}
					
				}	
			}
		
			$gen_settings=get_option('phoen_rewpts_custom_btn_styling');
			
			$apply_reward_amount_order_title    = (isset($gen_settings['apply_reward_amount_order_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_order_title'] ):'';
			
			$apply_reward_amount_range_title    = (isset($gen_settings['apply_reward_amount_range_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_range_title'] ):'';
			
			$apply_reward_amount_login_title    = (isset($gen_settings['apply_reward_amount_login_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_login_title'] ):'';
			
			$apply_reward_amount_first_order_title    = (isset($gen_settings['apply_reward_amount_first_order_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_first_order_title'] ):'';
		
			if($enable_plugin_cart_page!=1){
				?>
			<style>
			.phoen_reward_notification_text.cart_notification_{
				display:none!important;
			}
			</style>
			<?php
			}
			?>
			<div class="phoen_reward_notification_text cart_notification_">
			<?php
			
				if(is_user_logged_in())
				{
					if($reward_product_point_price_type=='fixed_price')
					{
					
						if($product_purchase_points_vasl=='')
						{
							
							if(round(($bill_price+$used_reward_amount)*$reward_value)!=0) 	{  
								
								$phoen_rewards_point = round($bill_price*$reward_value);
					
								if($enable_plugin_cart_page==1){
									?>
									<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_cart_page);?></div>
									<?php 
								}
					
								
								if($points_range!='')
								{
									$points_range = round($points_range);
									
									?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
									
								}
							
							}else{
								if($enable_plugin_cart_page==1){
									?>
									<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","0",$phoen_rewpts_notification_cart_page);?></div>
									<?php 
								}
							}
						}else{
							
							if($product_purchase_points_vasl!='' && $product_purchase_points_vasl!='0')
							{
								
								$phoen_rewards_point = round($product_purchase_points_vasl);
					
								if($enable_plugin_cart_page==1){
									?>
									<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_cart_page);?></div>
									<?php 
								}
								
							
							}
							
							if($points_range!='')
							{
									$points_range = round($points_range);
									
									?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
							}
						
						}
					
					}else{
						$product_percentage_points=isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'])?$phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']:'';
						
						if($reward_product_point_price_type=='percentage_price')
						{
							
							 if($product_percentage_points!='')
							{
									if($total_percentage_points!='')
									{
										$phoen_rewards_point = round($total_percentage_points);
					
										if($enable_plugin_cart_page==1){
										?>
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_cart_page);?></div>
										<?php 
										}
										
									
									}else{
										if($enable_plugin_cart_page==1){
										?>
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","0",$phoen_rewpts_notification_cart_page);?></div>
										<?php 
										}
									}
									if($points_range!='')
									{
										$points_range = round($points_range);
									
										?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
									}
							
							}
						
						}else{
							if($points_range!='')
							{
								$points_range = round($points_range);
									
								?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
							}
						}
					}	
					if($first_order_points_val !='' && $user_order_count ==0)
					{
						?> 
							<br /><div class='phoen_rewpts_reward_message_on_cart'><?php _e('You Will get','phoen-rewpts'); ?>  <?php echo round($first_order_points_val) ; ?>  <?php _e('Points On First order.','phoen-rewpts'); ?></div>
						<?php
						
					}
					
				}else{
					
					if($reward_product_point_price_type=='fixed_price')
					{
					
						if($product_purchase_points_vasl=='')
						{
							if(round(($bill_price+$used_reward_amount)*$reward_value)!=0) 	{  
							
								$phoen_rewards_point = round(($bill_price+$used_reward_amount)*$reward_value);
						
								if($enable_plugin_cart_page==1){
								?>
									<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_cart_page);?></div>
								<?php 
								}
							 
									if($points_range!='')
									{
										$points_range = round($points_range);
									
										?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
									}
							
							
							}
						}else{
							
							if($product_purchase_points_vasl!='' && $product_purchase_points_vasl!='0')
							{
								if($enable_plugin_cart_page==1){
									$product_purchase_points_vasl =  round($product_purchase_points_vasl);
								?>
									<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$product_purchase_points_vasl",$phoen_rewpts_notification_cart_page);?></div>
								<?php 
								}
						
							}
							if($points_range!='')
							{
								$points_range = round($points_range);
									
								?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
							}
						
						}
						
					}else{
						$product_percentage_points=isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'])?$phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']:'';
						if($reward_product_point_price_type=='percentage_price')
						{
							
							 if($product_percentage_points!='')
							{
								
								if($total_percentage_points!='')
								{
									
									if($enable_plugin_cart_page==1){
										
										$product_purchase_points_vasl =  round($total_percentage_points);
										?>
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$product_purchase_points_vasl",$phoen_rewpts_notification_cart_page);?></div>
										<?php 
									}
									
								}
								
								if($points_range!='')
								{
									$points_range = round($points_range);
									
									?><div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
								}
							
							
							}
						
						}else{
							if($points_range!='')
							{
								$points_range = round($points_range);
									
									?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
							}
						}
					}	
					
					if($first_login_points_val !='')
					{
						 if($apply_reward_amount_login_title=='')
						{
							?> <div class='phoen_rewpts_reward_message_on_cart'><?php _e('You Will get','phoen-rewpts'); ?>  <?php echo round($first_login_points_val) ; ?>  <?php _e('Points On SignUp Account For This Order','phoen-rewpts'); ?></div><?php
						}else{
							?> <div class='phoen_rewpts_reward_message_on_cart'>  <?php echo round($first_login_points_val) ; ?>  <?php echo $apply_reward_amount_login_title ; ?></div><?php
						} 
					}
					
					
					if($first_order_points_val !='' && $user_order_count==0)
					{
						?> 
							<div class='phoen_rewpts_reward_message_on_cart'><?php _e('You Will get','phoen-rewpts'); ?>  <?php echo round($first_order_points_val) ; ?>  <?php _e('Points On First order','phoen-rewpts'); ?></div>
						<?php
						
					}
						
				} 
				
			?>
			</div>
			<style>
			.phoen_reward_notification_text {
				background-color: #f9f9f9;
				border: 1px solid #e5e5e5;
				margin: 0 0 20px;
				padding: 10px 20px;
			}
			</style>
			<?php
		
	}	
	
	$user_detail=get_users();
	
	$phoen_uer_email=array();
	
	foreach($user_detail as $k=>$user_detail_val)
	{
		$phoen_uer_email[$k] = $user_detail_val->data->user_email;
		
	}
	
	update_option('phoen_email_id',$phoen_uer_email);
	
	$phoen_email_user = get_option('phoen_email_id');
	
}