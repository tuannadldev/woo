<?php 

function phoen_reward_redeem_point(){
	ob_start();
	global $woocommerce;
	
	$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
						
	$apply_reward_amount_title_val = (isset($gen_settingsddd['apply_reward_amount_title']) && $gen_settingsddd['apply_reward_amount_title']!='') ?$gen_settingsddd['apply_reward_amount_title']:'Reward Amount';
	
	$coupon_id = strtolower($apply_reward_amount_title_val);
	$amount=0;
	if(in_array($coupon_id, $woocommerce->cart->get_applied_coupons())){
			
		$amount = WC()->cart->get_coupon_discount_amount( $coupon_id, WC()->cart->display_cart_ex_tax );
		
	}
	ob_get_clean();
	
	return $amount;
	
}

function phoen_reward_check_first_order(){
	$order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');

	## ==> Define HERE the customer ID
	if(is_user_logged_in()){
	$customer_user_id = get_current_user_id(); // current user ID here for example

	// Getting current customer orders
	$customer_orders = wc_get_orders( array(
		'meta_key' => '_customer_user',
		'meta_value' => $customer_user_id,
		'post_status' => $order_statuses,
		'numberposts' => -1
	) );
	}else{
		$customer_orders= array();
	}
	return count($customer_orders);
	
}

function phoen_reward_redeem_point_checkout($total_percentage_points_value){
	ob_start();
	global $woocommerce;
	
	$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
						
	$apply_reward_amount_title_val = (isset($gen_settingsddd['apply_reward_amount_title']) && $gen_settingsddd['apply_reward_amount_title']!='') ?$gen_settingsddd['apply_reward_amount_title']:'Reward Amount';
	
	$coupon_id = strtolower($apply_reward_amount_title_val);
	$amount=0;
	if(in_array($coupon_id, $woocommerce->cart->get_applied_coupons())){
	
		$amount = WC()->cart->get_coupon_discount_amount( $coupon_id, WC()->cart->display_cart_ex_tax );
		
		$total_percentage_points_value = $total_percentage_points_value - $amount;
		
	}
	ob_get_clean();
	
	return $total_percentage_points_value;
	
}

function decimal_and_thousand_seprator($bill_price,$discount_price){
	
	$decimal_separator = wc_get_price_decimal_separator();
			
	$thousand_seprator = wc_get_price_thousand_separator();
	
	if($thousand_seprator !=','){

		if(strpos($bill_price, ',') !== false){

			$bill_price = str_replace(',', '.', $bill_price);

			$bill_price = (float) $bill_price;

			//$bill_price = str_replace($thousand_seprator,'',$bill_price);
		}

		$bill_price = $bill_price-$discount_price;

	}else{

		$bill_price =  str_replace($thousand_seprator,'',$bill_price)-$discount_price;

	}
	return $bill_price;
		
}

function phoen_reward_order_sub_total($order){
	
	$tax_display = get_option( 'woocommerce_tax_display_cart' );
	
	$subtotal    = 0;
	
	$compound = false;
	
	if ( ! $compound ) {
		
		foreach ( $order->get_items() as $item ) {
			
			$subtotal += $item->get_subtotal();

			if ( 'incl' === $tax_display ) {				
				$subtotal += $item->get_subtotal_tax();			
			}
		}

		//$subtotal = wc_price( $subtotal, array( 'currency' => $order->get_currency() ) );

	} else {
		if ( 'incl' === $tax_display ) {
			return '';
		}

		foreach ( $order->get_items() as $item ) {
			$subtotal += $item->get_subtotal();
		}

		// Add Shipping Costs.
		$subtotal += $order->get_shipping_total();

		// Remove non-compound taxes.
		foreach ( $order->get_taxes() as $tax ) {
			if ( $tax->is_compound() ) {
				continue;
			}
			$subtotal = $subtotal + $tax->get_tax_total() + $tax->get_shipping_tax_total();
		}

		// Remove discounts.
		$subtotal = $subtotal - $order->get_total_discount();
		//$subtotal = wc_price( $subtotal, array( 'currency' => $order->get_currency() ) );
	}
	
	return $subtotal;
}


function phoen_rewards_cart_subtotal(){
	
	$curreny_symbol = get_woocommerce_currency_symbol();
	
	if ( 'incl' === get_option( 'woocommerce_tax_display_cart' )) {
		$cart_subtotal = wc_price( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );

	} else {
		
		$cart_subtotal = wc_price( WC()->cart->get_subtotal() );

	}
	
	return $bill_price=str_replace("$curreny_symbol","",strip_tags($cart_subtotal));
}

