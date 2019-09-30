<?php if ( ! defined( 'ABSPATH' ) ) exit;
	
	$gen_settings = get_option('phoe_rewpts_page_settings_value',true);

	$enable_plugin=isset($gen_settings['enable_plugin'])?$gen_settings['enable_plugin']:'';

	$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);
	
	if($enable_plugin==1)
	{
		
			add_action( 'woocommerce_before_checkout_form', 'phoen_reward_points_checkout_msg' );
		
	}
	
	function phoen_reward_points_checkout_msg() {
		
		global $woocommerce;
		
		$phoen_apply_pointss = isset($_SESSION["phoen_favcolor"])?$_SESSION["phoen_favcolor"]:'';
		
		if(isset($_POST['phoen_apply_points']))
		{
		
			$phoen_points_use_edit=isset($_POST['phoen_points_use_edit'])?$_POST['phoen_points_use_edit']:'';
			
			$phoen_points_use_hidden=isset($_POST['phoen_points_use_hidden'])?$_POST['phoen_points_use_hidden']:'';
			
			if($phoen_points_use_edit<=$phoen_points_use_hidden)	
			{
				
			}
			if($phoen_points_use_edit=='')
			{
				wc_print_notice( sprintf( 'First Enter the points to apply<br />',''),'error' );
			}
		
		}
		
		$gen_settings=get_option('phoen_rewpts_custom_btn_styling');
		
		$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);
		
		$enable_plugin_checkout_page = isset($phoen_rewpts_notification_data['enable_plugin_checkout_page'])?$phoen_rewpts_notification_data['enable_plugin_checkout_page']:'1';
	
		$phoen_rewpts_notification_checkout_page = isset($phoen_rewpts_notification_data['phoen_rewpts_notification_checkout_page'])?$phoen_rewpts_notification_data['phoen_rewpts_notification_checkout_page']:'You Will get {points} Points On Completing This Order';
		
		$enable_apply_box_checkout_page = isset($phoen_rewpts_notification_data['apply_box_enable_on_checkout'])?$phoen_rewpts_notification_data['apply_box_enable_on_checkout']:'1';
		
		$phoen_apply_box_notification_checkout_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_checkout_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_checkout_page']:'You can apply {points} Points to get {price} Discount.';
		
		$phoen_apply_box_notification_bonus_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page']:'You Will get {points} Bonus Points On Completing This Order';
		
			$current_user = wp_get_current_user();
	
			global $woocommerce;
			
			$curr=get_woocommerce_currency_symbol();
			
			$gen_val = get_option('phoe_rewpts_value');

			$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
			
			$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
			
			$reward_point_value_data = phoen_reward_point_value();

			extract($reward_point_value_data);
			
			$user_order_count = phoen_reward_check_first_order();
			
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
			
			$curreny_symbol = get_woocommerce_currency_symbol();
			$bill_price = phoen_rewards_cart_subtotal();
			if($bill_price>=$amt){
				$reward_apply_limit = $total_point_reward;
			}else{
				
				$reward_apply_limit = round($bill_price);
			}
			
			$discount_price = phoen_reward_redeem_point();
			$bill_price = decimal_and_thousand_seprator($bill_price,$discount_price);
			
			$cart = $woocommerce->cart->subtotal;
		
			if(is_user_logged_in() ) {
 
				if((round($total_point_reward)!=0)||((int)$total_point_reward>0))
				{
					
					if($phoen_apply_pointss!='')
					{	
						$total_point_reward=($total_point_reward-$phoen_apply_pointss);
						$amts=round($phoen_apply_pointss,2);
						$amt=($amt-$amts);
						
					}
					if($enable_apply_box_checkout_page==1){
					?>
					
				<div class="phoen_rewpts_pts_link_div_main">
					<?php 
					if($bill_price>=$amt)
					{

					$phoen_rewards_point = round($total_point_reward);
					$phoen_apply_box_notification_checkout_page = str_replace("{points}","$phoen_rewards_point",$phoen_apply_box_notification_checkout_page);
					$phoen_rewards_point = str_replace("{price}","$amt$curreny_symbol",$phoen_apply_box_notification_checkout_page);
					
					echo "<div class='phoen_rewpts_redeem_message_on_cart'>".$phoen_rewards_point."</div>";

					}
					else if ($bill_price<=$amt)
					{
						$phoen_rewards_point = round($total_point_reward);
						$phoen_apply_box_notification_checkout_page = str_replace("{points}","$phoen_rewards_point",$phoen_apply_box_notification_checkout_page);
						$phoen_rewards_point = str_replace("{price}","$amt$curreny_symbol",$phoen_apply_box_notification_checkout_page);
					
						echo "<div class='phoen_rewpts_redeem_message_on_cart'>".$phoen_rewards_point."</div>";							
					}
					
					$gen_settings=get_option('phoen_rewpts_custom_btn_styling');
		
					$apply_btn_title    = (isset($gen_settings['apply_btn_title']))?( $gen_settings['apply_btn_title'] ):'APPLY POINTS';
					$remove_btn_title    = (isset($gen_settings['remove_btn_title']))?( $gen_settings['remove_btn_title'] ):'REMOVE POINTS';
					
					
					?>
					<div class="phoen_rewpts_pts_link_div phoen_edit_points">
				
						<form method="post" action="">
							<input type="number" name="phoen_points_use_edit" value="" min="0" max="<?php echo $reward_apply_limit.'000' ; ?>"    class="phoen_edit_points_input">
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
					<?php } ?>
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
		
		
		$gen_val = get_option('phoe_rewpts_value');
		
		$phoen_current_date = new DateTime();
		$phoen_current_dates = $phoen_current_date->format('d-m-Y');
		
		$phoen_points_assignment_date_val=isset($phoe_rewpts_page_settings_value['phoen_points_assignment_date'])?$phoe_rewpts_page_settings_value['phoen_points_assignment_date']:'';
		$phoen_points_assignment_date_vals = date("d-m-Y", strtotime($phoen_points_assignment_date_val));
		$phoen_current_datess=strtotime($phoen_current_dates);				
		$phoen_expiry_datse_to_assign_points=strtotime($phoen_points_assignment_date_vals);
		
		if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
		{
		
			$phoen_select_text = isset($gen_settings['phoen_select_text'])?$gen_settings['phoen_select_text']:'below_cart';
		
			$gen_val = get_option('phoe_rewpts_value');
		
			$reward_product_point_price_type=isset($phoen_rewpts_set_point_data['point_type'])?$phoen_rewpts_set_point_data['point_type']:'fixed_price';	 
		
			global $woocommerce,$product ,$post, $wp_query;
			
			$product_purchase_points_vasl='';
			
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
			
				$total_percentage_points = '';
				$used_reward_amount=0;
				if(is_user_logged_in() ) {
					if($phoen_apply_pointss!='')
					{
						$used_reward_amount=round($phoen_apply_pointss,2);
					}
				
					if($used_reward_amount!='')
					{
						$used_reward_amount = $used_reward_amount;
						
					}else{
						
							$used_reward_amount = 0;
							
					}
				}	
				
			
			$curreny_symbol = get_woocommerce_currency_symbol();
			
			$first_login_points_val=isset($phoen_rewpts_set_point_data['first_login_points'])?$phoen_rewpts_set_point_data['first_login_points']:'';
			$first_order_points_val=isset($phoen_rewpts_set_point_data['first_order_points'])?$phoen_rewpts_set_point_data['first_order_points']:'';
		
	
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
					{ 	$phoen_get_price = $_product->get_price();
						$phoen_total_percentageval = get_post_meta( $product_id_for_per, '_product_percentage_point_val',true);
						 $product_qty_for_per = $cart_item['quantity'];
						 $phoen_data = ($phoen_get_price*$product_qty_for_per);
						$phoen_data = phoen_reward_redeem_point($phoen_data);
						
						
						 $total_percentage_points = round($phoen_data*$phoen_total_percentageval/100);
						 
					}else{
						
						
						if($product_id_for_per==$pro_id)
						{	 $product_qty= $cart_item['quantity'];
							$phoen_get_price = $_product->get_price();
							$pro_id = $_product->get_id();
							$phoen_data = ($phoen_get_price*$product_qty);
							
							$phoen_total_percentages = get_post_meta( $pro_id, '_product_percentage_point_val',true);
							
						}
					
						 $total_percentage_pointss = round($phoen_data*$phoen_total_percentages/100); 
						 $total_percentage_points+=$total_percentage_pointss;
					
					}
				
				}
		
			}else{
				
				$total_percentage_points_value='';
				 
				$product_percentage_points=isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'])?$phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']:'';
				
				if($product_percentage_points!='')
				{
					
					$total_percentage_points = round($bill_price*$product_percentage_points/100);
					
				}
				
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
		
	
			$phoen_rewpts_custom_btn_styling=get_option('phoen_rewpts_custom_btn_styling');
			$apply_reward_amount_order_title    = (isset($phoen_rewpts_custom_btn_styling['apply_reward_amount_order_title']))?sanitize_text_field( $phoen_rewpts_custom_btn_styling['apply_reward_amount_order_title'] ):'';
			$apply_reward_amount_range_title    = (isset($phoen_rewpts_custom_btn_styling['apply_reward_amount_range_title']))?sanitize_text_field( $phoen_rewpts_custom_btn_styling['apply_reward_amount_range_title'] ):'';
			$apply_reward_amount_login_title    = (isset($phoen_rewpts_custom_btn_styling['apply_reward_amount_login_title']))?sanitize_text_field( $phoen_rewpts_custom_btn_styling['apply_reward_amount_login_title'] ):'';
			$apply_reward_amount_first_order_title    = (isset($phoen_rewpts_custom_btn_styling['apply_reward_amount_first_order_title']))?sanitize_text_field( $phoen_rewpts_custom_btn_styling['apply_reward_amount_first_order_title'] ):'';
	if($enable_plugin_checkout_page!=1){
		?>
		<style>
		.phoen_reward_notification_text.checkout_notification_{
			display:none!important;
		}
		</style>
		<?php 
	}
				?>
				<div class="phoen_reward_notification_text checkout_notification_">
				<?php
				
					if(is_user_logged_in())
					{
					
						if($reward_product_point_price_type=='fixed_price')
						{
						
							if($product_purchase_points_vasl=='')
							{
								
								if(round(($bill_price+$used_reward_amount)*$reward_value)!=0) 	{
								
									if($enable_plugin_checkout_page==1){
										
										$phoen_rewards_point = round($bill_price*$reward_value);
										
										?>
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_checkout_page);?></div>
										<?php 
									}
									
							
									if($points_range!='')
									{
										$points_range = round($points_range);
										
										?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
										
									}
								
								}else{
									
									if($enable_plugin_checkout_page==1){
										
										?>
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","0",$phoen_rewpts_notification_checkout_page);?></div>
										<?php 
									}
								}
							}else{
								
								if($product_purchase_points_vasl!='' && $product_purchase_points_vasl!='0')
								{
									if($enable_plugin_checkout_page==1){
										
										$phoen_rewards_point = round($product_purchase_points_vasl);
										
										?>
											
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_checkout_page);?></div>
										
										<?php 
									}
									
									
								
								}else{
									
									if($enable_plugin_checkout_page==1){
										
										?>
											
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","0",$phoen_rewpts_notification_checkout_page);?></div>
										
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
											
											if($enable_plugin_checkout_page==1){
										
												$phoen_rewards_point = round($total_percentage_points);
												
												?>
													
													<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_checkout_page);?></div>
												
												<?php 
											}
											
											
										}else{
											if($enable_plugin_checkout_page==1){
										
												?>
													
													<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","0",$phoen_rewpts_notification_checkout_page);?></div>
												
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
								?> <br /><div class='phoen_rewpts_reward_message_on_cart'><?php _e('You Will get','phoen-rewpts'); ?>  <?php echo round($first_order_points_val) ; ?>  <?php _e('Points On First order.','phoen-rewpts'); ?></div><?php
								
							}
						
					}else{
						
						if($reward_product_point_price_type=='fixed_price')
						{
						
							if($product_purchase_points_vasl=='')
							{
								if(round(($bill_price+$used_reward_amount)*$reward_value)!=0) 	{  
								
									if($enable_plugin_checkout_page==1){
											
										$phoen_rewards_point = round(($bill_price+$used_reward_amount)*$reward_value);
										
										?>
											
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_checkout_page);?></div>
										
										<?php 
									}
								
								if($points_range!='')
								{
									$points_range = round($points_range);
									
									?> <div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
									
								}
								
								}else{
									if($enable_plugin_checkout_page==1){
										
										?>
											
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","0",$phoen_rewpts_notification_checkout_page);?></div>
										
										<?php 
									}
								}
							}else{
								if($product_purchase_points_vasl!='' && $product_purchase_points_vasl!='0')
								{
									
									if($enable_plugin_checkout_page==1){
											
										$phoen_rewards_point = round($product_purchase_points_vasl);
										
										?>
											
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_checkout_page);?></div>
										
										<?php 
									}

								//echo "</br>";
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
										if($enable_plugin_checkout_page==1){
											
											$phoen_rewards_point = round($total_percentage_points);
										
											?>
											
											<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_checkout_page);?></div>
										
											<?php 
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
						//echo "</br>";
							if($first_login_points_val !='')
							{
								if($apply_reward_amount_login_title=='')
								{
								
									?> <div class='phoen_rewpts_reward_message_on_cart'><?php _e('You Will get','phoen-rewpts'); ?>  <?php echo round($first_login_points_val) ; ?>  <?php _e('Points On SignUp Account For This Order','phoen-rewpts'); ?></div><?php
								}else{
									?> <div class='phoen_rewpts_reward_message_on_cart'>  <?php echo round($first_login_points_val) ; ?>  <?php echo $apply_reward_amount_login_title ; ?></div><?php
								} 
							}
							
							//echo "</br>";
							if($first_order_points_val !='' && $user_order_count==0)
							{
								?> <div class='phoen_rewpts_reward_message_on_cart'><?php _e('You Will get','phoen-rewpts'); ?>  <?php echo round($first_order_points_val) ; ?>  <?php _e('Points On First order','phoen-rewpts'); ?></div><?php
							
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

	}
}