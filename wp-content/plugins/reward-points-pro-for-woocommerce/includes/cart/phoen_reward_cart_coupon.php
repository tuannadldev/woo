<?php if ( ! defined( 'ABSPATH' ) ) exit;

	$gen_settings = get_option('phoe_rewpts_page_settings_value');
	
	$enable_plugin=isset($gen_settings['enable_plugin'])?$gen_settings['enable_plugin']:'';

	if($enable_plugin==1)
	{
		session_start();
		
		if(isset($_SESSION['action']) && $_SESSION['action']=="remove")
		{
			global $woocommerce;
			
			unset($_COOKIE['user_used_points']);
			
		}

		add_action('wp_head','phoen_hide_reword_coupon');
		
		function phoen_hide_reword_coupon()
		{
			if(isset($_SESSION['action']) && $_SESSION['action']=="apply")
			{
					?>
				<style>
				.woocommerce-remove-coupon {
						display: none !important;
				}
				</style>
				<?php
			}
			
		}
							
		// add reward to cart page
		
		add_action('wp_head', 'phoeniixx_rewpts_add_fee_from_cart');
		//remove rewards from cart
		
		add_action('wp_head', 'phoeniixx_rewpts_remove_fee_from_cart');
		
	}

	function phoen_rewpts_woo_add_cart_fee_remove()
	{
		global $woocommerce;
		
		$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
		
		$apply_reward_amount_title_val = (isset($gen_settingsddd['apply_reward_amount_title']) && $gen_settingsddd['apply_reward_amount_title']!='')?$gen_settingsddd['apply_reward_amount_title']:'Reward Amount';

		if(isset($_POST['remove_points'])) {	  
			
			$code=$apply_reward_amount_title_val;
			
			WC()->cart->remove_coupon( $code );

			$_SESSION['action']="remove";
		}
	}  

	add_action('wp_head', 'phoen_rewpts_woo_add_cart_fee_remove');

	//remove reward points from total if click on rmove points
	function phoeniixx_rewpts_remove_fee_from_cart()
	{
		
		if(isset($_POST['remove_points'])) {	
		global $woocommerce;
			update_option('update_opints','');
			unset($_SESSION['phoen_favcolor']);
			
			if (is_cart()) {
					remove_action('woocommerce_before_cart_table', 'phoen_rewpts_woo_add_cart_fee');
					phoen_rewpts_user_reward_amount();
					
			}else{
				
				remove_action( 'woocommerce_calculate_totals', 'phoen_rewpts_woo_add_cart_fee', 30 );
				phoen_rewpts_user_reward_amount();
			} 
			
			$_SESSION['action']="remove";
		}
	}
		
	//add reward points to total if click on rmove points
	function phoeniixx_rewpts_add_fee_from_cart()
	{
		
		$phoen_points_use_edit=isset($_POST['phoen_points_use_edit'])?$_POST['phoen_points_use_edit']:'';
		
		$phoen_points_use_hidden=isset($_POST['phoen_points_use_hidden'])?$_POST['phoen_points_use_hidden']:'';
	
		if($phoen_points_use_edit<=$phoen_points_use_hidden)	
		{		
			if(isset($_POST['phoen_apply_points']) && $phoen_points_use_edit!='') 	{	
				global $woocommerce;
				
					add_action( 'woocommerce_after_calculate_totals', 'phoen_rewpts_woo_add_cart_fee', 30 );
					phoen_rewpts_user_reward_amount();
				
				
				$_SESSION['action']="apply";
				
					?>
			<style>
			.woocommerce-remove-coupon {
				display: none !important;
			}
			</style>
			<?php
			}
			
		}else{
			wc_add_notice(__('Not have Enough Points to apply.!!','woocommerce'));
		}	
	}
	
	
	//add and display reward points to total if click on rmove points
		
	function phoen_rewpts_woo_add_cart_fee() {

		global $woocommerce;
		
		$phoen_coupan_c_data = '';
		
		$amt = phoen_rewpts_user_reward_amount();
		
		$curreny_symbol = get_woocommerce_currency_symbol();
		
		$bill_price = phoen_rewards_cart_subtotal();
		
		$bill_price = decimal_and_thousand_seprator($bill_price,0);
		
		$gen_settingsddd=get_option('phoen_rewpts_custom_btn_styling');
		
		$apply_reward_amount_title_val = (isset($gen_settingsddd['apply_reward_amount_title']) && $gen_settingsddd['apply_reward_amount_title']!='') ?$gen_settingsddd['apply_reward_amount_title']:'Reward Amount';
	
		$u_price=0;
		
		if($amt>=$bill_price) 	{
			
			$u_price=$bill_price;
	
		} 
		else if($amt<$bill_price){
			
			$u_price=$amt;
	
		}  
	
		
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'asc',
			'post_type'        => 'shop_coupon',
			'post_status'      => 'publish',
		);
			
		$coupons = get_posts( $args );
		
		foreach($coupons as $k=>$coupan_data)
		{
			
			$phoen_coupan_c = isset($coupan_data->post_title)?$coupan_data->post_title:'';
			
			if($phoen_coupan_c ==$apply_reward_amount_title_val)	
			{
				$phoen_coupan_c_data = isset($coupan_data->post_title)?$coupan_data->post_title:'';
				$new_coupon_id = isset($coupan_data->ID)?$coupan_data->ID:'';
			
			}
			
		}
	
		$coupon_code =$apply_reward_amount_title_val;
		
			if($u_price != ''){
			   
				$amount = $u_price; // Amount
				$discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product
				if($phoen_coupan_c_data=='')
				{		
					$coupon = array(
						'post_title' => $coupon_code,
						'post_content' => '',
						'post_status' => 'publish',
						'post_author' => 1,
						'post_type'        => 'shop_coupon'
					);
									   
					$new_coupon_id = wp_insert_post( $coupon );
				}				   
				// Add meta
				update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
				update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
				update_post_meta( $new_coupon_id, 'individual_use', 'no' );
				update_post_meta( $new_coupon_id, 'product_ids', '' );
				update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
				update_post_meta( $new_coupon_id, 'usage_limit', '' );
				update_post_meta( $new_coupon_id, 'expiry_date', '' );
				update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
				update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
			   
				// your coupon code here

				if ( $woocommerce->cart->has_discount( $coupon_code ) ) return;
				
					WC()->cart->add_discount( $coupon_code );
					
			}else{
				
				WC()->cart->remove_coupon( $coupon_code );
				
			}
		
		?>
		<style>
			.woocommerce-remove-coupon {
					display: none !important;
			}
			
		</style>
		<?php
	
	}