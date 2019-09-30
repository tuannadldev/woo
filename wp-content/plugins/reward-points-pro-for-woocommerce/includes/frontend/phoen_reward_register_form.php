<?php if ( ! defined( 'ABSPATH' ) ) exit;

	$gen_settings = get_option('phoe_rewpts_page_settings_value');
		
	$enable_plugin_dob_date=isset($gen_settings['enable_plugin_dob_date'])?$gen_settings['enable_plugin_dob_date']:'';

	$enable_plugin_reff=isset($gen_settings['enable_plugin_reff_code'])?$gen_settings['enable_plugin_reff_code']:'';

	add_action('init','pho_do_stuff');
			 
	function pho_do_stuff(){

		$current_user = wp_get_current_user();

		$roless =  isset($current_user->roles[0])?$current_user->roles[0]:'';

		update_option('role_wise',$roless);

	}
	 
		$phoen_rol = get_option('role_wise');
		
		$phoen_role_datas = get_option('phoen_customer_role');
	
			
				add_action( 'woocommerce_register_form', 'Phoen_reward_WC_extra_registation_fields');
				
				add_action( 'woocommerce_created_customer', 'phoen_reward_wooc_save_extra_register_fields' ,10,1);
			 
				add_action( 'woocommerce_register_post', 'phoen_reward_wooc_validate_extra_register_fields', 10, 3 );
				
				add_action( 'woocommerce_edit_account_form', 'phoen_reward_edit_account_form' );
				
				add_action( 'woocommerce_save_account_details', 'phoen_reward_save_account_details' );
				
	
	
		function Phoen_reward_WC_extra_registation_fields() {
			
			$gen_settings = get_option('phoe_rewpts_page_settings_value');
		
			$enable_plugin_dob_date=isset($gen_settings['enable_plugin_dob_date'])?$gen_settings['enable_plugin_dob_date']:'';

			$enable_plugin_reff=isset($gen_settings['enable_plugin_reff_code'])?$gen_settings['enable_plugin_reff_code']:'';
			
			if($enable_plugin_dob_date==1)
			{
				?>
		
				<p class="form-row form-row-wide">
					
					<label for="phoen_reward_dob_user"><?php _e( 'Date of Birth', 'phoen-rewpts' ); ?> <span class="required">*</span></label></label>
					
					<input type="text" class="input-text phoen_reward_dob_date" name="phoen_reward_dob_user" id="phoen_reward_dob_user" value="<?php echo isset($_POST['phoen_reward_dob_user']) ? $_POST['phoen_reward_dob_user']:'' ?>" />
				
				</p>
				
				<?php 
			} 
				
			if($enable_plugin_reff==1)
			{
				$digits = 5;
				$phoe_rand_number =  rand(pow(10, $digits-1), pow(10, $digits)-1);
				?>	
				<p class="form-row form-row-wide">
					<label for="phoen_reward_dob_user"><?php _e( 'Referral code', 'phoen-rewpts' ); ?></label></label>
					<input type="hidden" class="input-text" name="phoen_reward_referral_user_hidden" id="phoen_reward_referral_user_hidden" value="<?php echo isset($phoe_rand_number )?$phoe_rand_number:''; ?>" />
					<input type="text" class="input-text" name="phoen_reward_referral_user" id="phoen_reward_referral_user" value="<?php echo isset($_POST['phoen_reward_referral_user']) ? $_POST['phoen_reward_referral_user']:'' ?>" />
				</p>
				
				<?php
			}
			?> <div class="clear"></div> <?php
		}
			
		function phoen_reward_wooc_validate_extra_register_fields( $username, $email, $validation_errors) {
		
			$gen_settings = get_option('phoe_rewpts_page_settings_value');
				
			$enable_plugin_dob_date=isset($gen_settings['enable_plugin_dob_date'])?$gen_settings['enable_plugin_dob_date']:'';

			$enable_plugin_reff=isset($gen_settings['enable_plugin_reff_code'])?$gen_settings['enable_plugin_reff_code']:'';
			
			if($enable_plugin_dob_date=='1' )
			{
			
				if ( isset( $_POST['phoen_reward_dob_user'] ) && empty( $_POST['phoen_reward_dob_user'] ) ) {

					 $validation_errors->add( 'phoen_reward_dob_user', __( '<strong>Error</strong>: Date of Birth is required!', 'phoen-rewpts' ) );

				}
			}

			if($enable_plugin_reff=='1')
			{	
		  
			  $phoen_validation_ref = get_option('phoen_validation_ref_code');
			  
				if($_POST['phoen_reward_referral_user']!='')
				{
					if (!in_array($_POST['phoen_reward_referral_user'], $phoen_validation_ref)) {
					  
						$validation_errors->add( 'phoen_reward_referral_user', __( '<strong>Error</strong>: Please Enter a Valid Referral Code!', 'phoen-rewpts' ) );
					}
				  
				}
			}  
		  
			 return $validation_errors;
		}

		function phoen_reward_wooc_save_extra_register_fields( $customer_id ) {
		
			update_option('nst',$customer_id);
			$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
			$gen_settings = get_option('phoe_rewpts_page_settings_value');
			$enable_plugin_dob_date=isset($gen_settings['enable_plugin_dob_date'])?$gen_settings['enable_plugin_dob_date']:'';
			$enable_plugin_reff=isset($gen_settings['enable_plugin_reff_code'])?$gen_settings['enable_plugin_reff_code']:'';
			$first_login_points_val=isset($phoen_rewpts_set_point_data['first_login_points'])?$phoen_rewpts_set_point_data['first_login_points']:'';
			if($first_login_points_val!='')
			{
				$user_update = get_post_meta($customer_id,'phoen_reward_points_for_register_user');
				
				if(empty($user_update)){
					update_post_meta($customer_id,'phoen_reward_points_for_register_user',$first_login_points_val);
					$phoen_current_dates = date("d-m-Y") ;
					update_post_meta($customer_id,'phoen_reward_points_for_register_userdate',$phoen_current_dates);
					update_post_meta($customer_id,'phoen_reward_points_for_register_user_id',$customer_id);
				}
			}
			
			if($enable_plugin_dob_date=='1' )
			{
			
				if ( isset( $_POST['phoen_reward_dob_user'] ) ) {
							 // Phone input filed which is used in WooCommerce
					update_user_meta( $customer_id, 'phoen_reward_dob_user_data', sanitize_text_field( $_POST['phoen_reward_dob_user'] ) );
				}
			}	
				
			if($enable_plugin_reff=='1')
			{
				if ( isset( $_POST['phoen_reward_referral_user_hidden'] ) ) {
						
					$user_info = get_userdata($customer_id);
					$phoen_user_name = $user_info->user_login;
					
					
					update_user_meta( $customer_id, 'phoen_reward_referral_user_hidden', sanitize_text_field($phoen_user_name."_". $_POST['phoen_reward_referral_user_hidden'] ) );
					$phoen_validation_ref = get_option('phoen_validation_ref_code');
					if(empty($phoen_validation_ref))
					{
						$code = array($phoen_user_name."_". $_POST['phoen_reward_referral_user_hidden']);
						update_option('phoen_validation_ref_code',$code);
					
					}else{
						$phoen_validation_ref = get_option('phoen_validation_ref_code');
						$code = array($phoen_user_name."_". $_POST['phoen_reward_referral_user_hidden']);
						$email_combine_ref=array_merge($code,$phoen_validation_ref);
						update_option('phoen_validation_ref_code',$email_combine_ref);
						
					}
					
				}
				
				if ( isset( $_POST['phoen_reward_referral_user'] ) ) {
					
					
					$user_detail=get_users();
					
					foreach($user_detail as $key=>$phoen_user_details)
					{
						$phoen_user_id = $phoen_user_details->ID;
						$phoen_reward_referral_user_checked = $_POST['phoen_reward_referral_user'];
						$phoen_reward_referral_user_val = get_user_meta( $phoen_user_id, 'phoen_reward_referral_user_hidden', true );
						if($phoen_reward_referral_user_val==$phoen_reward_referral_user_checked)
						{
							$phoen_user_id_ref = $phoen_user_details->ID;
							$phoen_user_id_ref_email = $_POST['email'];
							
						}
						
					}
					
					$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
					$link_referral_points_val=isset($phoen_rewpts_set_point_data['link_referral_points'])?$phoen_rewpts_set_point_data['link_referral_points']:'';
					$phoen_rewards_referral_user = get_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_codes', true );
					
					$phoen_reward_referral_user_array = array($_POST['phoen_reward_referral_user']);
					$phoen_current_dates = date("d-m-Y") ;
					
					if(empty($phoen_rewards_referral_user))
					{
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_codes', $phoen_reward_referral_user_array );
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_points', array($link_referral_points_val) );
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_date', array($phoen_current_dates) );
						
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_id', $phoen_user_id_ref );
					
					}else{
						$phoen_current_datesdd=array($phoen_current_dates);
						$phoen_rewards_referral_user = get_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_codes', true );
						$phoen_rewards_referral_user_points = get_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_points', true );
						$phoen_rewards_referral_user_points_date = get_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_date', true );
						$link_referral_points_val=array($link_referral_points_val);
							
						$email_combine_valc_ref_code=array_merge($phoen_reward_referral_user_array,$phoen_rewards_referral_user);
						
						$email_combine_valc_ref_code_points=array_merge($link_referral_points_val,$phoen_rewards_referral_user_points);
						
						$phoen_rewards_referral_user_points_date_val=array_merge($phoen_current_datesdd,$phoen_rewards_referral_user_points_date);
					
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_codes', $email_combine_valc_ref_code );
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_points',$email_combine_valc_ref_code_points);
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_date', $phoen_rewards_referral_user_points_date_val );
						update_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_id', $phoen_user_id_ref );
					
					}
				
				}
				$phoen_rewards_referral_user = get_user_meta( $phoen_user_id_ref, 'phoen_reward_referral_user_codes', true );
				$phoen_reward_referral_user_val = get_user_meta( $customer_id, 'phoen_reward_referral_user_hidden', true );
				if($phoen_user_id_ref_email!='')
				{
					
					$subject="Referral Code";
					$headers = array('Content-Type: text/html; charset=UTF-8');
					
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
																	<h3>Your Refferal Code is '.$phoen_reward_referral_user_val.'</h3>
																	<p>Thanks for creating an account.</p>
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
			
					wp_mail( $phoen_user_id_ref_email, $subject,$msg,$headers);
				
				}
				if($phoen_rewards_referral_user!='')
				{
					
					$subject="Referral Code";
					$headers = array('Content-Type: text/html; charset=UTF-8');
					
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
																	<h3>Thank you for using this Referral code.</h3>
																	
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
			
					wp_mail( $phoen_user_id_ref_email, $subject,$msg,$headers);
				
				}
			
			}	
			
		}
		
		function phoen_reward_edit_account_form() {
			$gen_settings = get_option('phoe_rewpts_page_settings_value');
		
			$enable_plugin_dob_date=isset($gen_settings['enable_plugin_dob_date'])?$gen_settings['enable_plugin_dob_date']:'';

			$enable_plugin_reff=isset($gen_settings['enable_plugin_reff_code'])?$gen_settings['enable_plugin_reff_code']:'';
			
			$user_id = get_current_user_id();
			
			?> 
			   
			<fieldset>
				<?php
			if($enable_plugin_dob_date=='1' )
			{
				$phoen_edit_reward_dob_data = get_user_meta( $user_id, 'phoen_reward_dob_user_data', true );
				if($phoen_edit_reward_dob_data!='')
				{
					$phoen_disable_dob ='disabled="disabled"';
				}
				
				?>
				
					<p class="form-row form-row-wide">
						<label for="reg_billing_phone"><?php _e( 'Date of Birth', 'phoen-rewpts' ); ?> <span class="required">*</span></label></label>
						<input type="text" class="input-text phoen_reward_dob_date" name="phoen_reward_edit_dob_user" id="phoen_reward_edit_dob_user" value="<?php echo isset($phoen_edit_reward_dob_data)?$phoen_edit_reward_dob_data:''; ?>" <?php echo isset($phoen_disable_dob)?$phoen_disable_dob:'' ; ?>/>
						<span class="description"><?php _e("You want to edit your birthday date please contact to admin.",'phoen-rewpts'); ?></span>
					</p>
				<?php
			}

			if($enable_plugin_reff=='1')
			{	
				
				$phoen_reward_referral_user_val_data = get_user_meta( $user_id, 'phoen_reward_referral_user_hidden', true );
				
				if($phoen_reward_referral_user_val_data!=''){
					
					$phoen_validation_ref = get_option('phoen_validation_ref_code');
				
					if(empty($phoen_validation_ref))
					{
						$code = array($phoen_reward_referral_user_val_data);
						
						if(!in_array($phoen_reward_referral_user_val_data,$phoen_validation_ref)){
							
							update_option('phoen_validation_ref_code',$code);
						}
						
					}else{
						
						$code = array($phoen_reward_referral_user_val_data);
						
						$email_combine_ref=array_merge($code,$phoen_validation_ref);
						
						if(!in_array($phoen_reward_referral_user_val_data,$phoen_validation_ref)){
							
							update_option('phoen_validation_ref_code',$email_combine_ref);
							
						}
						
					}
					
					$phoen_reward_referral_user_val=$phoen_reward_referral_user_val_data;
				
				}else{
					
					$digits = 5;
					
					$phoe_rand_number =  rand(pow(10, $digits-1), pow(10, $digits)-1);
					
					$user_info = get_userdata($user_id);
					
					$phoen_user_name = $user_info->user_login;
					
					update_user_meta( $user_id, 'phoen_reward_referral_user_hidden', sanitize_text_field($phoen_user_name."_". $phoe_rand_number ) );
					
					$phoen_reward_referral_user_val = get_user_meta( $user_id, 'phoen_reward_referral_user_hidden', true );
					
					$phoen_validation_ref = get_option('phoen_validation_ref_code');
				
					if(empty($phoen_validation_ref))
					{
						$code = array($phoen_user_name."_". $phoe_rand_number);
						
						update_option('phoen_validation_ref_code',$code);
					
					}else{
						
						$phoen_validation_ref = get_option('phoen_validation_ref_code');
						
						$code = array($phoen_user_name."_". $phoe_rand_number);
						
						$email_combine_ref=array_merge($code,$phoen_validation_ref);
						
						update_option('phoen_validation_ref_code',$email_combine_ref);
						
					}
					
				}
				
				$phoen_validation_ref = get_option('phoen_validation_ref_code');
				
				$phoen_rewards_referral_user = get_user_meta( $user_id, 'phoen_reward_referral_user_codes', true );
				
				if($phoen_reward_referral_user_val!='')
				{
					$phoen_disable_ref ='disabled="disabled"';
				}
				if($phoen_reward_referral_user_val!='')
				{
					
					?>
				<p class="form-row form-row-wide phoen_mail_ref_code">
				
				
					<label for="reg_billing_phone"><?php _e( 'Referral Code', 'phoen-rewpts' ); ?></label></label>
					<input type="text" class="input-text referral_code" name="phoen_reward_referral_user_code" id="phoen_reward_referral_user_code" value="<?php echo isset($phoen_reward_referral_user_val)?$phoen_reward_referral_user_val:''; ?>" readonly />
					<label for="reg_billing_phone "><?php _e( 'Site url', 'phoen-rewpts' ); ?></label></label>
					<input type="text" class="input-text referral_url" name="phoen_reward_referral_site_url" id="phoen_reward_referral_site_url" value="<?php echo get_site_url()."/my-account/"; ?>" readonly />
					<label for="reg_billing_phone"><?php _e( 'Enter Referral Email Id', 'phoen-rewpts' ); ?></label></label>
					<input type="text" class="input-text referral_email" name="phoen_reward_referral_email_id" id="phoen_reward_referral_email_id" value="<?php echo isset($phoen_reward_referral_email_id)?$phoen_reward_referral_email_id:''; ?>" />
					<a class="phoen_send_reff_code"><?php _e( 'Send Referral code', 'phoen-rewpts' ); ?></a>
					<span class="transfer_sucessfully" style="display:none"><?php _e( 'Referral code Sent Successfully', 'phoen-rewpts' ); ?>..</span>
				</p>
				
				<script>
				jQuery(document).ready(function()
				{
					jQuery(".phoen_send_reff_code").on("click",function()
					{
						var ref_code = jQuery(".referral_code").val();
						var ref_url  = jQuery(".referral_url").val();
						var ref_email_id = jQuery(".referral_email").val();
						
						var phoen_referral_newurl = '<?php echo admin_url('admin-ajax.php') ;?>';
			
							jQuery.post(
								
								phoen_referral_newurl,
								{
									'action'	:  'phoe_referral_code_completed',
									'data'		:	ref_code,
									'ref_site_url':	ref_url,
									'ref_user_email_id'   : ref_email_id
									
								},
								function(response){
									
									if(response=='completed')
									{
									
										jQuery(".transfer_sucessfully").css("display", "block");
									
									}
								}
								
								
							);
						
					});
				});
				
				</script>
				<style>
				.phoen_send_reff_code {
					border: 1px solid #ccc;
					border-radius: 4px;
					color: #555;
					cursor: pointer;
					display: inline-block;
					float: right;
					font-weight: 600;
					margin: 10px 0;
					padding: 8px 15px;
					text-shadow: none;
				}
				
				.phoen_send_reff_code:hover {
					color: #555;
				}
				
				.woocommerce form .phoen_mail_ref_code {
					background-color: #eee;
					border: 1px solid #eee;
					padding: 15px;
				}
				
				.transfer_sucessfully {
					border: 1px solid #008000;
					color: #008000;
					float: left;
					font-size: 12px;
					padding: 5px;
					text-align: center;
					width: 100%;
				}
				</style>
				<?php }

			}
			?>
				<div class="clear"></div>
			</fieldset>
			<?php
		}

		if($enable_plugin_dob_date=='1' )
		{
			include_once(PHOEN_REWPTSPLUGPATH.'includes/phoen_reward_dob_cron.php');
		}
		
	if($enable_plugin_reff=='1')
	{
		
		add_action( 'wp_ajax_phoe_referral_code_completed', 'phoe_referral_code_completed_fun' );

		add_action( 'wp_ajax_nopriv_phoe_referral_code_completed', 'phoe_referral_code_completed_fun' );

		function phoe_referral_code_completed_fun()
		{
			
			$phoen_code_id= sanitize_text_field($_POST['data']);
			
			$phoen_ref_site_url= sanitize_text_field($_POST['ref_site_url']);
			
			$phoen_ref_user_email_id= sanitize_text_field($_POST['ref_user_email_id']);
			
			$subject="Referral Code";
			
			$headers = array('Content-Type: text/html; charset=UTF-8');
				
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
																	<h3>Referral Code</h3>
																	<h3>'.$phoen_code_id.'</h3>
																	<p>'.$phoen_ref_site_url.'</p>
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
				
				
				if($phoen_ref_user_email_id!='')
				{
					
					wp_mail( $phoen_ref_user_email_id, $subject,$msg,$headers);
					
					echo $complit="completed";
				}
				
				die();
		}
	}	
		
	if($enable_plugin_dob_date=='1' )
	{

		function phoen_reward_save_account_details( $user_id ) {
		
			if ( isset( $_POST['phoen_reward_edit_dob_user'] ) ) {
				
				update_user_meta($user_id, 'phoen_reward_dob_user_data', sanitize_text_field($_POST['phoen_reward_edit_dob_user']));
			}

		}
	}	