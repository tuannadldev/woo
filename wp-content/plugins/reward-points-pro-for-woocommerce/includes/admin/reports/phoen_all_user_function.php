<?php 
global $woocommerce, $wpdb;

$curr=get_woocommerce_currency_symbol();
$offset = 0;
$posts_per_page = 500;
$args = array(
		'post_type'      => 'shop_order',
		//'fields'         => 'ids',
		'offset'         => $paged,
		'posts_per_page' => $posts_per_page,
		'post_status'    =>array_keys(wc_get_order_statuses())
		
	 
	);
	
$offset += $posts_per_page;
	
$products_order = get_posts( $args ); 

global $wpdb,$post;

$limit = 50;  

if (isset($_GET["paged"])) { $page  = $_GET["paged"]; } else { $page=1; };  

$start_from = ($page-1) * $per_page;

$user_detail = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->users WHERE 1 $do_search ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $start_from), ARRAY_A);

$data_count=0;

$all_user_list_data = array();

for($a=0;$a<count($user_detail);$a++) {
	
	$total_point_reward=0;
	
	$order_count=0;
	
	$amount_spent=0;
	
	$tpoint_reward_new_val=0;
	
	$total_point_reward_empty=0;

	$id = $user_detail[$a]['ID'];
	
	$phoen_current_dates_updates_val = get_post_meta($user_detail[$a]['ID'],'phoeni_update_dates_checkeds',true);
	$phoen_current_dates_updates_val = get_user_meta($user_detail[$a]['ID'],'phoen_update_date', true );
	
	$phoen_update_date_val=strtotime($phoen_current_dates_updates_val);
	
	$phoen_reward_referral_user_points = get_user_meta( $id, 'phoen_reward_referral_user_points', true );
	
		$phoen_add_reff_points=0;
		
		if(!empty($phoen_reward_referral_user_points))
		{
			for($j=0; $j<count($phoen_reward_referral_user_points); $j++)
			{
			
				if($phoen_reward_referral_user_points[$j]!='')
				{
					$phoen_add_reff_points+=$phoen_reward_referral_user_points[$j];
				}
				
			}
		
		}

		
	$phoen_reward_referral_user_id = get_user_meta( $id, 'phoen_reward_referral_user_id', true );
			
	$check_order_count=phoen_order_count($id); 

		$all_user_list_data [$a]['user_email'] = $user_detail[$a]['user_email']; 
		$all_user_list_data [$a]['ID'] = $user_detail[$a]['ID'];
		
		$gen_val = get_option('phoe_rewpts_value');
		
		$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value',true);
		
		$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
		
		$reward_point_value_data = phoen_reward_point_value();

		extract($reward_point_value_data);
			
		$phoen_update_date = get_option('phoen_update_dates');
			
			for($i=0;$i<count($products_order);$i++) {	
			
				$products_detail=get_post_meta($products_order[$i]->ID); 
			
				$gen_settings=get_post_meta( $products_order[$i]->ID, 'phoe_rewpts_order_status', true );
				
				$phoen_user_id=isset($gen_settings['user_id'])?$gen_settings['user_id']:'';
				
				if($phoen_user_id==$user_detail[$a]['ID'] || $products_detail['_customer_user'][0]==$user_detail[$a]['ID'])	
				{
					$data_count++;
					
					$phoen_expiry_date=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
					
					if($phoen_expiry_date!='')
					{
						
						$phoen_current_dates = date("d-m-Y") ;
						
						$phoen_current_dates=strtotime($phoen_current_dates);
						
						$phoen_expiry_date=strtotime($phoen_expiry_date);

						if($phoen_current_dates < $phoen_expiry_date)
						{
							
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
							
							$product_purchase_points_val=isset($gen_settings['product_purchase_points_val'])?$gen_settings['product_purchase_points_val']:'';
							
							$bill_price_checked_value=isset($gen_settings['bill_price_checked_value'])?$gen_settings['bill_price_checked_value']:'';
							
							$gift_birthday_points_val = get_post_meta($id,'phoeni_rewords_gift_dob_point',true);
							
							$phoeni_first_login_points_myaccount=isset($gen_settings['phoen_first_login_points_myaccount'])?$gen_settings['phoen_first_login_points_myaccount']:'';
							
							$totale_percentage_points=isset($gen_settings['totale_percentage_points'])?$gen_settings['totale_percentage_points']:'';
							
							$product_percentage=isset($gen_settings['product_percentage'])?$gen_settings['product_percentage']:'';
							$product_sale_price=0;
							$product_sale_price=isset($gen_settings['price'])?$gen_settings['price']:'0';
							
							$phoen_range_points=isset($gen_settings['phoen_range_points'])?$gen_settings['phoen_range_points']:'';
							
							
							$point_reward=0;
							$tpoint_reward=0;
							$tpoint_rewards=0;
					
							
							$phoen_current_date=isset($gen_settings['current_date'])?$gen_settings['current_date']:'';
							$phoen_current_date=strtotime($phoen_current_date);
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
									
									if($phoen_add_reff_points!='0')
									{
										$point_reward =($point_reward+$phoen_add_reff_points);
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
								
								$tpoint_reward_new_val+=$used_reward_point+$point_reward;
							
								$tpoint_rewards+=$used_reward_point+$point_reward;
								 
								$total_point_reward_empty+=$tpoint_rewards;
								
								$amount_spent+=$product_sale_price; 
								
								$order_count++; 
								
								
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
									
										if($phoen_add_reff_points!='0')
										{
											$point_reward =($point_reward+$phoen_add_reff_points);
										}
										
										if($phoen_add_update_points!='')
										{
											$point_reward =($point_reward+$phoen_add_update_points);
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
									
									$tpoint_reward_new_val+=$used_reward_point+$point_reward;
							
									$tpoint_reward+=$used_reward_point+$point_reward;
									 
									$total_point_reward+=$tpoint_reward;
									
									$amount_spent+=$product_sale_price; 
									
									$order_count++; 
								
								}
								
							}
						
						}
					}else{
						
						
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
							
							$product_purchase_points_val=isset($gen_settings['product_purchase_points_val'])?$gen_settings['product_purchase_points_val']:'';
							
							$bill_price_checked_value=isset($gen_settings['bill_price_checked_value'])?$gen_settings['bill_price_checked_value']:'';
							
							$gift_birthday_points_val = get_post_meta($id,'phoeni_rewords_gift_dob_point',true);
							
							$phoeni_first_login_points_myaccount=isset($gen_settings['phoen_first_login_points_myaccount'])?$gen_settings['phoen_first_login_points_myaccount']:'';
							
							$totale_percentage_points=isset($gen_settings['totale_percentage_points'])?$gen_settings['totale_percentage_points']:'';
							
							$product_percentage=isset($gen_settings['product_percentage'])?$gen_settings['product_percentage']:'';
							$product_sale_price=0;
							$product_sale_price=isset($gen_settings['price'])?$gen_settings['price']:'0';
							
							$phoen_range_points=isset($gen_settings['phoen_range_points'])?$gen_settings['phoen_range_points']:'';
							
							$point_reward=0;
							$tpoint_reward=0;
							$tpoint_rewards=0;
					
							$phoen_current_date=isset($gen_settings['current_date'])?$gen_settings['current_date']:'';
							$phoen_current_date=strtotime($phoen_current_date);
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
									
									if($phoen_add_reff_points!='0')
									{
										$point_reward =($point_reward+$phoen_add_reff_points);
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
								
								$tpoint_reward_new_val+=$used_reward_point+$point_reward;
							
								$tpoint_rewards+=$used_reward_point+$point_reward;
								 
								$total_point_reward_empty+=$tpoint_rewards;
								
								$amount_spent+=$product_sale_price; 
								
								$order_count++; 
								
								
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
										
										
										if($phoen_add_reff_points!='0')
										{
											$point_reward =($point_reward+$phoen_add_reff_points);
										}
									
										if($phoen_add_update_points!='')
										{
											$point_reward =($point_reward+$phoen_add_update_points);
										}
										
										if($phoeni_first_login_points_myaccount!='')
										{
											$point_reward =($point_reward+$phoeni_first_login_points_myaccount);
											
										}
										
										/* if($totale_percentage_points!='' && $totale_percentage_points!='0')
										{
											$point_reward =($point_reward+$totale_percentage_points);
											
										} */
										
										if($phoen_range_points!='')
										{
											$point_reward =($point_reward+$phoen_range_points);
										}
										
										
									}
									
									$tpoint_reward_new_val+=$used_reward_point+$point_reward;
							
									$tpoint_reward+=$used_reward_point+$point_reward;
									 
									$total_point_reward+=$tpoint_reward;
									
									$amount_spent+=$product_sale_price; 
									
									$order_count++; 
								
								}
								
							}
					}	
				}	
						
			}	
	
			
			$total_point_rewardss = get_post_meta( $user_detail[$a]['ID'], 'phoes_customer_points_update_valss',true);
			$total_point_rewardss = get_user_meta($user_detail[$a]['ID'],'phoen_update_customer_points',true );
			
			if($total_point_rewardss=='')
			{
				$total_point_reward=$total_point_reward_empty;
				
			}else{
				
				if($total_point_reward=='0')
				{
					$total_point_reward = get_post_meta( $user_detail[$a]['ID'], 'phoes_customer_points_update_valss',true);
					$total_point_reward = get_user_meta($user_detail[$a]['ID'],'phoen_update_customer_points',true );
				
				}else{
					
					$total_point_reward = $total_point_reward;
				}
			
			}
			
			$phoen_current_date = new DateTime();
			$phoen_current_dates = $phoen_current_date->format('d-m-Y');
			
			$phoen_points_assignment_date_val=isset($phoe_rewpts_page_settings_value['phoen_points_assignment_date'])?$phoe_rewpts_page_settings_value['phoen_points_assignment_date']:'';
			$phoen_points_assignment_date_vals = date("d-m-Y", strtotime($phoen_points_assignment_date_val));
			$phoen_current_datess=strtotime($phoen_current_dates);				
			$phoen_expiry_datse_to_assign_points=strtotime($phoen_points_assignment_date_vals);
			
			if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
			{
			
				if($total_point_rewardss=='')
				{
				
					$gift_birthday_points_valss=0;
					$gift_birthday_points_valss = get_post_meta($id,'phoeni_rewords_gift_dob_point',true);
					$phoen_gist_birthday_userid =get_post_meta($id,'phoeni_rewords_gift_dob_point_userid',true);
				
					if($check_order_count=='0')
					{
						if($phoen_reward_referral_user_id==$id)
						{
					
							$total_point_reward = $phoen_add_reff_points;
						
						}
						
						if($phoen_gist_birthday_userid==$id)
						{
							$total_point_reward =($total_point_reward+$gift_birthday_points_valss);
						}
						
							
						
					
						$phoen_first_login_points = get_post_meta($id,'phoen_reward_points_for_register_user',true);
						$phoen_reward_points_for_register_user_id = get_post_meta($id,'phoen_reward_points_for_register_user_id',true);		
						if($phoen_reward_points_for_register_user_id==$id)
						{
							if($phoen_first_login_points!='')
							{
								
								$total_point_reward = $phoen_first_login_points+$total_point_reward ;
								
							}
						
						}
				
					}
				}
			}	
				

		$all_user_list_data [$a]['order_count'] =  $order_count; 
		
	  $amount_spent = round($amount_spent) ; 
		
		$all_user_list_data [$a]['amount_spent'] =  $curr.$amount_spent; 
		
		$all_user_list_data [$a]['total_point_reward'] = round($total_point_reward); 
		
		 $all_user_list_data [$a]['amount_in_wallet'] = $curr.round($total_point_reward/$reedem_value,2);

}
$this->items = $all_user_list_data;