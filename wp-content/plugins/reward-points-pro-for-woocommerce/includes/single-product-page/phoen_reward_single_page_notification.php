<?php if ( ! defined( 'ABSPATH' ) ) exit;


				
	$gen_settings = get_option('phoe_rewpts_page_settings_value',true);

	$enable_plugin=isset($gen_settings['enable_plugin'])?$gen_settings['enable_plugin']:'';
		
	$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);			

	if($enable_plugin ==1){
		
		if(isset($phoen_rewpts_notification_data['enable_plugin_product_page']) && $phoen_rewpts_notification_data['enable_plugin_product_page'] ==1){
			
			$phoen_rewpts_custom_btn_styling=get_option('phoen_rewpts_custom_btn_styling');
		
			$phoen_select_text_product_page = isset($phoen_rewpts_custom_btn_styling['phoen_select_text_product_page'])?$phoen_rewpts_custom_btn_styling['phoen_select_text_product_page']:'below_add_cart';
			
			if($phoen_select_text_product_page=='below_add_cart')
			{
				
				add_action( 'woocommerce_after_add_to_cart_button', 'phoen_rew_add_content_after_addtocart_button_func' );	
			
			}else{
				
				add_action( 'woocommerce_single_product_summary', 'phoen_rew_add_content_after_addtocart_button_func', 20 );
			
			}
			
		}	
				
	}
	
