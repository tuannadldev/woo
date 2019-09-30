<?php

add_action( 'wp_loaded', 'phoen_reward_dob_cron_job' );

add_action('phoen_reward_points_cron_hook','phoen_reward_point_mail');

function phoen_reward_dob_cron_job() {
 
	if ( ! wp_next_scheduled( 'phoen_reward_points_cron_hook' ) ) {
			
		wp_schedule_event( $_SERVER['REQUEST_TIME'], 'daily', 'phoen_reward_points_cron_hook' );
		
	}
} 

function phoen_reward_point_mail(){
	
	global $wpdb,$woocommerce;

$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
	
$gift_birthday_points_val=isset($phoen_rewpts_set_point_data['gift_birthday_points'])?$phoen_rewpts_set_point_data['gift_birthday_points']:'';

$subject='Birthday Gift Points';

 $msg = '<div style="background-color:#f5f5f5;width:100%;margin:0;padding:70px 0 70px 0">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td valign="top" align="center">
						<div></div>
                    	<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-radius:6px!important;background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:6px!important">
						<tbody>
							<tr>
								<td valign="top" align="center">
                                    
                                	<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#557da1" style="background-color:#557da1;color:#ffffff;border-top-left-radius:6px!important;border-top-right-radius:6px!important;border-bottom:0;font-family:Arial;font-weight:bold;line-height:100%;vertical-align:middle">
										<tbody>
											<tr>
												<td>
													<h1 style="color:#ffffff;margin:0;padding:28px 24px;display:block;font-family:Arial;font-size:30px;font-weight:bold;text-align:left;line-height:150%">'.
													$subject.
													
													'</h1>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
                            </tr>
							
							<tr>
								<td valign="top" align="center">
									<table width="600" cellspacing="0" cellpadding="10" border="0" style="border-top:0">
										<tbody>
											<tr>
												<td valign="top">
													<table width="100%" cellspacing="0" cellpadding="10" border="0">
														<tbody>
															<tr>
																<td valign="middle" style="border:0;color:#99b1c7;font-family:Arial;font-size:12px;line-height:125%;text-align:center" colspan="2">
																	<h3>Happy Birthday..</h3>
																	
																	<p>Congratulation!! on the occasion of your Birthday we are giving you '.$gift_birthday_points_val. 'reward points</p>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
                             </tr>
							
						 </tbody>
						</table>
					  </td>
					</tr>
				</tbody>
				</table>
				</div>';  
      
        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 

		
		$phoen_rewoar_user_sql ="SELECT * FROM $wpdb->users WHERE 1 ORDER BY ID ASC";
		$user_detail = $wpdb->get_results(  $phoen_rewoar_user_sql,ARRAY_A);
		
		$phoen_current_dates = date("d-m") ;
		
		$bday_date = date('d-m-Y H:i:s');
						
		foreach($user_detail as $key=>$phoen_user_details)
		{
			$phoen_user_id = $phoen_user_details['ID'];
			
			$phoen_get_dob = get_user_meta($phoen_user_id, 'phoen_reward_dob_user_data', true);
			
			$user_dob_points = date('d-m', strtotime($phoen_get_dob));
			
			$subject='Birthday Gift Points';

			if($phoen_current_dates ==$user_dob_points)
			{
				$phoen_user_id = $phoen_user_details['ID'];
				$user_email_id = $phoen_user_details['user_email'];
				
			
				
				update_post_meta($phoen_user_id,'phoeni_rewords_gift_dob_point',$gift_birthday_points_val);
				update_post_meta($phoen_user_id,'phoeni_rewords_gift_dob_point_date',$bday_date);
				update_post_meta($phoen_user_id,'phoeni_rewords_gift_dob_point_userid',$phoen_user_id);
				
				mail( $user_email_id, $subject, $msg,$header); 
		
			}
			
		}
		

		
		$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);
		$phoen_points_expiry_before=isset($phoen_rewpts_notification_data['phoen_points_expiry_before'])?$phoen_rewpts_notification_data['phoen_points_expiry_before']:'';
		$phoen_points_notification_before=isset($phoen_rewpts_notification_data['phoen_points_notification_before'])?$phoen_rewpts_notification_data['phoen_points_notification_before']:'';
		if($phoen_points_expiry_before!='' || $phoen_points_notification_before!='')
		{
				global $woocommerce, $wpdb;
				
				$curr=get_woocommerce_currency_symbol();
				
				$argsm    = array('posts_per_page' => -1, 'order' => 'ASC',  'post_type' => 'shop_order','post_status'=>array_keys(wc_get_order_statuses()));
				
				$products_order = get_posts( $argsm ); 
				
				global $wpdb,$post;
			
				 $phoen_rewoar_user_sql ="SELECT * FROM $wpdb->users WHERE 1 ORDER BY user_email ASC";
				   
				 $user_detail = $wpdb->get_results(  $phoen_rewoar_user_sql,ARRAY_A);
				
				$data_count=0;
					
				
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
					
				
						$user_email_id = $user_detail[$a]['user_email']; 
							
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
								
									$current_date=isset($gen_settings['current_date'])?$gen_settings['current_date']:'';
									$phoen_expiry_date_add_mail=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
							
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
							
							$phoen_points_assignment_date_val=isset($gen_val['phoen_points_assignment_date'])?$gen_val['phoen_points_assignment_date']:'';
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
						
						
						
					if($phoen_expiry_date_add_mail!='') 
					{
						
						if($total_point_reward!='0')
						{
						
							$createDated = new DateTime($phoen_expiry_date_add_mail);
							
							$orders_current_dates = $createDated->format('d-m-Y');
							
							$exp_date_set=("-".$phoen_points_expiry_before."  "."day");
							
							$phoen_after_exp_date = date('d-m-Y', strtotime($exp_date_set, strtotime($orders_current_dates)));
							
							$phoen_current_dates = date("d-m-Y") ;
							
							$phoen_current_dates=strtotime($phoen_current_dates);
							
							$phoen_after_exp_dates=strtotime($phoen_after_exp_date);

							if($phoen_current_dates >= $phoen_after_exp_dates)
							{
								if($phoen_points_expiry_before!='')
								{
							
									$subject='Your Points Will Expire';

									 $msg = '<div style="background-color:#f5f5f5;width:100%;margin:0;padding:70px 0 70px 0">
													<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
														<tr>
															<td valign="top" align="center">
															<div></div>
															<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-radius:6px!important;background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:6px!important">
															<tbody>
																<tr>
																	<td valign="top" align="center">
																		
																		<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#557da1" style="background-color:#557da1;color:#ffffff;border-top-left-radius:6px!important;border-top-right-radius:6px!important;border-bottom:0;font-family:Arial;font-weight:bold;line-height:100%;vertical-align:middle">
																			<tbody>
																				<tr>
																					<td>
																						<h1 style="color:#ffffff;margin:0;padding:28px 24px;display:block;font-family:Arial;font-size:30px;font-weight:bold;text-align:left;line-height:150%">'.
																						$subject.
																						
																						'</h1>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
																
																<tr>
																	<td valign="top" align="center">
																		<table width="600" cellspacing="0" cellpadding="10" border="0" style="border-top:0">
																			<tbody>
																				<tr>
																					<td valign="top">
																						<table width="100%" cellspacing="0" cellpadding="10" border="0">
																							<tbody>
																								<tr>
																									<td valign="middle" style="border:0;color:#99b1c7;font-family:Arial;font-size:12px;line-height:125%;text-align:center" colspan="2">
																										<h3>This is to remind you that your points will expire on  '.$phoen_expiry_date_add_mail.'</h3>
																										<h3>Currently You Have Points</h3>
																										<p>'.$total_point_reward. '</p>
																										
																									</td>
																								</tr>
																							</tbody>
																						</table>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																 </tr>
																
															 </tbody>
															</table>
														  </td>
														</tr>
													</tbody>
													</table>
													</div>';  
				  
										$header = "MIME-Version: 1.0\r\n";
										$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
										
										mail( $user_email_id, $subject, $msg,$header); 
									
								}
						
							}
							
						}
						
					}	
						
						
						
						if($current_date!='') 
						{
							
							if($total_point_reward!='0')
							{
							
								$createDate = new DateTime($current_date);
								$orders_current_date = $createDate->format('d-m-Y');
								
								$notification_date_set=("-".$phoen_points_noticication_before." "."month");
								
								$phoen_after_notification_date = date('d-m-Y', strtotime($notification_date_set, strtotime($orders_current_date)));
								
								$phoen_current_dates=strtotime($phoen_current_dates);
								
								$phoen_after_noti_dates=strtotime($phoen_after_notification_date);
								
								if($phoen_current_dates >= $phoen_after_noti_dates)
								{
									if($phoen_points_noticication_before!='')
									{
								
										$subject='Currently You Have Points';

										 $msg = '<div style="background-color:#f5f5f5;width:100%;margin:0;padding:70px 0 70px 0">
														<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
														<tbody>
															<tr>
																<td valign="top" align="center">
																<div></div>
																<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-radius:6px!important;background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:6px!important">
																<tbody>
																	<tr>
																		<td valign="top" align="center">
																			
																			<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#557da1" style="background-color:#557da1;color:#ffffff;border-top-left-radius:6px!important;border-top-right-radius:6px!important;border-bottom:0;font-family:Arial;font-weight:bold;line-height:100%;vertical-align:middle">
																				<tbody>
																					<tr>
																						<td>
																							<h1 style="color:#ffffff;margin:0;padding:28px 24px;display:block;font-family:Arial;font-size:30px;font-weight:bold;text-align:left;line-height:150%">'.
																							$subject.
																							
																							'</h1>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																		</td>
																	</tr>
																	
																	<tr>
																		<td valign="top" align="center">
																			<table width="600" cellspacing="0" cellpadding="10" border="0" style="border-top:0">
																				<tbody>
																					<tr>
																						<td valign="top">
																							<table width="100%" cellspacing="0" cellpadding="10" border="0">
																								<tbody>
																									<tr>
																										<td valign="middle" style="border:0;color:#99b1c7;font-family:Arial;font-size:12px;line-height:125%;text-align:center" colspan="2">
																											<h3>Currently You Have Points</h3>
																											<p>'.$total_point_reward. '</p>
																											
																										</td>
																									</tr>
																								</tbody>
																							</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																		</td>
																	 </tr>
																	
																 </tbody>
																</table>
															  </td>
															</tr>
														</tbody>
														</table>
														</div>';  
					  
											$header = "MIME-Version: 1.0\r\n";
											$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
											
											mail( $user_email_id, $subject, $msg,$header); 
										
									}
							
								}
						
								
							}
							
						}
						
				}
		}		
		
}
?>