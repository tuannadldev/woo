<?php if ( ! defined( 'ABSPATH' ) ) exit;

$gen_settings = get_option('phoe_rewpts_page_settings_value');
		
$enable_plugin=isset($gen_settings['enable_plugin'])?$gen_settings['enable_plugin']:'';

if($enable_plugin==1)
{
	// save data in post meta when click on checkout in order page
	add_action( 'woocommerce_checkout_order_processed', 'phoen_rewpts_click_on_checkout_action',  1, 1  );
	
}

// save data in post meta when click on checkout in order page
function phoen_rewpts_click_on_checkout_action( $order_id ){

	if ( ! $order = wc_get_order( $order_id ) ) {
		return;
	}

	global $woocommerce;
	
	$tax_enable = get_option('woocommerce_calc_taxes');
	
	$prices_include_tax = get_option('woocommerce_prices_include_tax');
	
	$order_bill_subtotal = phoen_reward_order_sub_total($order);
	
	$order_bill_subtotal = phoen_reward_redeem_point_checkout($order_bill_subtotal);
	
	$gen_val = get_option('phoe_rewpts_value');
	
	$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
	
	$phoe_rewpts_page_settings_data = get_option('phoe_rewpts_page_settings_value',true);
	
	$reward_point_value_data = phoen_reward_point_value();
	
	extract($reward_point_value_data);
	
	$reward_product_point_price_type=isset($phoen_rewpts_set_point_data['point_type'])?$phoen_rewpts_set_point_data['point_type']:'fixed_price';	 

	$use_payment_gateway_val = 0;
	
	$used_reward_point = 0;
	
	$used_reward_amount = 0;
	
	$order_point = 0;
	
	$add_log_ord = 0;
	
	$phoen_expiry_date_add = 0;
	
	$use_payment_gateway_val = 0;
	
	$items_values_data = 0;
	
	$bill_price_checked_value = 0;
	
	$gen = '';
	
	$phoen_first_login_pointsdd = 0;
	
	$count_order = 0;
	
	$reedem_amt=phoen_rewpts_user_reward_amount();
	
	$reedem_point=phoen_rewpts_user_reward_point();
	
	$order_detail=get_post_meta($order_id);
	
	$email_id = $order_detail['_billing_email'][0];

	$order_shipping = $order_detail['_order_shipping'][0]+$order_detail['_order_shipping_tax'][0]+$order_detail['_order_tax'][0];
	
	$order_shipping_tax = $order_detail['_order_shipping_tax'][0];
	
	$order_tax = $order_detail['_order_tax'][0];
	
	$all_tax_add = ($order_shipping+$order_tax);			
	
	$order_shipping_tax=$all_tax_add;
	
	$phoen_current_date = new DateTime();
	
	$phoen_current_dates = $phoen_current_date->format('d-m-Y');
	
	$phoen_points_assignment_date_val=isset($phoe_rewpts_page_settings_data['phoen_points_assignment_date'])?$phoe_rewpts_page_settings_data['phoen_points_assignment_date']:'';
	
	$phoen_points_assignment_date_vals = date("d-m-Y", strtotime($phoen_points_assignment_date_val));
	
	$phoen_current_datess=strtotime($phoen_current_dates);
							
	$phoen_expiry_datse_to_assign_points=strtotime($phoen_points_assignment_date_vals);
	
	$orders = new WC_Order( $order_id );
	
	$points_items = $orders->get_items();
	
	$product_purchase_points_val='';
	
	foreach ( $points_items as $item_val ) {
		
		$product_id_points = $item_val['product_id'];

		$product_qtys = $item_val['qty'];

		$product_purchase_points_val_all= get_post_meta( $product_id_points, '_product_reward_point_val', true );

		if($product_purchase_points_val_all!='')
		{
		   $product_purchase_points_val+= get_post_meta( $product_id_points, '_product_reward_point_val', true );

			$product_purchase_points_val =($product_purchase_points_val*$product_qtys);
		}

	}
	
	
	$phoen_get_cat_id=array();
	
	if($product_purchase_points_val =='')
	{
		
		$bill_price= $order_bill_subtotal;
		
		$bill_price_checked_value = $order_bill_subtotal;
	
		
	}else{

		
		if($phoen_points_assignment_date_val=='')
		{ 
			
			$bill_price = phoen_reward_redeem_point_checkout($product_purchase_points_val);
		
			$bill_price_checked_value = $product_purchase_points_val;
		
		}else{
		
			if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points)
			{
				
				if($product_purchase_points_val=='')
				{
					$bill_price= $order_bill_subtotal;
					$bill_price = phoen_reward_redeem_point_checkout($bill_price);
					$bill_price_checked_value=$order_bill_subtotal;
					
				}else{
					
					$bill_price=($product_purchase_points_val);
					
					$bill_price = phoen_reward_redeem_point_checkout($bill_price);
				}
			}else{
				
				$bill_price='0';
				
			}
			
		} 
	
	}
	
	$phoen_payment_method = $order_detail['_payment_method'][0];
	
	if($phoen_payment_method=='PayPal')
	{
		$use_payment_gateway_val=isset($phoen_rewpts_set_point_data['use_payment_gateway'])?$phoen_rewpts_set_point_data['use_payment_gateway']:'';
	}
	
	
	$phoen_complited_date= $order_detail['_completed_date'][0];
	
	global $woocommerce;
	
		$orders_data = new WC_Order( $order_id );
	
		$coupons_data = $orders_data->get_items( 'coupon' );
		
		$phoen_reward_coupon_name='';	
			
		if (!empty($coupons_data)) {
			
			foreach ( $coupons_data as $item_id => $item_data ) {
			
				$phoen_reward_coupon_name = $item_data['name'];
			
			}
			
		}
		$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
		$apply_reward_amount_title_val = isset($gen_settingsddd['apply_reward_amount_title']) ?$gen_settingsddd['apply_reward_amount_title']:'';
		if($apply_reward_amount_title_val=='')
		{
			$apply_reward_amount_title_val ='Reward Amount';
		}
		$apply_reward_amount_title_val = strtolower($apply_reward_amount_title_val);
		if($phoen_reward_coupon_name!='')
		{
			$phoen_reward_coupon_name = strtolower($phoen_reward_coupon_name);
		}
		if($phoen_reward_coupon_name==$apply_reward_amount_title_val)
		{
			$cart_discount = $order_detail['_cart_discount'][0];
			
			$cart_discount_tax = $order_detail['_cart_discount_tax'][0];
			
			if($tax_enable == 'yes' && $prices_include_tax == 'yes'){
				
				$used_reward_amount =($cart_discount+$cart_discount_tax);
				
				$used_reward_amount =("-".$used_reward_amount);
			}else{
				$used_reward_amount = ("-".$cart_discount);
			}
			
			
			$used_reward_point=$used_reward_amount*$reedem_value;
			
			$gen ="hello";
		}
	
	
	
	$first_login_points_val=isset($phoen_rewpts_set_point_data['first_login_points'])?$phoen_rewpts_set_point_data['first_login_points']:'';
	
	$first_order_points_val=isset($phoen_rewpts_set_point_data['first_order_points'])?$phoen_rewpts_set_point_data['first_order_points']:'';
	
	
	//expiry date
	$phoen_points_expiry_month_val=isset($phoe_rewpts_page_settings_data['phoen_points_expiry_month'])?$phoe_rewpts_page_settings_data['phoen_points_expiry_month']:'';
	
	if($phoen_points_expiry_month_val!='')
	{
		
		$phoen_expiry_date_add =  date('d-m-Y', strtotime($phoen_points_expiry_month_val));
	
	}
	
	$phoen_email_user = get_option('phoen_email_id');
	
	$current_user = wp_get_current_user();
	
	$cur_email = $current_user->user_email;
	
	$user_id = $current_user->ID;
	$login_point = '';
	if(in_array($cur_email,$phoen_email_user))
	{
		//purchase points
		if($product_purchase_points_val=='')
		{
			$get_reward_point=$bill_price*$reward_value;
			$add_price_rev_log=$bill_price*$reward_value;
			
		}else{
			
			$bill_price=$bill_price;
			$get_reward_point=$bill_price;
			$add_price_rev_log=$bill_price;
		}

		if($use_payment_gateway_val!='')
		{
			$get_reward_point = $get_reward_point+$use_payment_gateway_val;
		}
	
	}else{
		
		//purchase points
		if($product_purchase_points_val=='')
		{
			$get_reward_point=$bill_price*$reward_value;
			$get_reward_point_add=$bill_price*$reward_value;
			
		}else{
			
			$get_reward_point=$bill_price;
			$get_reward_point_add=$bill_price;
		}
		
		if($first_login_points_val !='')
		{
			$login_point = $first_login_points_val;
			$get_reward_point=($first_login_points_val+$get_reward_point);
			
		}
		
		if($first_order_points_val !='')
		{
			$order_point = $first_order_points_val;
			$get_reward_point =($first_order_points_val+$get_reward_point);
		}
		
		if($use_payment_gateway_val !='')
		{
			$get_reward_point = $get_reward_point+$use_payment_gateway_val;
		}
		$add_log_ord = ($login_point+$order_point);
		
		$add_price_rev_log = round($get_reward_point_add+$add_log_ord);
		
	}

	$order = new WC_Order( $order_id );
	
	$items = $order->get_items();
	
	$product_id_ct=0;
	
	$product_qtys=0;
	
	foreach ( $items as $item ) {
		
		$product_id = $item['product_id'];
		
		$product_id_ct+=count($item['product_id']);
		
		$product_qtys+= $item['qty'];
		
	} 
	
	$phoen_reword_comments = get_comments( array( 
			'status'      => 'approve', 
		 'post_type'   => 'product' 
	) );	

	
	$current_user = wp_get_current_user();
	
	$cur_email = $current_user->user_email;
	
	$user_id = $current_user->ID;
	
	if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
	{
	
		foreach($phoen_reword_comments as $phoe=>$phoen_review_data)
		{
			
			$phoen_product_id = $phoen_review_data->comment_post_ID;
			
			$phoen_com_user_id = $phoen_review_data->user_id;
			
			$phoen_review_email = $phoen_review_data->comment_author_email;
						
			if($user_id==$phoen_com_user_id)
			{	
				
					$phoen_rev_data = get_post_meta($product_id,'phoeni_rewords_reviews', true);
			
					if(empty($phoen_rev_data))
					{
						$phoen_review_email_val[]=$phoen_review_email;
						update_post_meta($product_id,'phoeni_rewords_reviews',$phoen_review_email_val);
						$first_review_points_val=isset($phoen_rewpts_set_point_data['first_review_points'])?$phoen_rewpts_set_point_data['first_review_points']:'';
						
						update_post_meta($order_id,'phoeni_rewords_review_point',$first_review_points_val);
					
					}else{
						
						if(!in_array($phoen_review_email,$phoen_rev_data)){
							
							$phoen_review_email_val[]=$phoen_review_email;
						
							update_post_meta($product_id,'phoeni_rewords_reviews',$phoen_review_email_val);
							
							$first_review_points_val=isset($phoen_rewpts_set_point_data['first_review_points'])?$phoen_rewpts_set_point_data['first_review_points']:'';
							
							update_post_meta($order_id,'phoeni_rewords_review_point',$first_review_points_val);
							
						}
						
					}
			
			}
		
		}
	}	
	
	if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
	{
	
		$first_order_points_val=isset($phoen_rewpts_set_point_data['first_order_points'])?$phoen_rewpts_set_point_data['first_order_points']:'';
		
		$products_order = get_posts( array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => 'shop_order',
			'order' => 'ASC',
			'post_status' => array_keys( wc_get_order_statuses() ),
		) );
				
			$count_order =count($products_order);
			//first order points
			if($order_point=='')
			{
		
				if($count_order=='1')
				{
					$order_point =$first_order_points_val;
			
				}
			}
				//first login points
			if($count_order=='1')
			{
				$phoen_first_login_points = get_post_meta($user_id,'phoen_reward_points_for_register_user',true);
	
				if($phoen_first_login_points!='')
				{ 
					$phoen_first_login_pointsdd = get_post_meta($user_id,'phoen_reward_points_for_register_user',true);
					
				}
			}
	}		
		

	
	$phoen_update_date = get_option('phoeni_update_dates');
	
	$phoen_current_dates_updatse = new DateTime();
	
	$phoen_current_dates_update = $phoen_current_dates_updatse->format('d-m-Y H:i:s');
	
	$phoen_data_reviews = get_post_meta($order_id,'phoeni_rewords_review_point',true);
	
	$phoen_data_update_points = get_post_meta( $user_id, 'phoes_customer_points_update_valss_empty',true);
	
	$phoen_data_update_points = get_user_meta($user_id,'phoen_update_customer_hiden_val',true );
	
	$bill_pricesdfsdf= $order_bill_subtotal;
	
	$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
						
	$apply_reward_amount_title_val = (isset($gen_settingsddd['apply_reward_amount_title']) && $gen_settingsddd['apply_reward_amount_title']!='') ?$gen_settingsddd['apply_reward_amount_title']:'Reward Amount';
	
	$coupon_id = strtolower($apply_reward_amount_title_val);
	
	if(in_array($coupon_id, $woocommerce->cart->get_applied_coupons())){
		
		$coupons_obj = new WC_Coupon($coupon_id);
		$bill_pricesdfsdf = $bill_pricesdfsdf - $coupons_obj->get_amount();
		
	}

	$total_percentage_points=0;
	
	$orders = new WC_Order( $order_id );
		
	$items_val = $orders->get_items();
	
	if($reward_product_point_price_type=='percentage_price')
	{	

		$product_percentage='1';
		
		if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
		{	
		
			$phoen_totle_percentage_val = get_post_meta( $product_id, '_product_percentage_point_val',true);
			if($phoen_totle_percentage_val!='')
			{
			
				$phoen_total_percentage=0;
				
				foreach ( $items_val as $item_data ) {
		
					$product_id_for_per = $item_data['product_id'];
					$_product = wc_get_product( $product_id_for_per );
					$pro_id = $_product->get_id();
					
					if($product_id_ct=='1')
					{ 
						$phoen_total_percentageval = get_post_meta( $product_id_for_per, '_product_percentage_point_val',true);
						 $product_qty_for_per = $item_data['qty'];
						
						 $total_percentage_points = round($bill_pricesdfsdf*$phoen_total_percentageval/100);
					
					}else{
						
						
						if($product_id_for_per==$pro_id)
						{	 
							$product_qty= $item_data['qty'];
							$phoen_get_price = $_product->get_price();
							$pro_id = $_product->get_id();
							$phoen_data = ($phoen_get_price*$product_qty);
							$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
						
							$apply_reward_amount_title_val = (isset($gen_settingsddd['apply_reward_amount_title']) && $gen_settingsddd['apply_reward_amount_title']!='') ?$gen_settingsddd['apply_reward_amount_title']:'Reward Amount';
							
							$coupon_id = strtolower($apply_reward_amount_title_val);
							
							if(in_array($coupon_id, $woocommerce->cart->get_applied_coupons())){
								
								$coupons_obj = new WC_Coupon($coupon_id);
								$phoen_data = $phoen_data - $coupons_obj->get_amount();
								
							}
							
							
							$phoen_total_percentages = get_post_meta( $pro_id, '_product_percentage_point_val',true);
							
						}
					
						 $total_percentage_pointss = round($phoen_data*$phoen_total_percentages/100); 
						 $total_percentage_points+=$total_percentage_pointss;
					
					}
				
				}
		
			}else{
				
				
				 $product_percentage_points=isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'])?$phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']:'';
				
				if($product_percentage_points!='')
				{
					
						$total_percentage_pointss = round($order_bill_subtotal*$product_percentage_points/100,0); 
						
						$total_percentage_points+=$total_percentage_pointss;
				}
			}
			
		}
	}else{
		
		$product_percentage='0';
		
		if($reward_product_point_price_type=='fixed_price')
		{
			$total_percentage_points = round($bill_price);
		}
	
	}

	//Range points add 
	if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
	{
		$points_range='';
		$phoen_range_points_data = get_option('phoen_points_range_array');
		if(!empty($phoen_range_points_data))
		{
			foreach($phoen_range_points_data as $keyss=>$phoen_range_data)
			{
				$form_range = $phoen_range_data['form_range'];
				$to_range = $phoen_range_data['to_range'];
				
				if($bill_pricesdfsdf>=$form_range && $bill_pricesdfsdf<$to_range)
				{
					$points_range = $phoen_range_data['points_range'];
				
				}
				
			}	
		}
	}	 
 
	$phoe_rewpts_value = array(
				 
		'phoen_reward_enable'=>1,
		
		'total_reward_point'=>$reedem_point, //total reward points
		
		'total_reward_amount'=>$reedem_amt,  //total reward amount
		
		'used_reward_point'=>$used_reward_point, // get used reward point if used
		
		'used_reward_amount'=>$used_reward_amount, // get used reward amount if used
		
		'points_per_price'=>$reward_value, //POINTS PER PRICE
		
		'reedem_per_price'=>$reedem_value, //REEDEM PER PRICE
		
		'get_reward_point'=>$get_reward_point, //get reward points from shopping
		
		'get_reward_amount'=>$total_percentage_points, // order amount
		
		'login_point'=>0,//$login_point,  //login_point
		
		'order_point'=>$order_point,  //first order points
		
		'first_comment_rev'=>$phoen_data_reviews,   //review points
		
		'email_id'=>$email_id,  //user email id
		
		'phoen_complited_date'=>$phoen_complited_date,  //order complited date
		
		'add_log_ord'=>$add_log_ord,
		
		'add_price_rev_log'=>$add_price_rev_log,
		
		'payment_gatway_val'=>$use_payment_gateway_val,
		
		'phoen_expiry_date_add'=>$phoen_expiry_date_add,
		
		'phoen_order_id'=>$order_id,
		
		'qty'=>$items_values_data,
		
		'user_id'=>$user_id,
		
		'phoen_update_date'=>$phoen_update_date,
		
		'current_date'=>$phoen_current_dates_update,
		
		'add_update_points'=>$phoen_data_update_points,
		
		'product_purchase_points_val'=>$product_purchase_points_val,
		
		'bill_price_checked_value'=>$bill_price_checked_value,
		
		'price'=>$bill_pricesdfsdf,
		
		'gen'=>$gen,
		
		'phoen_first_login_points_myaccount'=>$phoen_first_login_pointsdd,
		
		'count_order'=>$count_order,
		
		'totale_percentage_points'=>'',
		
		'product_percentage'=>$product_percentage,
		
		'phoen_range_points'=>$points_range
	
	);
	
	
			 update_post_meta( $order_id, 'phoe_rewpts_order_status', $phoe_rewpts_value );

	
		$phoen_update_data='';
	   
	   update_post_meta( $user_id, 'phoes_customer_points_update_valss_empty', $phoen_update_data );
	   update_user_meta($user_id,'phoen_update_customer_hiden_val',$phoen_update_data );
  
   session_destroy();
}
?>