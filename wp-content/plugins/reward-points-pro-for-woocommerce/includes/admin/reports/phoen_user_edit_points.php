<?php if ( ! defined( 'ABSPATH' ) ) exit; 

	$user_id = isset($_GET['user'])?$_GET['user']:'';
	
	if(isset($_POST['update_points']))
	{
		$phoen_update_data = isset($_POST['phoen_update_data'])?$_POST['phoen_update_data']:'';
		$phoen_hidden_user_id=isset($_POST['phoen_hidden_user_id'])?$_POST['phoen_hidden_user_id']:'';
		
		$phoen_current_dates_update = new DateTime();
		
		$phoen_current_dates_updates = $phoen_current_dates_update->format('d-m-Y H:i:s');
			
		update_post_meta( $phoen_hidden_user_id, 'phoes_customer_points_update_valss', $phoen_update_data );
			
		update_option('phoeni_update_dates',$phoen_current_dates_updates);
		
		update_post_meta($phoen_hidden_user_id,'phoeni_update_dates_checkeds',$phoen_current_dates_updates);
		
		update_post_meta( $phoen_hidden_user_id, 'phoes_customer_points_update_valss_empty', $phoen_update_data );
		
		update_user_meta( $phoen_hidden_user_id, 'phoen_update_customer_points', $phoen_update_data);
			
		update_user_meta( $phoen_hidden_user_id, 'phoen_update_date', $phoen_current_dates_updates);
		
		update_user_meta($phoen_hidden_user_id,'phoen_update_customer_hiden_val',$phoen_update_data);
		
	
	}
	
	?>
