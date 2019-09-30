<?php 
/*
** Plugin Name: Reward Points Pro For Woocommerce

** Plugin URI: http://www.phoeniixx.com/

** Description: It is a plugin which provides the customers to get the reward points on the basis of the  purchase of the products or the money spent by them.

** Version: 3.6.8

** Author: phoeniixx

** Text Domain:phoen-rewpts

** Domain Path: /languages/

** Author URI: http://www.phoeniixx.com/

** License: GPLv2 or later

** License URI: http://www.gnu.org/licenses/gpl-2.0.html

** WC requires at least: 2.6.0

** WC tested up to: 3.5.5

**/  

if ( ! defined( 'ABSPATH' ) ) exit;
	
		
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		
		global $wpdb,$user;
	
		define('PHOEN_REWPTSPLUGURL',plugins_url(  "/", __FILE__));
	
		define('PHOEN_REWPTSPLUGPATH',plugin_dir_path(  __FILE__));
		
		include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/reports/phoen_all_user_wp_list_table.php');
		
		include_once(PHOEN_REWPTSPLUGPATH.'main_function.php');
		
		include_once(PHOEN_REWPTSPLUGPATH.'phoen_reward_menu_page.php');
		
		include_once(PHOEN_REWPTSPLUGPATH.'phoen_reward_library.php');
		
		include_once(PHOEN_REWPTSPLUGPATH.'includes/frontend/phoen_reward_register_form.php');
		
		include_once(PHOEN_REWPTSPLUGPATH.'includes/cart/phoen_apply_point_box.php');
		
		$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
		
		$phoen_role_datas = get_option('phoen_customer_role');
		
		$phoen_rol = get_option('role_wise');
		
		
			
			$enable_plugin_myaccount_val=isset($phoe_rewpts_page_settings_value['enable_plugin_myaccount'])?$phoe_rewpts_page_settings_value['enable_plugin_myaccount']:'';
			
			if($enable_plugin_myaccount_val=='1')
			{
				add_action( 'woocommerce_before_my_account',  'phoen_rew_cma_get_template'  );
				
				/* add_filter( 'wc_get_template', 'phoen_rew_cma_get_template', 10, 5 );*/
				
				function phoen_rew_cma_get_template( ) {
					
					wc_get_template( 'phoen-rewpts-dashboard.php', array(), '', plugin_dir_path( __FILE__ ) . 'includes/'  );
									
				} 
			
			}
	
		//admin user profile add date of birth
		include_once(plugin_dir_path(  __FILE__).'includes/phoen_rewpts_user_profile.php');
		
		include_once(plugin_dir_path(  __FILE__).'includes/phoen_products_wise_points.php');
		
		//checkout page notification
		include_once(PHOEN_REWPTSPLUGPATH.'includes/checkout/phoen_reward_checkout_message.php');
		
		// returns user reward amount
		  
		function phoen_rewpts_user_reward_amount()
		{
			//session_start();
			
			$total_point_reward=phoen_rewpts_user_reward_point();
				
			$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
			
			$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value',true);
			
			$reward_point_value_data = phoen_reward_point_value();
	
			extract($reward_point_value_data);
		
			$limit_points_use_oneorder=isset($phoe_rewpts_page_settings_value['limit_use_points'])?$phoe_rewpts_page_settings_value['limit_use_points']:'';
			
			if($limit_points_use_oneorder !='')
			{	
					
				if($total_point_reward <=$limit_points_use_oneorder)
				{
					
					$points =$total_point_reward;
				
				}else{
					
					$points =$limit_points_use_oneorder;
				}
			
			}else{
				$points = $total_point_reward;
			}
		
			$phoen_points_use_edit=isset($_POST['phoen_points_use_edit'])?$_POST['phoen_points_use_edit']:'';
			
			if($phoen_points_use_edit<=$points)
			{
				if($phoen_points_use_edit!='')
				{
					update_option('update_opints',$phoen_points_use_edit);
					$_SESSION["phoen_favcolor"] = $phoen_points_use_edit;
				}
				
			}else{
				update_option('update_opints','');
				
			}
			
			$phoen_points_use_edits = get_option('update_opints');
		
			if($phoen_points_use_edits=='')
			{
				$points=$points;
				
			}else{
				
				$points=$phoen_points_use_edits;
			} 
		
			return round($points/$reedem_value,1);
	
		}
		
		//return user reward points
		function phoen_rewpts_user_reward_point(){
			
			$current_user = wp_get_current_user();
    
			global $woocommerce;
			
			$curr=get_woocommerce_currency_symbol();
			
			$products_order = get_posts( array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => 'shop_order',
				'post_status' => array_keys( wc_get_order_statuses() ),
			) );
			$count_order =count($products_order);
			
			$user_detail=get_users();
			
			$total_point_reward=0;
			
			$amount_spent=0;
			
			$order_count=0;
			
			$total_point_reward_empty=0;
			
			$phoe_rewpts_page_settings_value = get_option('phoe_rewpts_page_settings_value');
			
			$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
			
			$reward_point_value_data = phoen_reward_point_value();
	
			extract($reward_point_value_data);
			
			$current_user = wp_get_current_user();
			
				$cur_user_id = $current_user->ID;
		
						$phoen_current_dates_updates_val = get_post_meta($cur_user_id,'phoeni_update_dates_checkeds',true);
						$phoen_current_dates_updates_val = get_user_meta($cur_user_id,'phoen_update_date', true );
						$phoen_update_date_val=strtotime($phoen_current_dates_updates_val);
						
						$phoen_reward_referral_user_points = get_user_meta( $cur_user_id, 'phoen_reward_referral_user_points', true );
						$phoen_add_reff_points=0;
				
						if(!empty($phoen_reward_referral_user_points))
						{
							for($j=0; $j<count($phoen_reward_referral_user_points); $j++)
							{
								$phoen_add_reff_points+=$phoen_reward_referral_user_points[$j];
							}
						}	
						
						for($i=0;$i<count($products_order);$i++) {	
						
							$products_detail=get_post_meta($products_order[$i]->ID); 
							
							$gen_settings=get_post_meta( $products_order[$i]->ID, 'phoe_rewpts_order_status', true );
							
							$phoen_user_id=isset($gen_settings['user_id'])?$gen_settings['user_id']:'';
							
							if($phoen_user_id==$cur_user_id)	
							{
								
								
								$phoen_expiry_date=isset($gen_settings['phoen_expiry_date_add'])?$gen_settings['phoen_expiry_date_add']:'';
								
								if($phoen_expiry_date!='')
								{
									
									$phoen_current_dates = date("d-m-Y") ;
									
									$phoen_current_dates=strtotime($phoen_current_dates);
									
									$phoen_expiry_date=strtotime($phoen_expiry_date);

									if($phoen_current_dates <= $phoen_expiry_date)
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
										
										$bill_price_checked_value=isset($gen_settings['bill_price_checked_value'])?$gen_settings['bill_price_checked_value']:'0';
										$gift_birthday_points_val=0;
										$gift_birthday_points_val = get_post_meta($cur_user_id,'phoeni_rewords_gift_dob_point',true);
										
										$phoeni_first_login_points_myaccount=isset($gen_settings['phoen_first_login_points_myaccount'])?$gen_settings['phoen_first_login_points_myaccount']:'';
										
										$totale_percentage_points=isset($gen_settings['totale_percentage_points'])?$gen_settings['totale_percentage_points']:'';
										$product_percentage=isset($gen_settings['product_percentage'])?$gen_settings['product_percentage']:'';
										$phoen_range_points=isset($gen_settings['phoen_range_points'])?$gen_settings['phoen_range_points']:'';
										
										$point_reward=0;
										$tpoint_reward=0;
										$tpoint_rewards=0;
										$tpoint_reward_new_val=0;
								
										$phoen_current_date=isset($gen_settings['current_date'])?$gen_settings['current_date']:'';
										$phoen_current_date=strtotime($phoen_current_date);
										if($phoen_update_date_val=='')
										{
											
											if($products_order[$i]->post_status=="wc-completed")
											{
											
												if($login_point_first =='')
												{
													
													if($bill_price_checked_value !='' && $bill_price_checked_value!='0')
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
													
													if($bill_price_checked_value !='' && $bill_price_checked_value!='0')
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
												
												if($gift_birthday_points_val!='' && $gift_birthday_points_val!='0')
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
									
											if($phoen_update_date_val<=$phoen_current_date)	
											{
												if($products_order[$i]->post_status=="wc-completed")
												{
												
													if($login_point_first =='')
													{
														
														if($bill_price_checked_value !='' && $bill_price_checked_value!='0')
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
													
													if($gift_birthday_points_val!='' && $gift_birthday_points_val!='0')
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
										
										$bill_price_checked_value=isset($gen_settings['bill_price_checked_value'])?$gen_settings['bill_price_checked_value']:'0';
										$gift_birthday_points_val=0;
										$gift_birthday_points_val = get_post_meta($cur_user_id,'phoeni_rewords_gift_dob_point',true);
										
										$phoeni_first_login_points_myaccount=isset($gen_settings['phoen_first_login_points_myaccount'])?$gen_settings['phoen_first_login_points_myaccount']:'';
										
										$totale_percentage_points=isset($gen_settings['totale_percentage_points'])?$gen_settings['totale_percentage_points']:'';
										
										$product_percentage=isset($gen_settings['product_percentage'])?$gen_settings['product_percentage']:'';
										
										$phoen_range_points=isset($gen_settings['phoen_range_points'])?$gen_settings['phoen_range_points']:'';
										
										$point_reward=0;
										$tpoint_reward=0;
										$tpoint_rewards=0;
										$tpoint_reward_new_val=0;
								
										$phoen_current_date=isset($gen_settings['current_date'])?$gen_settings['current_date']:'';
										$phoen_current_date=strtotime($phoen_current_date);
										if($phoen_update_date_val=='')
										{
											
											if($products_order[$i]->post_status=="wc-completed")
											{
											
												if($login_point_first =='')
												{
													
													if($bill_price_checked_value !='' && $bill_price_checked_value!='0')
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
													
													if($bill_price_checked_value !='' && $bill_price_checked_value!='0')
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
												
												if($gift_birthday_points_val!='' && $gift_birthday_points_val!='0')
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
									
											if($phoen_update_date_val<=$phoen_current_date)	
											{
												if($products_order[$i]->post_status=="wc-completed")
												{
												
													if($login_point_first =='')
													{
														
														if($bill_price_checked_value !='' && $bill_price_checked_value!='0')
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
														
														if($bill_price_checked_value !='' && $bill_price_checked_value!='0')
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
													
													if($gift_birthday_points_val!='' && $gift_birthday_points_val!='0')
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
									
							}	
									
						}	
				
						$total_point_rewardss = get_post_meta( $cur_user_id, 'phoes_customer_points_update_valss',true);
						$total_point_rewardss = get_user_meta($cur_user_id,'phoen_update_customer_points',true );
						
						if($total_point_rewardss=='')
						{
							$total_point_reward=$total_point_reward_empty;
							
						}else{
							
							if($total_point_reward=='0')
							{
								$total_point_reward = get_post_meta( $cur_user_id, 'phoes_customer_points_update_valss',true);
								$total_point_reward = get_user_meta($cur_user_id,'phoen_update_customer_points',true );
							
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
						
				
							if($total_point_rewardss=='')
							{

								$phoen_reward_referral_user_id = get_user_meta( $cur_user_id, 'phoen_reward_referral_user_id', true );
								$gift_birthday_points_valss=0;
								
								$gift_birthday_points_valss = get_post_meta($cur_user_id,'phoeni_rewords_gift_dob_point',true);
								
								if($count_order=='0')
								{
								
									$total_point_reward = $phoen_add_reff_points+$total_point_reward;
									
									if($gift_birthday_points_valss!='' && $gift_birthday_points_valss!='0')
									{
										$total_point_reward = $total_point_reward+$gift_birthday_points_valss;
									}
									
									
									$phoen_first_login_points = get_post_meta($cur_user_id,'phoen_reward_points_for_register_user',true);
									$phoen_reward_points_for_register_user_id = get_post_meta($cur_user_id,'phoen_reward_points_for_register_user_id',true);		
									if($phoen_reward_points_for_register_user_id==$cur_user_id)
									{
										if($phoen_first_login_points!='')
										{
											
											$total_point_reward = $phoen_first_login_points+$total_point_reward ;
											
										}
									
									}
								} 
							
							}
					
					return round($total_point_reward);	
			
		}
		// Start checkout form data save on processed order
		include_once(PHOEN_REWPTSPLUGPATH.'includes/checkout/phoen_reward_checkout_data_process.php');
		// End checkout form data save on processed order
		
		// Start Reward point shortcode
		include_once(PHOEN_REWPTSPLUGPATH.'includes/shortcode/phoen_reward_point_shortcode.php');
		// End Reward point shortcode
		
		// Start Reward total point shortcode
		include_once(PHOEN_REWPTSPLUGPATH.'includes/shortcode/phoen_reward_total_point_shortcode.php');
		// End Reward total point shortcode
		
		// Start Single Product Notification 
		include_once(PHOEN_REWPTSPLUGPATH.'includes/single-product-page/phoen_reward_single_page_notification.php');
		// End Single Product Notification 
		
		// Start cart  Notification 
		include_once(PHOEN_REWPTSPLUGPATH.'includes/cart/phoen_reward_cart_notification.php');
		// End Cart Notification 
		
		register_activation_hook( __FILE__, 'phoe_rewpts_activation_func');
		
		// Start save data on Activation hook
		include_once(PHOEN_REWPTSPLUGPATH.'includes/phoen_reward_activation_hook.php');
		// End save data on Activation hook
		
		// Start cart Coupon fee remove and add
		include_once(PHOEN_REWPTSPLUGPATH.'includes/cart/phoen_reward_cart_coupon.php');
		// End cart Coupon fee remove and add
		
	}else{
		
		add_action('admin_notices', 'phoen_rewpts_admin_notice');

		function phoen_rewpts_admin_notice() {
			
			global $current_user ;
				
				$user_id = $current_user->ID;
				
				/* Check that the user hasn't already clicked to ignore the message */
			
			if ( ! get_user_meta($user_id, 'phoen_rewpts_ignore_notice') ) {
				
				echo '<div class="error"><p>'; 
				
				printf(__('Woocommerce Reward Points could not detect an active Woocommerce plugin. Make sure you have activated it. | <a href="%1$s">Hide Notice</a>'), '?phoen_rewpts_nag_ignore=0');
				
				echo "</p></div>";
			}
		}

		add_action('admin_init', 'phoen_rewpts_nag_ignore');

		function phoen_rewpts_nag_ignore() {
			
			global $current_user;
				
				$user_id = $current_user->ID;
				
				/* If user clicks to ignore the notice, add that to their user meta */
				
				if ( isset($_GET['phoen_rewpts_nag_ignore']) && '0' == $_GET['phoen_rewpts_nag_ignore'] ) {
					
					add_user_meta($user_id, 'phoen_rewpts_ignore_notice', 'true', true);
				}
		}
		
		
	} ?>