function phoen_reward_point_value(){

	$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
	
	$phoen_role_based_datas= get_option('enable_role_based',true);

	$phoen_role_datas= get_option('phoen_reword_roles');
	if(empty($phoen_role_datas)){
		$phoen_role_datas = array('');
	}

	$rewards_point_value_data = array('reward_value'=>'','reedem_value'=>'');
	
	if(is_user_logged_in() && $phoen_role_based_datas==1){
		
		$user = wp_get_current_user();
		
		$role = ( array ) $user->roles;
		
		$user_role = $role[0];
		
		$phoen_reword_roles_reward_money = get_option('phoen_reword_roles_reward_money',true);
	
		$phoen_reword_roles_reward_point = get_option('phoen_reword_roles_reward_point',true);
		
		$phoen_reword_roles_reedem_money = get_option('phoen_reword_roles_reedem_money',true);
		
		$phoen_reword_roles_reedem_point = get_option('phoen_reword_roles_reedem_point',true);
		
		if(in_array($user_role,$phoen_role_datas) || in_array('all',$phoen_role_datas)){
			
			$reward_point=isset($phoen_reword_roles_reward_point[$user_role])?$phoen_reword_roles_reward_point[$user_role]:'0';

			$reedem_point=isset($phoen_reword_roles_reedem_point[$user_role])?$phoen_reword_roles_reedem_point[$user_role]:'0';

			$reward_money=isset($phoen_reword_roles_reward_money[$user_role])?$phoen_reword_roles_reward_money[$user_role]:'0';

			$reedem_money=isset($phoen_reword_roles_reedem_money[$user_role])?$phoen_reword_roles_reedem_money[$user_role]:'0';
			
			if($reward_point !=0 || $reward_money !=0){
				$reward_value=$reward_point/$reward_money;
			}else{
				$reward_value =0;
			}
			if($reedem_point !=0 || $reedem_money !=0){
				$reedem_value=$reedem_point/$reedem_money;
			}else{
				$reedem_value =0;
			}
			
			$rewards_point_value_data = array('reward_value'=>$reward_value,'reedem_value'=>$reedem_value);
		}else{
			
			$reward_point=isset($phoen_rewpts_set_point_data['reward_point'])?$phoen_rewpts_set_point_data['reward_point']:'0';

			$reedem_point=isset($phoen_rewpts_set_point_data['reedem_point'])?$phoen_rewpts_set_point_data['reedem_point']:'0';

			$reward_money=isset($phoen_rewpts_set_point_data['reward_money'])?$phoen_rewpts_set_point_data['reward_money']:'0';

			$reedem_money=isset($phoen_rewpts_set_point_data['reedem_money'])?$phoen_rewpts_set_point_data['reedem_money']:'0';

			if($reward_point !=0 || $reward_money !=0){
				$reward_value=$reward_point/$reward_money;
			}else{
				$reward_value =0;
			}
			if($reedem_point !=0 || $reedem_money !=0){
				$reedem_value=$reedem_point/$reedem_money;
			}else{
				$reedem_value =0;
			}
			
			$rewards_point_value_data = array('reward_value'=>$reward_value,'reedem_value'=>$reedem_value);
			
		}
				
	}else{
	
		$reward_point=isset($phoen_rewpts_set_point_data['reward_point'])?$phoen_rewpts_set_point_data['reward_point']:'0';

		$reedem_point=isset($phoen_rewpts_set_point_data['reedem_point'])?$phoen_rewpts_set_point_data['reedem_point']:'0';

		$reward_money=isset($phoen_rewpts_set_point_data['reward_money'])?$phoen_rewpts_set_point_data['reward_money']:'0';

		$reedem_money=isset($phoen_rewpts_set_point_data['reedem_money'])?$phoen_rewpts_set_point_data['reedem_money']:'0';

		if($reward_point !=0 || $reward_money !=0){
			$reward_value=$reward_point/$reward_money;
		}else{
			$reward_value =0;
		}
		if($reedem_point !=0 || $reedem_money !=0){
			$reedem_value=$reedem_point/$reedem_money;
		}else{
			$reedem_value =0;
		}
		
		$rewards_point_value_data = array('reward_value'=>$reward_value,'reedem_value'=>$reedem_value);
	
	}
	
	return $rewards_point_value_data;
	
}


?>