function phoen_rew_add_content_after_addtocart_button_func() {	

	global $woocommerce,$product ,$post;
	
	$gen_val = get_option('phoe_rewpts_value');
	
	$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
	
	$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
	
	$phoen_current_date = new DateTime();
	
	$phoen_current_dates = $phoen_current_date->format('d-m-Y');
	
	$phoen_points_assignment_date_val=isset($phoe_rewpts_page_settings_value['phoen_points_assignment_date'])?$phoe_rewpts_page_settings_value['phoen_points_assignment_date']:'';
	
	$phoen_points_assignment_date_vals = date("d-m-Y", strtotime($phoen_points_assignment_date_val));
	
	$phoen_current_datess=strtotime($phoen_current_dates);				
	
	$phoen_expiry_datse_to_assign_points=strtotime($phoen_points_assignment_date_vals);
	
	if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
	{
	
		$reward_point_value_data = phoen_reward_point_value();
	
		extract($reward_point_value_data);
			
		$phoen_product_post_meta = get_post_meta($post->ID);
		
		$product_purchase_points_vasl = get_post_meta( $post->ID, '_product_reward_point_val', true );
		
		$phoen_product_price = wc_get_price_to_display($product,$args=array());
		
		if(empty($phoen_product_price)){
			$phoen_product_price = 0;
		}

		
		$phoen_points_array_data = get_option('_phoeni_all_cat_id_pointss'); 
		
		global $wp_query;
		
			 $product_purchase_points_vasl = get_post_meta( $post->ID, '_product_reward_point_val', true );
		
		$reward_product_point_price_type=isset($phoen_rewpts_set_point_data['point_type'])?$phoen_rewpts_set_point_data['point_type']:'fixed_price';	 
		
		$points_range='';
		
		$phoen_range_points_data = get_option('phoen_points_range_array');
		
		if(!empty($phoen_range_points_data))
		{
			foreach($phoen_range_points_data as $keyss=>$phoen_range_data)
			{
				$form_range = $phoen_range_data['form_range'];
				$to_range = $phoen_range_data['to_range'];
				
				
				if($phoen_product_price>=$form_range && $phoen_product_price<$to_range)
				{
					$points_range = $phoen_range_data['points_range'];
				
				}
				
			}	
		}
		
		$gen_settings=get_option('phoen_rewpts_custom_btn_styling');

		$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);
		
		$enable_plugin_product_page = isset($phoen_rewpts_notification_data['enable_plugin_product_page'])?$phoen_rewpts_notification_data['enable_plugin_product_page']:'1';
		
		$phoen_rewpts_notification_product_page = isset($phoen_rewpts_notification_data['phoen_rewpts_notification_product_page'])?$phoen_rewpts_notification_data['phoen_rewpts_notification_product_page']:'You Will get {points} Points On Completing This Order';
		
		$phoen_apply_box_notification_bonus_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page']:'You Will get {points} Bonus Points On Completing This Order';
		
		$apply_reward_amount_order_title    = (isset($gen_settings['apply_reward_amount_order_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_order_title'] ):'';
		
		$apply_reward_amount_range_title    = (isset($gen_settings['apply_reward_amount_range_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_range_title'] ):'';
		
		$apply_reward_amount_login_title    = (isset($gen_settings['apply_reward_amount_login_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_login_title'] ):'';
		
		$apply_reward_amount_first_order_title    = (isset($gen_settings['apply_reward_amount_first_order_title']))?sanitize_text_field( $gen_settings['apply_reward_amount_first_order_title'] ):'';
	 
		$phoen_totle_percentage_val = get_post_meta($post->ID, '_product_percentage_point_val',true);
		
		$product_percentage_points=isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'])?$phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']:'';
		
		$curr=get_woocommerce_currency_symbol();
		
		if($product->is_type('variable'))
		{
			
			echo '<p class="pricess phoen_rewpts_reward_message_on_cart">  </p>';
			
			?>
			<input type="hidden" value="<?php echo $curr ?>" class="phoen_symbole">
			
			<script>
			
			
		 	jQuery(document).ready(function(){
				
				setTimeout(function() { 
					if(!jQuery('.woocommerce-variation-add-to-cart .single_add_to_cart_button').hasClass('disabled')){
						variation_notification();
					}
				},500)
				
			});
			
			jQuery(document).on("found_variation",".variations_form",variation_notification);
				
						
				function variation_notification(){
					
					var points_range= '<?php echo round($points_range);?>';
					
					var alt = jQuery(".phoen_symbole").val();	
					
					var decimal_point_reward_ = '<?php echo wc_get_price_decimal_separator();?>';
					
					var thousand_seprator_point_reward_ = '<?php echo wc_get_price_thousand_separator();?>';
				
					var product_price='0';
					
					if(jQuery('.woocommerce-variation-add-to-cart .single_add_to_cart_button').hasClass('disabled')){
					
						var price= '0';
					}else{
					
						if(jQuery(".woocommerce-variation-price .price ins").length==1){
							var price=jQuery(".woocommerce-variation-price .price").find("ins .amount").text();
							alert();
						}else{
							
							var price=jQuery(".woocommerce-variation-price .price").find(".amount").text();
						}
						if(price==''){
							if(jQuery(".entry-summary .price ins ").length==1){
								var price=jQuery(".entry-summary .price ins .woocommerce-Price-amount").text();
							}else{
								var price=jQuery(".entry-summary .price .woocommerce-Price-amount").text();
							}
						}
					}
					
					if(thousand_seprator_point_reward_ != ','){
						product_price= price.replace(/[.,]/g,function(m){
							return m===','?'.':'';
						});
					}else{
						var product_price  = price.replace(',','');
					}
					var product_price  = product_price.replace(alt,'');
					
					var product_price = product_price.split(alt);
					
					var product_price = product_price[0];
					
					var reward_value    = '<?php echo $reward_value ?>';

					var product_purchase_points_vasl    = '<?php echo $product_purchase_points_vasl ?>';
					 
					var enable_point_notification_on_product = '<?php echo $enable_plugin_product_page ?>';
					
					var enable_point_notification_message = '<?php echo $phoen_rewpts_notification_product_page ?>';
					
					var enable_bonus_point_notification_message = '<?php echo $phoen_apply_box_notification_bonus_page ?>';
					
					var apply_reward_amount_range_title = '<?php echo $apply_reward_amount_range_title ?>';	

					var reward_product_point_price_type = '<?php echo $reward_product_point_price_type ?>';	

					var phoen_totle_percentage_val = '<?php echo $phoen_totle_percentage_val ?>';	

					var product_percentage_points = '<?php echo $product_percentage_points ?>';	

					var product_price_total = Math.round(product_price);
							
					var total_price = (product_price*reward_value);
						
					var total_price = Math.round(total_price);
					
					if(reward_product_point_price_type=='fixed_price')
					{							 
					
						if(product_purchase_points_vasl=='')
						{
						
							if(enable_point_notification_on_product=='1')
							{
								var phoen_reward_notification_message = enable_point_notification_message.replace("{points}", total_price);
								jQuery('p.pricess').html(phoen_reward_notification_message);
								
							}
							
							
							
						}else{
							
							if(product_purchase_points_vasl!='' && product_purchase_points_vasl!=0)
							{
								
								if(enable_point_notification_on_product=='1')
								{
									var phoen_reward_notification_message = enable_point_notification_message.replace("{points}", product_purchase_points_vasl);
									jQuery('p.pricess').html(phoen_reward_notification_message);
									
								}
								
							}
						}
						
						
						if(points_range!='')
						{
							
							var phoen_bonus_reward_notification_message = enable_bonus_point_notification_message.replace("{points}", points_range);
							jQuery('p.pricessrange').html(phoen_bonus_reward_notification_message);
							
						}
						
					}else{
						if(reward_product_point_price_type=='percentage_price')
						{
							if(phoen_totle_percentage_val!='')
							{
								
								var product_precentage = (product_price_total*phoen_totle_percentage_val/100);
									
								var product_precentage = Math.round(product_precentage);
								
								if(enable_point_notification_on_product=='1')
								{
									var phoen_reward_notification_message = enable_point_notification_message.replace("{points}", product_precentage);
									jQuery('p.pricess').html(phoen_reward_notification_message);
									
								}
						
							}else{
								
								var product_percentage_points = (product_price_total*product_percentage_points/100);
									
								var product_percentage_points = Math.round(product_percentage_points);
								
								if(enable_point_notification_on_product=='1')
								{
									var phoen_reward_notification_message = enable_point_notification_message.replace("{points}", product_percentage_points);
									jQuery('p.pricess').html(phoen_reward_notification_message);
									
								}
							
							}
							
							if(points_range!='')
							{
								
								var phoen_bonus_reward_notification_message = enable_bonus_point_notification_message.replace("{points}", points_range);
								jQuery('p.pricessrange').html(phoen_bonus_reward_notification_message);
								
							}
							
						}
					}	
				   
					
				   
				}
			</script>      
		   
			<?php
		
			if($points_range!='')
			{
				echo '<p class="pricessrange phoen_rewpts_reward_message_on_cart"></p>';
			}
			
		}else{

		
		
			if($reward_product_point_price_type=='fixed_price')
			{	 
				if($product_purchase_points_vasl=='')
				{
					if(round(($phoen_product_price)*$reward_value)!=0){
					
					$phoen_rewards_point = round(($phoen_product_price)*$reward_value);
					
					if($enable_plugin_product_page==1){
						?>
						<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_rewards_point",$phoen_rewpts_notification_product_page);?></div>
						<?php 
					}
					
					
							if($points_range!='')
							{
								
								?><div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
								
							}
						}
						
				}else{
					if($product_purchase_points_vasl!='' && $product_purchase_points_vasl!='0')
					{
						if($enable_plugin_product_page==1){
							$product_purchase_points_vasl = round($product_purchase_points_vasl);
						?>
							<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$product_purchase_points_vasl",$phoen_rewpts_notification_product_page);?></div>
						<?php 
						}
					
					}	
					if($points_range!='')
					{
						
						?><div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
						
					}
				}
			
			}else{
			
				if($reward_product_point_price_type=='percentage_price')
				{
					$product_percentage_points=isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'])?$phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']:'';
					$phoen_totle_percentage_val = get_post_meta($post->ID, '_product_percentage_point_val',true);
					if($phoen_totle_percentage_val!='')
					{
						$phoen_reward_point = round($phoen_product_price*$phoen_totle_percentage_val/100) ;
						
						if($enable_plugin_product_page==1){
						?>
							<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_reward_point",$phoen_rewpts_notification_product_page);?></div>
						<?php 
						}
				
					if($points_range!='')
					{
						
						?><div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
						
					}
					
					}else{
						
						$phoen_reward_point = round($phoen_product_price*$product_percentage_points/100);
						
						if($enable_plugin_product_page==1){
						?>
							<div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$phoen_reward_point",$phoen_rewpts_notification_product_page);?></div>
						<?php 
						}
						
					
						
						if($points_range!='')
						{
							
							?><div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
							
						}
					}
				
				}else{
					
					if($points_range!='')
					{
						
						?><div class='phoen_rewpts_reward_message_on_cart'><?php echo str_replace("{points}","$points_range",$phoen_apply_box_notification_bonus_page);?></div><?php
						
					}
					
				}
			}
		}
?>
<style>
	.phoen_rewpts_reward_message_on_cart {
		margin-top: 10px;
		width: 100%;
	}
</style>
<?php
	}

}