<div class="wrap">

	<h1 class="wp-heading-inline"><?php _e('MANAGE CUSTOMER POINTS','phoen-rewpts'); ?></h1>

	<a class="page-title-action" href="?page=phoeniixx_reward_order"><?php _e('Back To Report','phoen-rewpts');?></a>

	<br /><br />
	<table class="wp-list-table widefat fixed striped customers">
				
	<thead>
		
		<tr class="phoen_rewpts_user_reward_point_tr">
			
			<th class=" column-customer_name " scope="col"><span><?php _e('Customer Email Id','phoen-rewpts'); ?></span>
				
			</th>

			<th class=" column-spent" scope="col"><span><?php _e('Reward Points','phoen-rewpts'); ?></span>
				
			</th>
			
			<th class=" column-spent" scope="col"><span><?php _e('Update','phoen-rewpts'); ?></span>
				
			</th>

		</tr>
		
	</thead>	
	
	<tbody>	
			<?php 
			
			global $woocommerce;
			
			$curr=get_woocommerce_currency_symbol();
			
			$products_order = get_posts( array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => $user_id,
				'post_type'   => 'shop_order',
				'order' => 'ASC',
				'post_status' => array_keys( wc_get_order_statuses() ),
			) );
			
			$user_data_by_email = get_userdata( $user_id );
			
			$user = get_user_by( 'ID', $user_id);
			
				$total_point_reward=0;
				
				$total_point_reward_empty=0;
				
				$order_count=0;
				
				$amount_spent=0;
				
				$tpoint_reward_new_val=0;
				
				$id=$user_id;
				
				$check_order_count=phoen_order_count($id); 
				
				$phoen_current_dates_updates_val = get_post_meta($user_id,'phoeni_update_dates_checkeds',true);
				$phoen_current_dates_updates_val = get_user_meta($user_id,'phoen_update_date', true );
				
				$phoen_update_date_val=strtotime($phoen_current_dates_updates_val);
			
					?>
					<tr>
					
						<td class="customer_name " ><?php echo $usert_email = isset($user->user_email)?$user->user_email:'' ; ?></td>
					
						<?php 	
						
						$phoen_add_reff_points=0;
						$phoen_reward_referral_user_points = get_user_meta( $user_id, 'phoen_reward_referral_user_points', true );
						if(!empty($phoen_reward_referral_user_points))
						{
							for($j=0; $j<count($phoen_reward_referral_user_points); $j++)
							{
								$phoen_add_reff_points+=$phoen_reward_referral_user_points[$j];
							}
						}	
						
						$reward_point_value_data = phoen_reward_point_value();
	
						extract($reward_point_value_data);
						
						$phoen_update_date = get_option('phoen_update_dates');
						
						for($i=0;$i<count($products_order);$i++) {	
						
							$products_detail=get_post_meta($products_order[$i]->ID); 
							
							$gen_settings=get_post_meta( $products_order[$i]->ID, 'phoe_rewpts_order_status', true );
							
							$phoen_user_id=isset($gen_settings['user_id'])?$gen_settings['user_id']:'';
							
							if($phoen_user_id==$user_id)	
							{
								
								
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
												
												if($i=='0')
												{
													if($phoen_add_reff_points!='0')
													{
														$point_reward =($point_reward+$phoen_add_reff_points);
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
											
											$tpoint_reward_new_val+=$used_reward_point+$point_reward;
										
											$tpoint_rewards+=$used_reward_point+$point_reward;
											 
											$total_point_reward_empty+=$tpoint_rewards;
											
											$amount_spent+=$order_bill; 
											
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
													
													if($i=='0')
													{
														
														if($phoen_add_reff_points!='0')
														{
															$point_reward =($point_reward+$phoen_add_reff_points);
														}
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
												
												$amount_spent+=$order_bill; 
												
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
												if($i=='0')
												{
													if($phoen_add_reff_points!='0')
													{
														$point_reward =($point_reward+$phoen_add_reff_points);
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
											
											$tpoint_reward_new_val+=$used_reward_point+$point_reward;
										
											$tpoint_rewards+=$used_reward_point+$point_reward;
											 
											$total_point_reward_empty+=$tpoint_rewards;
											
											$amount_spent+=$order_bill; 
											
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
													
													
													if($phoen_add_update_points!='')
													{
														$point_reward =($point_reward+$phoen_add_update_points);
													}
													
													if($gift_birthday_points_val!='')
													{
														$point_reward =($point_reward+$gift_birthday_points_val);
													}
													if($i=='0')
													{
														if($phoen_add_reff_points!='0')
														{
															$point_reward =($point_reward+$phoen_add_reff_points);
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
												
												$tpoint_reward_new_val+=$used_reward_point+$point_reward;
										
												$tpoint_reward+=$used_reward_point+$point_reward;
												 
												$total_point_reward+=$tpoint_reward;
												
												$amount_spent+=$order_bill; 
												
												$order_count++; 
											
											}
											
										}
								}	
									
							}	
									
						}	
				
						
						$total_point_rewardss = get_post_meta( $user_id, 'phoes_customer_points_update_valss',true);
						$total_point_rewardss = get_user_meta($user_id,'phoen_update_customer_points',true );
				
						if($total_point_rewardss=='')
						{
							$total_point_reward=$total_point_reward_empty;
							
						}else{
							
							if($total_point_reward=='0')
							{
								$total_point_reward = get_post_meta( $user_id, 'phoes_customer_points_update_valss',true);
								$total_point_reward = get_user_meta($user_id,'phoen_update_customer_points',true );
								
							}else{
								
								$total_point_reward = $total_point_reward;
								
							}
							
						}
						
						$phoen_current_date = new DateTime();
						$phoen_current_dates = $phoen_current_date->format('d-m-Y');
						$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
						$phoen_points_assignment_date_val=isset($phoe_rewpts_page_settings_value['phoen_points_assignment_date'])?$phoe_rewpts_page_settings_value['phoen_points_assignment_date']:'';
						$phoen_points_assignment_date_vals = date("d-m-Y", strtotime($phoen_points_assignment_date_val));
						$phoen_current_datess=strtotime($phoen_current_dates);				
						$phoen_expiry_datse_to_assign_points=strtotime($phoen_points_assignment_date_vals);
						
						if($phoen_current_datess <= $phoen_expiry_datse_to_assign_points || $phoen_points_assignment_date_val=='')
						{
						
							if($total_point_rewardss=='')
							{

								$phoen_reward_referral_user_id = get_user_meta( $user_id, 'phoen_reward_referral_user_id', true );
								$gift_birthday_points_valss=0;
								
								$gift_birthday_points_valss = get_post_meta($user_id,'phoeni_rewords_gift_dob_point',true);
								
								$check_order_count=phoen_order_count($user_id); 	
								
								if($check_order_count=='0')
								{
								
									$total_point_reward = $phoen_add_reff_points+$total_point_reward;
									
									if($gift_birthday_points_valss!='' && $gift_birthday_points_valss!='0')
									{
										$total_point_reward = $total_point_reward+$gift_birthday_points_valss;
									}
									
									
									$phoen_first_login_points = get_post_meta($user_id,'phoen_reward_points_for_register_user',true);
									$phoen_reward_points_for_register_user_id = get_post_meta($user_id,'phoen_reward_points_for_register_user_id',true);		
									if($phoen_reward_points_for_register_user_id==$user_id)
									{
										if($phoen_first_login_points!='')
										{
											
											$total_point_reward = $phoen_first_login_points+$total_point_reward ;
											
										}
									
									}
								} 
							
							}
						}	
			
				
						?>
				
						<td class=" column-orders" ><?php echo round($total_point_reward); ?></td>
						
						<td class=" column-spent" >
						
							<form method="post">
							
								<input type="text" name="phoen_update_data" value="<?php echo round($total_point_reward) ; ?>">
								<input type="hidden" name="phoen_hidden_user_id" value="<?php echo $user_id ; ?>">
								
								<input type="submit" name="update_points" value="Update">
									
							</form>		
							
						</td>
					
					</tr>
					
					<?php 	
				
				
				?>
	</tbody>
	
	<tfoot>
					
		<tr class="phoen_rewpts_user_reward_point_tr">
		
			<th class=" column-customer_name " scope="col"><span><?php _e('Customer Email Id','phoen-rewpts'); ?></span></th>

			<th class=" column-spent" scope="col"><span><?php _e('Reward Points','phoen-rewpts'); ?></span></th>
			
			<th class=" column-spent" scope="col"><span><?php _e('Update','phoen-rewpts'); ?></span>	</th>

		</tr>
		
	</tfoot>	
</table>
</div>
<?php	