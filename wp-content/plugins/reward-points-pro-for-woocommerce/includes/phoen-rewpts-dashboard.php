<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
	global $woocommerce;
	
	if(is_user_logged_in()){
	
	
	
	$curr=get_woocommerce_currency_symbol();
	
	$current_user = wp_get_current_user();

	$cur_email = $current_user->user_email;

	$cur_id = $current_user->ID;
		
	$products_order = get_posts( array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => $cur_id,
		'post_type'   => 'shop_order',
		'order' => 'ASC',
		'post_status' => array_keys( array('wc-completed'=>'Completed')),
		//'post_status' => array_keys( wc_get_order_statuses() ),
	) );
	
	
	 $limit = 10;

	$total = count($products_order);
	$pages = ceil($total / $limit);
	$result = ceil($total / $limit);
	
	$current = (get_query_var("paged")) ? get_query_var("paged") : 1;
	
	$next = $current < $pages ? $current + 1 : null;
	$previous = $current > 1 ? $current - 1 : null;

	$offset = ($current - 1) * $limit;
	$products_order = array_slice($products_order, $offset, $limit);
	
	$phoen_expire_date_data = '';
	
	$phoe_set_point_value = get_option('phoe_set_point_value',true);
	
	$reward_point_value_data = phoen_reward_point_value();
	
	extract($reward_point_value_data);
	
	?>
	<table>
	
		<tr>
		
			<th><?php _e('Order Date','phoen-rewpts'); ?></th>
			<th><?php _e('Event Name','phoen-rewpts'); ?> </th>
			<th><?php _e('Credit','phoen-rewpts'); ?></th>
			<th><?php _e('Debit','phoen-rewpts'); ?></th>
			<th><?php _e('Balance','phoen-rewpts'); ?></th>
			<th><?php _e('Expiry Date','phoen-rewpts'); ?> </th>
		
		</tr>
		<?php
	
		$phoen_current_dates_updates_val = get_post_meta($cur_id,'phoeni_update_dates_checkeds',true);
		$phoen_current_dates_updates_val = get_user_meta($cur_id,'phoen_update_date', true );
		$phoen_update_date_val=strtotime($phoen_current_dates_updates_val);
			
		$phoen_current_date = new DateTime();
		$phoen_current_dates = $phoen_current_date->format('d-m-Y');
		$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
		$phoen_points_assignment_date_val=isset($phoe_rewpts_page_settings_value['phoen_points_assignment_date'])?$phoe_rewpts_page_settings_value['phoen_points_assignment_date']:'';
		$phoen_points_assignment_date_vals = date("d-m-Y", strtotime($phoen_points_assignment_date_val));
		$phoen_current_datess=strtotime($phoen_current_dates);				
		$phoen_expiry_datse_to_assign_points=strtotime($phoen_points_assignment_date_vals);
		$add_pall_pointss = 0;
		$phoen_points_expiry_month=isset($phoe_rewpts_page_settings_value['phoen_points_expiry_month'])?$phoe_rewpts_page_settings_value['phoen_points_expiry_month']:'';
		$phoen_points_expiry_month_val = date("d-m-Y", strtotime($phoen_points_expiry_month));
		
	if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
	{				
			
			$phoen_first_login_points=0;
			$phoen_first_login_points = get_post_meta($cur_id,'phoen_reward_points_for_register_user',true);
			$phoen_reward_points_for_register_userdate = get_post_meta($cur_id,'phoen_reward_points_for_register_userdate',true);	
			$phoen_reward_points_for_register_user_id = get_post_meta($cur_id,'phoen_reward_points_for_register_user_id',true);		
			if($phoen_reward_points_for_register_user_id==$cur_id ) 
			{				
				if($phoen_first_login_points!='' && $phoen_first_login_points!='0')
				{
					$add_pall_pointss=$phoen_first_login_points;
					?>
					<tr>
					<td><?php echo $phoen_reward_points_for_register_userdate ;?></td>
					<td><?php _e('Points Earn For Account SignUp','phoen-rewpts'); ?> </td>
					<td><?php echo $phoen_first_login_points ; ?></td>
					<td>-</td>
					<td><?php echo "+".$phoen_first_login_points ; ?></td>
					<td><?php echo $phoen_points_expiry_month_val;?></td>
					</tr>
					<?php
				}
			} 
			
			
			$phoen_reward_referral_user_points = get_user_meta( $cur_id, 'phoen_reward_referral_user_points', true );
			
			$phoen_rewards_referral_user_points_date = get_user_meta( $cur_id, 'phoen_reward_referral_user_date', true );
			
			$phoen_reward_referral_user_id = get_user_meta( $cur_id, 'phoen_reward_referral_user_id', true );
			
			$phoen_reward_referral_user_pointsss=0;
				
			if(!empty($phoen_reward_referral_user_points))
			{
				for($j=0; $j<count($phoen_reward_referral_user_points); $j++)
				{
					if($phoen_reward_referral_user_id==$cur_id)
					{
						
						$phoen_reward_referral_user_pointsss+= $phoen_reward_referral_user_points[$j];
						
						?>
						<tr>
							<td><?php echo $phoen_rewards_referral_user_points_date[$j] ;?></td>
							<td><?php _e('Referral Points','phoen-rewpts'); ?> </td>
							<td><?php echo round($phoen_reward_referral_user_points[$j]) ;?></td>
							<td><?php echo "-"; ?></td>
							<td><?php echo  "+".round((is_numeric($phoen_first_login_points)?$phoen_first_login_points:0)+(is_numeric($phoen_reward_referral_user_pointsss)?$phoen_reward_referral_user_pointsss:0)) ;?></td>
							<td>
							<?php //echo $phoen_expire_date_data ; ?></td>
						</tr>
						<?php
						
					}
						
				}
				$add_pall_pointss = $phoen_reward_referral_user_pointsss+$add_pall_pointss;
			}
			
			$gift_birthday_points_valss=0;
			$gift_birthday_points_valss = get_post_meta($cur_id,'phoeni_rewords_gift_dob_point',true);
			$phoen_bday_date = get_post_meta($cur_id,'phoeni_rewords_gift_dob_point_date',true);
			if(empty($products_order))
			{
				if($gift_birthday_points_valss!='' && $gift_birthday_points_valss!='0')
				{
					$add_pall_pointss = $gift_birthday_points_valss+$add_pall_pointss;
					?>
						<tr>
							<td><?php echo $phoen_bday_date ;?></td>
							<td><?php _e('Birth Day Gift Points','phoen-rewpts'); ?> </td>
							<td><?php echo round($gift_birthday_points_valss) ;?></td>
							<td><?php echo "-"; ?></td>
							<td><?php echo  "+".round($add_pall_pointss) ;?></td>
							<td>
							<?php //echo $phoen_expire_date_data ; ?></td>
						</tr>
					<?php
				}
			}
	}		
			
			for($i=0;$i<count($products_order);$i++) {

				$products_detail=get_post_meta($products_order[$i]->ID); 
				
				$gen_settings=get_post_meta( $products_order[$i]->ID, 'phoe_rewpts_order_status', true );
						
					$user_id=isset($gen_settings['user_id'])?$gen_settings['user_id']:'';
					$phoen_order_id=isset($gen_settings['phoen_order_id'])?$gen_settings['phoen_order_id']:'';
					$phoen_expiry_date=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
					$phoen_expiry_date_num=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
				
				if($phoen_expiry_date !='')
				{
				
					$phoen_current_date = date("d-m-Y") ;
					
					$phoen_current_date=strtotime($phoen_current_date);
					$phoen_expiry_date=strtotime($phoen_expiry_date);
				 
					 if($phoen_current_date<=$phoen_expiry_date)
					 {
						 $phoen_expire_date_data =  $phoen_expiry_date_num;
					
					  }else{
						
						$phoen_expire_date_data= "Expire";
					 } 
					 
				}
				
				$phoen_complited_date = isset($products_detail['_completed_date'])?$products_detail['_completed_date'][0]:'';
				$phoen_complited_date = date("d-m-Y", strtotime($phoen_complited_date));
				
				$phoen_email_id=isset($gen_settings['email_id'])?$gen_settings['email_id']:'';
				$phoen_login_points=isset($gen_settings['login_point'])?$gen_settings['login_point']:'';
				$phoen_order_point=isset($gen_settings['order_point'])?$gen_settings['order_point']:'';
				$add_log_ord=isset($gen_settings['add_log_ord'])?$gen_settings['add_log_ord']:'';
				
				
				if($phoen_complited_date !='')
				{
				
					if($user_id ==$cur_id)
					{
						if($phoen_login_points !='')
						{
							$phoen_login_points = $gen_settings['login_point'] ;
							?>
							<tr>
							<td><?php echo $phoen_complited_date ;?></td>
							<td><?php _e('Points Earn For Account SignUp','phoen-rewpts'); ?> </td>
							<td><?php echo $phoen_login_points ; ?></td>
							<td>-</td>
							<td><?php echo "+".$phoen_login_points ; ?></td>
							<td><?php echo $phoen_expire_date_data ; ?></td>
							</tr>
							<?php
						}
						
						if($phoen_order_point !='')
						{
							$phoen_login_points = isset($gen_settings['login_point'])?$gen_settings['login_point']:'' ;
							?>
							<tr>
							<td><?php echo $phoen_complited_date ;?></td>
							<td><?php _e('Points Earn For First Order','phoen-rewpts'); ?></td>
							<td><?php echo $phoen_order_point ;?></td>
							<td>-</td>
							<td><?php echo "+".round($phoen_order_point+$add_pall_pointss) ;?></td>
							<td><?php echo $phoen_expire_date_data ; ?></td>
							</tr>
							<?php
						}
							
					}
					
				}		
			
			}
			
		
			$total_point_reward=0;
			for($i=0;$i<count($products_order);$i++){


				$products_detail=get_post_meta($products_order[$i]->ID); 
				
				$gen_settings=get_post_meta( $products_order[$i]->ID, 'phoe_rewpts_order_status', true );
				
					$phoen_email_id=isset($gen_settings['email_id'])?$gen_settings['email_id']:'';
					$user_id=isset($gen_settings['user_id'])?$gen_settings['user_id']:'';
				
				if($user_id ==$cur_id)
				{
					
					$phoen_order_id=isset($gen_settings['phoen_order_id'])?$gen_settings['phoen_order_id']:'';
					$phoen_expiry_date=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
					
					if($phoen_expiry_date!='')
					{
						
						$phoen_current_dates = date("d-m-Y") ;
					
						$phoen_current_dates=strtotime($phoen_current_dates);
						$phoen_expiry_date= strtotime($phoen_expiry_date);

						if($phoen_current_dates > $phoen_expiry_date)
						{
							
							$phoen_complited_date_exp= date("d-m-Y", strtotime($products_detail['_completed_date'][0]));
					
							$ptsperprice_exp=isset($gen_settings['points_per_price'])?$gen_settings['points_per_price']:'';
							
							$used_reward_point_exp=isset($gen_settings['used_reward_point'])?$gen_settings['used_reward_point']:'0';
							
							$get_reward_point=isset($gen_settings['get_reward_point'])?$gen_settings['get_reward_point']:'';
							
							$login_point_first_exp=isset($gen_settings['login_point'])?$gen_settings['login_point']:'';
							
							$order_point_val_exp=isset($gen_settings['order_point'])?$gen_settings['order_point']:'';
							
							$payment_gatway_val_exp=isset($gen_settings['payment_gatway_val'])?$gen_settings['payment_gatway_val']:'';
							
							$phoen_order_id_exp=isset($gen_settings['phoen_order_id'])?$gen_settings['phoen_order_id']:'';
							$phoen_data_reviews_exp = get_post_meta($phoen_order_id_exp,'phoeni_rewords_review_point',true);
						
							$phoen_expire_date_data_exp="Expire";
							
							$order_bill_points_expire=isset($gen_settings['get_reward_amount'])?$gen_settings['get_reward_amount']:'';
							$order_bill_exp=round($order_bill_points_expire);
							
							$product_purchase_points_val_exp=isset($gen_settings['product_purchase_points_val'])?$gen_settings['product_purchase_points_val']:'';
							$bill_price_checked_value_exp=isset($gen_settings['bill_price_checked_value'])?$gen_settings['bill_price_checked_value']:'';
							$product_percentage=isset($gen_settings['product_percentage'])?$gen_settings['product_percentage']:'';
							
							
							$point_reward_exp=0;
							$tpoint_reward_exp=0;
							
							
							if($products_order[$i]->post_status=="wc-completed")
							{
							
								if($login_point_first_exp =='')
								{
								
									if($bill_price_checked_value_exp !='')
									{
									
										if($product_percentage=='1')
										{
											$point_reward_exp= $order_bill_exp;
											$point_reward_points_exp= $order_bill_exp;
										
										}else{
											
											$point_reward_exp= $order_bill_exp*$ptsperprice_exp;
											$point_reward_points_exp= $order_bill_exp*$ptsperprice_exp;
										}
										
										
									}else{
										
										$point_reward_exp= $order_bill_exp;
										$point_reward_points_exp= $order_bill_exp;
									}
									
								
								}else{
									
									$point_reward_points_exp= $order_bill_exp*$ptsperprice_exp;
									
									$point_reward_exp= $order_bill_exp*$ptsperprice_exp;
									
									$point_reward_exp = ($login_point_first_exp+$point_reward_exp);
								}
								
								if($order_point_val_exp!='')
								{
									$point_reward_exp =($point_reward_exp+$order_point_val_exp);
									
								}
								
								if($phoen_data_reviews_exp !='')
								{
									$point_reward_exp =($point_reward_exp+$phoen_data_reviews_exp);
								
								}
								
								if($payment_gatway_val_exp !='')
								{
									$point_reward_exp =($point_reward_exp+$payment_gatway_val_exp);
								}
								
							}
						
							$tpoint_reward_exp+=$used_reward_point_exp+$point_reward_exp;
						
							$total_point_reward_exp+=$tpoint_reward_exp;
							
							if($phoen_complited_date_exp!='')
							{
								if($phoen_data_reviews_exp !='')
								{
									?>
									<tr>
										<td><?php echo $phoen_complited_date_exp ;?></td>
										<td><?php _e('Points Earn For Product Review','phoen-rewpts'); ?> </td>
										<td><?php echo  round($phoen_data_reviews_exp) ;?></td>
										<td>-</td>
										<td><?php echo  "+".round($phoen_data_reviews_exp) ;?></td>
										<td><?php echo $phoen_expire_date_data_exp ; ?></td>
									</tr>
									
									<?php
								}
								
								if($payment_gatway_val_exp !='')
								{
									?>
									<tr>
										<td><?php echo $phoen_complited_date_exp ;?></td>
										<td><?php _e('Points Earn For payment gateway Use','phoen-rewpts'); ?>  </td>
										<td><?php echo  round($payment_gatway_val_exp) ;?></td>
										<td>-</td>
										<td><?php echo  "+".round($payment_gatway_val_exp) ;?></td>
										<td><?php echo $phoen_expire_date_data_exp ; ?></td>
									</tr>
									
									<?php
								}
								
								 if($used_reward_point_exp !='0' && $used_reward_point_exp!='')
								{
									?>
									<tr>
										<td><?php echo $phoen_complited_date_exp ;?></td>
										<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
										<td>-</td>
										<td><?php 
										if($used_reward_point_exp =='0')
										{
											echo "-";
											
										}else{
											
											echo round($used_reward_point_exp);
										}
										
										?></td>
										<td><?php echo round($used_reward_point_exp) ;?></td>
										<td>-</td>
									</tr>
									<?php
								} 
						
								?>
								<tr>
									<td><?php echo $phoen_complited_date_exp ;?></td>
									<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
									<td><?php echo round($point_reward_points_exp) ;?></td>
									<td><?php echo "-"; ?></td>
									<td><?php echo  "+".round($total_point_reward_exp) ;?></td>
									<td><?php echo $phoen_expire_date_data_exp ; ?></td>
								</tr>
								<?php
							}	
					
						}else{
							
							$phoen_complited_date= date("d-m-Y", strtotime($products_detail['_completed_date'][0]));
							
							$ptsperprice=isset($gen_settings['points_per_price'])?$gen_settings['points_per_price']:'';
							
							$used_reward_point=isset($gen_settings['used_reward_point'])?$gen_settings['used_reward_point']:'0';
							
							$get_reward_point=isset($gen_settings['get_reward_point'])?$gen_settings['get_reward_point']:'';
							
							$login_point_first=isset($gen_settings['login_point'])?$gen_settings['login_point']:'';
							
							$order_point_val=isset($gen_settings['order_point'])?$gen_settings['order_point']:'';
							
							$first_comment_rev=isset($gen_settings['first_comment_rev'])?$gen_settings['first_comment_rev']:'';
							
							$payment_gatway_val=isset($gen_settings['payment_gatway_val'])?$gen_settings['payment_gatway_val']:'';
							
							$phoen_order_id=isset($gen_settings['phoen_order_id'])?$gen_settings['phoen_order_id']:'';
							
							$phoen_expiry_date=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
							
							$order_bill_points=isset($gen_settings['get_reward_amount'])?$gen_settings['get_reward_amount']:'';
							$order_bill=round($order_bill_points);
							
							$order_bill_refund=round($order_bill_points);
							
							$phoen_expire_date_data=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
							
							$phoen_data_reviews = get_post_meta($phoen_order_id,'phoeni_rewords_review_point',true);
							
							$phoen_add_update_points=isset($gen_settings['add_update_points'])?$gen_settings['add_update_points']:'';
							
							$gift_birthday_points_val = get_post_meta($cur_id,'phoeni_rewords_gift_dob_point',true);
							
							$phoen_bday_date = get_post_meta($cur_id,'phoeni_rewords_gift_dob_point_date',true);
							
							$phoeni_first_login_points_myaccount=isset($gen_settings['phoen_first_login_points_myaccount'])?$gen_settings['phoen_first_login_points_myaccount']:'';
							
							$totale_percentage_points=isset($gen_settings['totale_percentage_points'])?$gen_settings['totale_percentage_points']:'';
							
							$point_reward=0;
							$tpoint_reward=0;
							
							$phoen_current_date=isset($gen_settings['current_date'])?$gen_settings['current_date']:'';
							$phoen_current_date=strtotime($phoen_current_date);
							
							$product_purchase_points_val=isset($gen_settings['product_purchase_points_val'])?$gen_settings['product_purchase_points_val']:'';
							
							$bill_price_checked_value=isset($gen_settings['bill_price_checked_value'])?$gen_settings['bill_price_checked_value']:'';
							$product_percentage=isset($gen_settings['product_percentage'])?$gen_settings['product_percentage']:'';
							$phoen_range_points=isset($gen_settings['phoen_range_points'])?$gen_settings['phoen_range_points']:'';
							
							if($phoen_update_date_val=='')
							{
							
								if($products_order[$i]->post_status=="wc-completed")
								{
								
									if($login_point_first =='')
									{
										
										if($bill_price_checked_value !='')
										{
										
											if($product_percentage=='1')
											{
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											
											}else{
												
												$point_reward= $order_bill*$ptsperprice;
												$point_reward_points= $order_bill*$ptsperprice;
											}
											
										}else{
											
											$point_reward= $order_bill;
											$point_reward_points= $order_bill;
										}
								
									}else{
										
										if($bill_price_checked_value !='')
										{
											if($product_percentage=='1')
											{
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											
											}else{
												
												$point_reward= $order_bill*$ptsperprice;
												$point_reward_points= $order_bill*$ptsperprice;
											}
											
										}else{
											
											$point_reward= $order_bill;
											$point_reward_points= $order_bill;
										}
										
										$point_reward = ($login_point_first+$point_reward);
									}
									
									if($order_point_val!='')
									{
										$point_reward =($point_reward+$order_point_val);
										
									}
									
									if($phoen_data_reviews !='')
									{
										$point_reward =($point_reward+$phoen_data_reviews);
									
									}
									
									if($payment_gatway_val!='')
									{
										$point_reward =($point_reward+$payment_gatway_val);
									}
									
									if($gift_birthday_points_val!='')
									{
										$point_reward =($point_reward+$gift_birthday_points_val);
									}
									if($i=='0')
									{
										if($phoen_reward_referral_user_pointsss!='0')
										{
											$point_reward =($point_reward+$phoen_reward_referral_user_pointsss);
										}
									}
									
									if($phoeni_first_login_points_myaccount!='')
									{
										$point_reward =($point_reward+$phoeni_first_login_points_myaccount);
									}
								
									if($phoen_range_points!='')
									{
										$point_reward =($point_reward+$phoen_range_points);
									}
									
									
									
								}
								
								$tpoint_reward+=$used_reward_point+$point_reward;
								
								$total_point_reward+=$tpoint_reward;
								
								if($phoen_complited_date!='')
								{
									if($phoen_data_reviews !='')
									{
										?>
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Points Earn For Product Review','phoen-rewpts'); ?> </td>
											<td><?php echo  round($phoen_data_reviews) ;?></td>
											<td>-</td>
											<td><?php echo  "+".round($phoen_data_reviews) ;?></td>
											<td><?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										
										<?php
									}
									
									if($payment_gatway_val !='')
									{
										?>
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Points Earn For payment gateway Use','phoen-rewpts'); ?>  </td>
											<td><?php echo  round($payment_gatway_val) ;?></td>
											<td>-</td>
											<td><?php echo  "+".round($payment_gatway_val) ;?></td>
											<td><?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										
										<?php
									}
									
									if($gift_birthday_points_val!='')
									{
										
										?>
									<tr>
										<td><?php echo $phoen_bday_date ;?></td>
										<td><?php _e('Birth Day Gift Points','phoen-rewpts'); ?> </td>
										<td><?php echo round($gift_birthday_points_val) ;?></td>
										<td><?php echo "-"; ?></td>
										<td><?php echo  "+".round($gift_birthday_points_val) ;?></td>
										<td>
										<?php echo $phoen_expire_date_data ; ?></td>
									</tr>
									<?php
									
									}
									
									if($used_reward_point !='' && $used_reward_point!='0')
									{
										?>
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
											<td>-</td>
											<td><?php 
											if($used_reward_point =='0')
											{
												echo "-";
											}else{
												
												echo round($used_reward_point);
											}
											
											?></td>
											<td><?php echo round($used_reward_point) ;?></td>
											<td>-</td>
										</tr>
										<?php
									}
									
									if($phoen_range_points!='')
									{
									
										if($point_reward_points=='0')
										{
											$total_point_rewards_range = $total_point_reward;
											
										}else{
											
											$total_point_rewards_range = $phoen_range_points;
										}
									
									
										?>
									
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Bonus Points For Purchase','phoen-rewpts'); ?> </td>
											<td><?php echo round($phoen_range_points) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($total_point_rewards_range) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
										
										
									}
									
												?>
												<tr>
													<td><?php echo $phoen_complited_date ;?></td>
													<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
													<td><?php echo round($point_reward_points) ;?></td>
													<td><?php echo "-"; ?></td>
													<td><?php echo  "+".round($total_point_reward) ;?></td>
													<td>
													<?php echo $phoen_expire_date_data ; ?></td>
												</tr>
												<?php
											
								}else{
									$phoen_current_date = date("d-m-Y");
									$point_reward_points=isset($point_reward_points)?$point_reward_points:'';
									if($used_reward_point !='' && $used_reward_point!='0')
									{
										?>
										<tr>
											<td><?php echo $phoen_current_date ;?></td>
											<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
											<td>-</td>
											<td><?php 
											if($used_reward_point =='0')
											{
												echo "-";
											}else{
												
												echo round($used_reward_point);
											}
											
											?></td>
											<td><?php echo round($used_reward_point) ;?></td>
											<td>-</td>
										</tr>
										<?php
									}
									
									if($point_reward_points!='' && $point_reward_points!='0')
									{
										?>
										<tr>
											<td><?php echo $phoen_current_date ;?></td>
											<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
											<td><?php echo "0"; //round($point_reward_points) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($total_point_reward) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
									}	
									
									
								}
							}else{
								
								if($phoen_update_date_val<$phoen_current_date)	
								{
									
									if($products_order[$i]->post_status=="wc-completed")
									{
									
										if($login_point_first =='')
										{
											
											if($bill_price_checked_value !='')
											{
										
												if($product_percentage=='1')
												{
													$point_reward= $order_bill;
													$point_reward_points= $order_bill;
												
												}else{
													
													$point_reward= $order_bill*$ptsperprice;
													$point_reward_points= $order_bill*$ptsperprice;
												}
												
											}else{
												
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											}
									
										}else{
											
											if($bill_price_checked_value !='')
											{
												if($product_percentage=='1')
												{
													$point_reward= $order_bill;
													$point_reward_points= $order_bill;
												
												}else{
													
													$point_reward= $order_bill*$ptsperprice;
													$point_reward_points= $order_bill*$ptsperprice;
												}
												
											}else{
												
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											}
											
											$point_reward = ($login_point_first+$point_reward);
										}
										
										if($order_point_val!='')
										{
											$point_reward =($point_reward+$order_point_val);
											
										}
										
										if($phoen_data_reviews !='')
										{
											$point_reward =($point_reward+$phoen_data_reviews);
										
										}
										
										if($payment_gatway_val!='')
										{
											$point_reward =($point_reward+$payment_gatway_val);
										}
										
										if($gift_birthday_points_val!='')
										{
											$point_reward =($point_reward+$gift_birthday_points_val);
										}
										
										if($phoen_add_update_points!='')
										{
											$point_reward =($point_reward+$phoen_add_update_points);
											
										}
										if($i=='0')
										{
											if($phoen_reward_referral_user_pointsss!='0')
											{
												$point_reward =($point_reward+$phoen_reward_referral_user_pointsss);
											}
										}
										
										if($phoeni_first_login_points_myaccount!='')
										{
											$point_reward =($point_reward+$phoeni_first_login_points_myaccount);
										}
									
										if($phoen_range_points!='')
										{
											$point_reward =($point_reward+$phoen_range_points);
										}
									
											
									}
									
									$tpoint_reward+=$used_reward_point+$point_reward;
								
									$total_point_reward+=$tpoint_reward;
									
									if($phoen_complited_date!='')
									{
										if($phoen_data_reviews !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For Product Review','phoen-rewpts'); ?> </td>
												<td><?php echo  round($phoen_data_reviews) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($phoen_data_reviews) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($payment_gatway_val !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For payment gateway Use','phoen-rewpts'); ?>  </td>
												<td><?php echo  round($payment_gatway_val) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($payment_gatway_val) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($gift_birthday_points_val!='')
										{
											
											?>
										<tr>
											<td><?php echo $phoen_bday_date ;?></td>
											<td><?php _e('Birth Day Gift Points','phoen-rewpts'); ?> </td>
											<td><?php echo round($gift_birthday_points_val) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($gift_birthday_points_val) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
										
										}
										
										if($used_reward_point !='0' && $used_reward_point!='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
												<td>-</td>
												<td><?php 
												if($used_reward_point =='0')
												{
													echo "-";
												}else{
													
													echo round($used_reward_point);
												}
												
												?></td>
												<td><?php echo round($used_reward_point) ;?></td>
												<td>-</td>
											</tr>
											<?php
										}
										
									
										if($total_point_reward=='0')
										{
											$total_point_reward = get_post_meta( $cur_id, 'phoes_customer_points_update_valss',true);
											$total_point_reward = get_user_meta($cur_id,'phoen_update_customer_points',true );
											
										}else{
											
											$total_point_reward =$total_point_reward;
											
										}
										
										if($phoen_range_points!='')
										{
										
											if($point_reward_points=='0')
											{
												$total_point_rewards_range = $total_point_reward;
												
											}else{
												
												$total_point_rewards_range = $phoen_range_points;
											}
										
										
										
											?>
										
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Bonus Points For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo round($phoen_range_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_rewards_range) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
											
											
										}
										
									
											?>
											
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo round($point_reward_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_reward) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
										
									}else{
										$phoen_current_date = date("d-m-Y");
										$point_reward_points=isset($point_reward_points)?$point_reward_points:'';
										if($used_reward_point !='0' && $used_reward_point!='')
										{
											?>
											<tr>
												<td><?php echo $phoen_current_date ;?></td>
												<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
												<td>-</td>
												<td><?php 
												if($used_reward_point =='0')
												{
													echo "-";
												}else{
													
													echo round($used_reward_point);
												}
												
												?></td>
												<td><?php echo round($used_reward_point) ;?></td>
												<td>-</td>
											</tr>
											<?php
										}
										
										if($point_reward_points!='' && $point_reward_points!='0')
											{
												?>
												<tr>
													<td><?php echo $phoen_current_date ;?></td>
													<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
													<td><?php echo "0" ; //round($point_reward_points) ;?></td>
													<td><?php echo "-"; ?></td>
													<td><?php echo  "+".round($total_point_reward) ;?></td>
													<td>
													<?php echo $phoen_expire_date_data ; ?></td>
												</tr>
												<?php
											}	
										
									}
								}else{
									if($products_order[$i]->post_status=="wc-completed")
									{
									
										if($login_point_first =='')
										{
											
											if($bill_price_checked_value !='')
											{
												
												if($product_percentage=='1')
												{
													$point_rewards= $order_bill;
													$point_reward_points= $order_bill;
												
												}else{
													
													$point_rewards= $order_bill*$ptsperprice;
													$point_reward_points= $order_bill*$ptsperprice;
												}
												
											}else{
												
												$point_rewards= $order_bill;
												$point_reward_points= $order_bill;
											}
									
										}else{
											
											if($bill_price_checked_value !='')
											{
												if($product_percentage=='1')
												{
													$point_rewards= $order_bill;
													$point_reward_points= $order_bill;
												
												}else{
													
													$point_rewards= $order_bill*$ptsperprice;
													$point_reward_points= $order_bill*$ptsperprice;
												}
												
											}else{
												
												$point_rewards= $order_bill;
												$point_reward_points= $order_bill;
											}
											
											$point_rewards = ($login_point_first+$point_rewards);
										}
										
										if($order_point_val!='')
										{
											$point_rewards =($point_rewards+$order_point_val);
											
										}
										
										if($phoen_data_reviews !='')
										{
											$point_rewards =($point_rewards+$phoen_data_reviews);
										
										}
										
										if($payment_gatway_val!='')
										{
											$point_rewards =($point_rewards+$payment_gatway_val);
										}
										
										if($gift_birthday_points_val!='')
										{
											$point_rewards =($point_rewards+$gift_birthday_points_val);
										}
										if($i=='0')
										{
											if($phoen_reward_referral_user_pointsss!='0')
											{
												$point_rewards =($point_rewards+$phoen_reward_referral_user_pointsss);
											}
										}
										
										if($phoeni_first_login_points_myaccount!='')
										{
											$point_rewards =($point_rewards+$phoeni_first_login_points_myaccount);
										}
									
										if($phoen_range_points!='')
										{
											$point_rewards =($point_rewards+$phoen_range_points);
										}
									
											
									}
									
									$tpoint_rewards+=$used_reward_point+$point_rewards;
								
									$total_point_rewards+=$tpoint_rewards;
									
									if($phoen_complited_date!='')
									{
										if($phoen_data_reviews !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For Product Review','phoen-rewpts'); ?> </td>
												<td><?php echo  round($phoen_data_reviews) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($phoen_data_reviews) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($payment_gatway_val !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For payment gateway Use','phoen-rewpts'); ?>  </td>
												<td><?php echo  round($payment_gatway_val) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($payment_gatway_val) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($gift_birthday_points_val!='')
										{
											
											?>
										<tr>
											<td><?php echo $phoen_bday_date ;?></td>
											<td><?php _e('Birth Day Gift Points','phoen-rewpts'); ?> </td>
											<td><?php echo round($gift_birthday_points_val) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($gift_birthday_points_val) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
										
										}
									
										if($used_reward_point !='0' && $used_reward_point!='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
												<td>-</td>
												<td><?php 
												if($used_reward_point =='0')
												{
													echo "-";
												}else{
													
													echo round($used_reward_point);
												}
												
												?></td>
												<td><?php echo round($used_reward_point) ;?></td>
												<td>-</td>
											</tr>
											<?php
										}
										
									$total_point_rewardsss = get_post_meta( $cur_id, 'phoes_customer_points_update_valss',true);
									$total_point_rewardsss = get_user_meta($cur_id,'phoen_update_customer_points',true );
									
									$total_point_rewards = $total_point_rewardsss;	
									
									if($phoen_range_points!='')
									{
										if($point_reward_points=='0')
										{
											$total_point_rewards_range = $total_point_rewards;
											
										}else{
											
											$total_point_rewards_range = $phoen_range_points;
										}
									
										?>
									
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Bonus Points For Purchase','phoen-rewpts'); ?> </td>
											<td><?php echo round($phoen_range_points) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($total_point_rewards_range) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
										
										
									}	
										
								
												?>
												
												<tr>
													<td><?php echo $phoen_complited_date ;?></td>
													<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
													<td><?php echo round($point_reward_points) ;?></td>
													<td><?php echo "-"; ?></td>
													<td><?php echo  "+".round($total_point_rewards) ;?></td>
													<td>
													<?php echo $phoen_expire_date_data ; ?></td>
												</tr>
												<?php
									
									}else{
										$phoen_current_date = date("d-m-Y");
										$point_reward_points=isset($point_reward_points)?$point_reward_points:'';
										
										if($used_reward_point !='' && $used_reward_point!='0')
										{
											?>
											<tr>
												<td><?php echo $phoen_current_date ;?></td>
												<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
												<td>-</td>
												<td><?php 
												if($used_reward_point =='0')
												{
													echo "-";
												}else{
													
													echo round($used_reward_point);
												}
												
												?></td>
												<td><?php echo round($used_reward_point) ;?></td>
												<td>-</td>
											</tr>
											<?php
										}
										$total_point_rewardsss = get_post_meta( $cur_id, 'phoes_customer_points_update_valss',true);
										$total_point_rewardsss = get_user_meta($cur_id,'phoen_update_customer_points',true );
										
										$total_point_rewards = $total_point_rewardsss;	
									
										if($point_reward_points!='' && $point_reward_points!='0')
										{
											?>
											<tr>
												<td><?php echo $phoen_current_date ;?></td>
												<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo "0"; //round($point_reward_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_rewards) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
										}	
										
										
									}
								}
								
							}		
					
							
						}
					}else{
						
							$phoen_complited_date= $products_detail['_completed_date'][0];
							$phoen_complited_date = date("d-m-Y", strtotime($phoen_complited_date));
							$ptsperprice=isset($gen_settings['points_per_price'])?$gen_settings['points_per_price']:'';
							
							$used_reward_point=isset($gen_settings['used_reward_point'])?$gen_settings['used_reward_point']:'0';
							
							$get_reward_point=isset($gen_settings['get_reward_point'])?$gen_settings['get_reward_point']:'';
							
							$login_point_first=isset($gen_settings['login_point'])?$gen_settings['login_point']:'';
							
							$order_point_val=isset($gen_settings['order_point'])?$gen_settings['order_point']:'';
							
							$first_comment_rev=isset($gen_settings['first_comment_rev'])?$gen_settings['first_comment_rev']:'';
							
							$payment_gatway_val=isset($gen_settings['payment_gatway_val'])?$gen_settings['payment_gatway_val']:'';
							
							$phoen_order_id=isset($gen_settings['phoen_order_id'])?$gen_settings['phoen_order_id']:'';
							
							$phoen_expiry_date=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
							
							$order_bill_points=isset($gen_settings['get_reward_amount'])?$gen_settings['get_reward_amount']:'';
							$order_bill=round($order_bill_points);
							
							$order_bill_refund=round($order_bill_points);
							
							$phoen_expire_date_data=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
							
							$phoen_data_reviews = get_post_meta($phoen_order_id,'phoeni_rewords_review_point',true);
							
							$phoen_add_update_points=isset($gen_settings['add_update_points'])?$gen_settings['add_update_points']:'';
							
							$gift_birthday_points_val = get_post_meta($cur_id,'phoeni_rewords_gift_dob_point',true);
							
							$phoen_bday_date = get_post_meta($cur_id,'phoeni_rewords_gift_dob_point_date',true);
							
							$phoeni_first_login_points_myaccount=isset($gen_settings['phoen_first_login_points_myaccount'])?$gen_settings['phoen_first_login_points_myaccount']:'';
							
							$totale_percentage_points=isset($gen_settings['totale_percentage_points'])?$gen_settings['totale_percentage_points']:'';
							
							$point_reward=0;
							$tpoint_reward=0;
							
							$phoen_current_date=isset($gen_settings['current_date'])?$gen_settings['current_date']:'';
							$phoen_current_date=strtotime($phoen_current_date);
							
							$product_purchase_points_val=isset($gen_settings['product_purchase_points_val'])?$gen_settings['product_purchase_points_val']:'';
							
							$bill_price_checked_value=isset($gen_settings['bill_price_checked_value'])?$gen_settings['bill_price_checked_value']:'';
							
							$product_percentage=isset($gen_settings['product_percentage'])?$gen_settings['product_percentage']:'';
							
							$phoen_range_points=isset($gen_settings['phoen_range_points'])?$gen_settings['phoen_range_points']:'';
							
							if($phoen_update_date_val=='')
							{
							
								if($products_order[$i]->post_status=="wc-completed")
								{
								
									if($login_point_first =='')
									{
										
										if($bill_price_checked_value !='')
										{
										
											if($product_percentage=='1')
											{
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											
											}else{
												
												$point_reward= $order_bill*$ptsperprice;
												$point_reward_points= $order_bill*$ptsperprice;
											}
										}else{
											
											$point_reward= $order_bill;
											$point_reward_points= $order_bill;
										}
								
									}else{
										
										if($bill_price_checked_value !='')
										{
											if($product_percentage=='1')
											{
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											
											}else{
												
												$point_reward= $order_bill*$ptsperprice;
												$point_reward_points= $order_bill*$ptsperprice;
											}
											
										}else{
											
											$point_reward= $order_bill;
											$point_reward_points= $order_bill;
										}
										
										$point_reward = ($login_point_first+$point_reward);
									}
									
									if($order_point_val!='')
									{
										$point_reward =($point_reward+$order_point_val);
										
									}
									
									if($phoen_data_reviews !='')
									{
										$point_reward =($point_reward+$phoen_data_reviews);
									
									}
									
									if($payment_gatway_val!='')
									{
										$point_reward =($point_reward+$payment_gatway_val);
									}
									
									if($gift_birthday_points_val!='')
									{
										$point_reward =($point_reward+$gift_birthday_points_val);
									}
									if($i=='0')
									{
									
										if($phoen_reward_referral_user_pointsss!='0')
										{
											$point_reward =($point_reward+$phoen_reward_referral_user_pointsss);
											
										}
									}
									
									if($phoeni_first_login_points_myaccount!='')
									{
										$point_reward =($point_reward+$phoeni_first_login_points_myaccount);
									}
									
									if($phoen_range_points!='')
									{
										$point_reward =($point_reward+$phoen_range_points);
									}
									
								}
								
								$tpoint_reward+=$used_reward_point+$point_reward;
							
								$total_point_reward+=$tpoint_reward;
								
								if($phoen_complited_date!='')
								{
									if($phoen_data_reviews !='')
									{
										?>
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Points Earn For Product Review','phoen-rewpts'); ?> </td>
											<td><?php echo  round($phoen_data_reviews) ;?></td>
											<td>-</td>
											<td><?php echo  "+".round($phoen_data_reviews) ;?></td>
											<td><?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										
										<?php
									}
									
									if($payment_gatway_val !='')
									{
										?>
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Points Earn For payment gateway Use','phoen-rewpts'); ?> </td>
											<td><?php echo  round($payment_gatway_val) ;?></td>
											<td>-</td>
											<td><?php echo  "+".round($payment_gatway_val) ;?></td>
											<td><?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										
										<?php
									}
									
									if($gift_birthday_points_val!='')
									{
										
										?>
									<tr>
										<td><?php echo $phoen_bday_date ;?></td>
										<td><?php _e('Birth Day Gift Points','phoen-rewpts'); ?> </td>
										<td><?php echo round($gift_birthday_points_val) ;?></td>
										<td><?php echo "-"; ?></td>
										<td><?php echo  "+".round($gift_birthday_points_val) ;?></td>
										<td>
										<?php echo $phoen_expire_date_data ; ?></td>
									</tr>
									<?php
									
									}
									
									if($used_reward_point !='0' && $used_reward_point!='')
									{
										?>
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
											<td>-</td>
											<td><?php 
											if($used_reward_point =='0')
											{
												echo "-";
											}else{
												
												echo round($used_reward_point);
											}
											
											?></td>
											<td><?php echo round($used_reward_point) ;?></td>
											<td>-</td>
										</tr>
										<?php
									}
									if($phoen_range_points!='')
									{
									
										if($point_reward_points=='0')
										{
											$total_point_rewards_range = $total_point_reward;
											
										}else{
											
											$total_point_rewards_range = $phoen_range_points;
										}
									
										?>
									
										<tr>
											<td><?php echo $phoen_complited_date ;?></td>
											<td><?php _e('Bonus Points For Purchase','phoen-rewpts'); ?> </td>
											<td><?php echo round($phoen_range_points) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($total_point_rewards_range) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
										
										
									}
									
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo round($point_reward_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_reward) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
									
								}else{
									$phoen_current_date = date("d-m-Y");
									$point_reward_points=isset($point_reward_points)?$point_reward_points:'';
									if($used_reward_point !='0' && $used_reward_point!='')
									{
										?>
										<tr>
											<td><?php echo $phoen_current_date ;?></td>
											<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
											<td>-</td>
											<td><?php 
											if($used_reward_point =='0')
											{
												echo "-";
											}else{
												
												echo round($used_reward_point);
											}
											
											?></td>
											<td><?php echo round($used_reward_point) ;?></td>
											<td>-</td>
										</tr>
										<?php
									}
									
									if($point_reward_points!='' && $point_reward_points!='0')
										{
											?>
											<tr>
												<td><?php echo $phoen_current_date ;?></td>
												<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo "0" ; //round($point_reward_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_reward) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
										}	
									
								}
							}else{
								
								if($phoen_update_date_val<$phoen_current_date)	
								{
									
									if($products_order[$i]->post_status=="wc-completed")
									{
									
										if($login_point_first =='')
										{
											
											if($bill_price_checked_value !='')
											{
											
												if($product_percentage=='1')
												{
													$point_reward= $order_bill;
													$point_reward_points= $order_bill;
												
												}else{
													
													$point_reward= $order_bill*$ptsperprice;
													$point_reward_points= $order_bill*$ptsperprice;
												}
												
												
											}else{
												
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											}
									
										}else{
											
											if($bill_price_checked_value !='')
											{
												$point_reward= $order_bill*$ptsperprice;
												$point_reward_points= $order_bill*$ptsperprice;
												
											}else{
												
												$point_reward= $order_bill;
												$point_reward_points= $order_bill;
											}
											
											$point_reward = ($login_point_first+$point_reward);
										}
										
										if($order_point_val!='')
										{
											$point_reward =($point_reward+$order_point_val);
											
										}
										
										if($phoen_data_reviews !='')
										{
											$point_reward =($point_reward+$phoen_data_reviews);
										
										}
										
										if($payment_gatway_val!='')
										{
											$point_reward =($point_reward+$payment_gatway_val);
										}
										
										if($gift_birthday_points_val!='')
										{
											$point_reward =($point_reward+$gift_birthday_points_val);
										}
										
										if($phoen_add_update_points!='')
										{
											$point_reward =($point_reward+$phoen_add_update_points);
											
										}
										if($i=='0')
										{
											if($phoen_reward_referral_user_pointsss!='0')
											{
												$point_reward =($point_reward+$phoen_reward_referral_user_pointsss);
											}
										}	
										
										if($phoeni_first_login_points_myaccount!='')
										{
											$point_reward =($point_reward+$phoeni_first_login_points_myaccount);
										}
										
										if($phoen_range_points!='')
										{
											$point_reward =($point_reward+$phoen_range_points);
										}
										
										
									}
									
									$tpoint_reward+=$used_reward_point+$point_reward;
								
									$total_point_reward+=$tpoint_reward;
									
								
									
									if($phoen_complited_date!='')
									{
										if($phoen_data_reviews !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For Product Review','phoen-rewpts'); ?></td>
												<td><?php echo  round($phoen_data_reviews) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($phoen_data_reviews) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($payment_gatway_val !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For payment gateway Use','phoen-rewpts'); ?>  </td>
												<td><?php echo  round($payment_gatway_val) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($payment_gatway_val) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($gift_birthday_points_val!='')
										{
											
											?>
										<tr>
											<td><?php echo $phoen_bday_date ;?></td>
											<td><?php _e('Birth Day Gift Points','phoen-rewpts'); ?> </td>
											<td><?php echo round($gift_birthday_points_val) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($gift_birthday_points_val) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
										
										}
										
										if($used_reward_point !='0' && $used_reward_point!='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
												<td>-</td>
												<td><?php 
												if($used_reward_point =='0')
												{
													echo "-";
												}else{
													
													echo round($used_reward_point);
												}
												
												?></td>
												<td><?php echo round($used_reward_point) ;?></td>
												<td>-</td>
											</tr>
											<?php
										}
										
										if($total_point_reward=='0')
										{
											$total_point_reward = get_post_meta( $cur_id, 'phoes_customer_points_update_valss',true);
											$total_point_reward = get_user_meta($cur_id,'phoen_update_customer_points',true );
											
										}else{
											
											$total_point_reward =$total_point_reward;
											
										}
										
										if($phoen_range_points!='')
										{
										
											if($point_reward_points=='0')
											{
												$total_point_rewards_range = $total_point_reward;
												
											}else{
												
												$total_point_rewards_range = $phoen_range_points;
											}
										
											?>
										
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Bonus Points For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo round($phoen_range_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_rewards_range) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
											
											
										}
										
										
									
											//if($point_reward_points!='' && $point_reward_points!='0')
											//{
												
												?>
												
												<tr>
													<td><?php echo $phoen_complited_date ;?></td>
													<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
													<td><?php echo round($point_reward_points) ;?></td>
													<td><?php echo "-"; ?></td>
													<td><?php echo  "+".round($total_point_reward) ;?></td>
													<td>
													<?php echo $phoen_expire_date_data ; ?></td>
												</tr>
												<?php
											//}	
									}else{
									$phoen_current_date = date("d-m-Y");
									$point_reward_points=isset($point_reward_points)?$point_reward_points:'';
									if($used_reward_point !='0' && $used_reward_point!='')
									{
										?>
										<tr>
											<td><?php echo $phoen_current_date ;?></td>
											<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
											<td>-</td>
											<td><?php 
											if($used_reward_point =='0')
											{
												echo "-";
											}else{
												
												echo round($used_reward_point);
											}
											
											?></td>
											<td><?php echo round($used_reward_point) ;?></td>
											<td>-</td>
										</tr>
										<?php
									}
									
									if($point_reward_points!='' && $point_reward_points!='0')
										{
											?>
											<tr>
												<td><?php echo $phoen_current_date ;?></td>
												<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo "0" ; //round($point_reward_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_reward) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
										}	
									
								}
								}else{
									if($products_order[$i]->post_status=="wc-completed")
									{
									
										if($login_point_first =='')
										{
											
											if($bill_price_checked_value !='')
											{
											
												if($product_percentage=='1')
												{
													$point_rewards= $order_bill;
													$point_reward_points= $order_bill;
												
												}else{
													
													$point_rewards= $order_bill*$ptsperprice;
													$point_reward_points= $order_bill*$ptsperprice;
												}
												
											}else{
												
												$point_rewards= $order_bill;
												$point_reward_points= $order_bill;
											}
									
										}else{
											
											if($bill_price_checked_value !='')
											{
												if($product_percentage=='1')
												{
													$point_rewards= $order_bill;
													$point_reward_points= $order_bill;
												
												}else{
													
													$point_rewards= $order_bill*$ptsperprice;
													$point_reward_points= $order_bill*$ptsperprice;
												}
												
											}else{
												
												$point_rewards= $order_bill;
												$point_reward_points= $order_bill;
											}
											
											$point_rewards = ($login_point_first+$point_rewards);
										}
										
										if($order_point_val!='')
										{
											$point_rewards =($point_rewards+$order_point_val);
											
										}
										
										if($phoen_data_reviews !='')
										{
											$point_rewards =($point_rewards+$phoen_data_reviews);
										
										}
										
										if($payment_gatway_val!='')
										{
											$point_rewards =($point_rewards+$payment_gatway_val);
										}
										
										if($gift_birthday_points_val!='')
										{
											$point_rewards =($point_rewards+$gift_birthday_points_val);
										}
										if($i=='0')
										{
											if($phoen_reward_referral_user_pointsss!='0')
											{
												$point_rewards =($point_rewards+$phoen_reward_referral_user_pointsss);
											}
										}	
										
										if($phoeni_first_login_points_myaccount!='')
										{
											$point_rewards =($point_rewards+$phoeni_first_login_points_myaccount);
										}
										
										if($phoen_range_points!='')
										{
											$point_rewards =($point_rewards+$phoen_range_points);
										}
									
										
									}
									$tpoint_rewards ='';
									$tpoint_rewards+=$used_reward_point+$point_rewards;
									$total_point_rewards='';
									$total_point_rewards+=$tpoint_rewards;
									
									if($phoen_complited_date!='')
									{
										if($phoen_data_reviews !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For Product Review','phoen-rewpts'); ?> </td>
												<td><?php echo  round($phoen_data_reviews) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($phoen_data_reviews) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($payment_gatway_val !='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For payment gateway Use','phoen-rewpts'); ?>  </td>
												<td><?php echo  round($payment_gatway_val) ;?></td>
												<td>-</td>
												<td><?php echo  "+".round($payment_gatway_val) ;?></td>
												<td><?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											
											<?php
										}
										
										if($gift_birthday_points_val!='')
										{
											
											?>
										<tr>
											<td><?php echo $phoen_bday_date ;?></td>
											<td><?php _e('Birth Day Gift Points','phoen-rewpts'); ?> </td>
											<td><?php echo round($gift_birthday_points_val) ;?></td>
											<td><?php echo "-"; ?></td>
											<td><?php echo  "+".round($gift_birthday_points_val) ;?></td>
											<td>
											<?php echo $phoen_expire_date_data ; ?></td>
										</tr>
										<?php
										
										}
									
										if($used_reward_point !='0' && $used_reward_point!='')
										{
											?>
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
												<td>-</td>
												<td><?php 
												if($used_reward_point =='0')
												{
													echo "-";
												}else{
													
													echo round($used_reward_point);
												}
												
												?></td>
												<td><?php echo round($used_reward_point) ;?></td>
												<td>-</td>
											</tr>
											<?php
										}
									
										$total_point_rewardsss = get_post_meta( $cur_id, 'phoes_customer_points_update_valss',true);
										$total_point_rewardsss = get_user_meta($cur_id,'phoen_update_customer_points',true );
										
										$total_point_rewards = $total_point_rewardsss;
								
										if($phoen_range_points!='')
										{
										
											if($point_reward_points=='0')
											{
												$total_point_rewards_range = $total_point_rewards;
												
											}else{
												$total_point_rewards_range = $phoen_range_points;
											}
								
											?>
										
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Bonus Points For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo round($phoen_range_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_rewards_range) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
											
											
										}	
										
									
											?>
											
											<tr>
												<td><?php echo $phoen_complited_date ;?></td>
												<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
												<td><?php echo round($point_reward_points) ;?></td>
												<td><?php echo "-"; ?></td>
												<td><?php echo  "+".round($total_point_rewards) ;?></td>
												<td>
												<?php echo $phoen_expire_date_data ; ?></td>
											</tr>
											<?php
									
									}else{
										$phoen_current_date = date("d-m-Y");
										$point_reward_points=isset($point_reward_points)?$point_reward_points:'';
										if($used_reward_point !='0' && $used_reward_point!='')
										{
											?>
											<tr>
												<td><?php echo $phoen_current_date ;?></td>
												<td><?php _e('Spent Points For Purchase','phoen-rewpts'); ?> </td>
												<td>-</td>
												<td><?php 
												if($used_reward_point =='0')
												{
													echo "-";
												}else{
													
													echo round($used_reward_point);
												}
												
												?></td>
												<td><?php echo round($used_reward_point) ;?></td>
												<td>-</td>
											</tr>
											<?php
										}
										
										if($point_reward_points!='' && $point_reward_points!='0')
											{
												?>
												<tr>
													<td><?php echo $phoen_current_date ;?></td>
													<td><?php _e('Points Earn For Purchase','phoen-rewpts'); ?> </td>
													<td><?php echo "0" ; //round($point_reward_points) ;?></td>
													<td><?php echo "-"; ?></td>
													<td><?php echo  "+".round($total_point_rewards) ;?></td>
													<td>
													<?php echo $phoen_expire_date_data ; ?></td>
												</tr>
												<?php
											}	
										
									}
								}
								
							}	
					}	
			
				}
				
			}
		
					$current_user = wp_get_current_user();
					$cur_id = $current_user->ID;
					$phoen_current_datse = date("d-m-Y") ;
					$total_point_rewardss = get_post_meta( $cur_id, 'phoes_customer_points_update_valss',true);
					$total_point_rewardss = get_user_meta($cur_id,'phoen_update_customer_points',true );
					if($total_point_rewardss!='' && $total_point_rewardss!='0')
					{
						?>
					<tr>
					<td><?php echo $phoen_current_datse ;?></td>
					<td><?php _e('Admin Updated Points','phoen-rewpts'); ?> </td>
					<td><?php echo $total_point_rewardss; //round($point_reward_points) ;?></td>
					<td><?php echo "-"; ?></td>
					<td><?php echo  "+".round($total_point_rewardss) ;?></td>
					<td>
					<?php echo "-" ; ?></td>
					</tr>
					
					<?php
					}
				
				global $wp;
				$site_url =  home_url( $wp->request ); ?>
			</table>
					
						<?php echo "<p>(Page: ". $current . " of " . $result .")</p>"; ?>
    <? if($previous): ?>
        <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ).'?paged='.$previous; ?>">Previous</a>
    <? endif; ?>
    <? if($next) : ?>
        <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ).'?paged='.$next; ?>">Next</a>
    <? endif; 
			}
		
	?>
</p>