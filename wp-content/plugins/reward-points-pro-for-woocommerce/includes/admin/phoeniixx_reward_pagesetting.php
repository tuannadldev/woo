<?php if ( ! defined( 'ABSPATH' ) ) exit;
	
if ( ! empty( $_POST ) && check_admin_referer( 'phoen_rewpts_form_action', 'phoen_rewpts_form_action_form_nonce_field' ) ) {

	if(isset($_POST['rewpts_submit'] )){
	
		$enable_plugin = ( isset( $_POST['enable_plugin'] ) ? sanitize_text_field( $_POST['enable_plugin'] ) : "" );
		
		$enable_plugin_myaccount = ( isset( $_POST['enable_plugin_myaccount'] ) ? sanitize_text_field( $_POST['enable_plugin_myaccount'] ) : "" );
	
		$enable_plugin_dob_date = ( isset( $_POST['enable_plugin_dob_date'] ) ? sanitize_text_field( $_POST['enable_plugin_dob_date'] ) : "" );
		
		$enable_plugin_reff_code = ( isset( $_POST['enable_plugin_reff_code'] ) ? sanitize_text_field( $_POST['enable_plugin_reff_code'] ) : "" );
		
		$phoen_points_expiry_month = ( isset( $_POST['phoen_points_expiry_month'] ) ? sanitize_text_field( $_POST['phoen_points_expiry_month'] ) : "" );
		
		$phoen_points_assignment_date = ( isset( $_POST['phoen_points_assignment_date'] ) ? sanitize_text_field( $_POST['phoen_points_assignment_date'] ) : "" );
		
		$limit_use_points = ( isset( $_POST['limit_use_points'] ) ? sanitize_text_field( $_POST['limit_use_points'] ) : "" );
	
		$phoe_rewpts_value = array(
		
			'enable_plugin'=>$enable_plugin,
			
			'enable_plugin_myaccount'=>$enable_plugin_myaccount,
			
			'enable_plugin_dob_date'=>$enable_plugin_dob_date,
			
			'enable_plugin_reff_code'=>$enable_plugin_reff_code,
			
			'phoen_points_expiry_month'=>$phoen_points_expiry_month,
			
			'phoen_points_assignment_date'=>$phoen_points_assignment_date,
			
			'limit_use_points'=>$limit_use_points
		);
			
		update_option('phoe_rewpts_page_settings_value',$phoe_rewpts_value);
		
	}
	
}

	$gen_settings = get_option('phoe_rewpts_page_settings_value');
	
	$enable_plugin=isset($gen_settings['enable_plugin'])?$gen_settings['enable_plugin']:'';
	
	$enable_plugin_myaccount_val=isset($gen_settings['enable_plugin_myaccount'])?$gen_settings['enable_plugin_myaccount']:'';
	
	$enable_plugin_dob_date=isset($gen_settings['enable_plugin_dob_date'])?$gen_settings['enable_plugin_dob_date']:'';
	
	$enable_plugin_reff=isset($gen_settings['enable_plugin_reff_code'])?$gen_settings['enable_plugin_reff_code']:'';
	
	$phoen_points_expiry_month_val=isset($gen_settings['phoen_points_expiry_month'])?$gen_settings['phoen_points_expiry_month']:'';
	
	$phoen_points_assignment_date_val=isset($gen_settings['phoen_points_assignment_date'])?$gen_settings['phoen_points_assignment_date']:'';
	
	$limit_use_points_val=isset($gen_settings['limit_use_points'])?$gen_settings['limit_use_points']:'';
	
 ?>

	<div id="phoeniixx_phoe_book_wrap_profile-page"  class=" phoeniixx_phoe_book_wrap_profile_div">
	
		<form method="post" id="phoeniixx_phoe_book_wrap_profile_form" action="" >
		
			<?php wp_nonce_field( 'phoen_rewpts_form_action', 'phoen_rewpts_form_action_form_nonce_field' ); ?>
			
			<table class="form-table">
				
				<tbody>	
		
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
						<th>
						
							<label><?php _e('Enable Reward','phoen-rewpts'); ?> </label>
							
						</th>
						
						<td>
						
							<input type="checkbox"  name="enable_plugin" id="enable_plugin" value="1" <?php echo(isset($gen_settings['enable_plugin']) && $gen_settings['enable_plugin'] == '1')?'checked':'';?>>
							
						</td>
						<td></td>
						
					</tr>
					
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
						<th>
						
							<label><?php _e('Enable Customer Points on My Account page','phoen-rewpts'); ?> </label>
							
						</th>
						
						<td>
						
							<input type="checkbox"  name="enable_plugin_myaccount" id="enable_plugin_myaccount" value="1" <?php echo(isset($gen_settings['enable_plugin_myaccount']) && $gen_settings['enable_plugin_myaccount'] == '1')?'checked':'';?>>
							
						</td>
						<td></td>
						
					</tr>
					
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
						<th>
						
							<label><?php _e('Enable Birthday Date','phoen-rewpts'); ?> </label>
							
						</th>
						
						<td>
						
							<input type="checkbox"  name="enable_plugin_dob_date" id="enable_plugin_dob_date" value="1" <?php echo(isset($gen_settings['enable_plugin_dob_date']) && $gen_settings['enable_plugin_dob_date'] == '1')?'checked':'';?>>
							
						</td>
						<td></td>
						
					</tr>
					
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
						<th>
						
							<label><?php _e('Enable Referral','phoen-rewpts'); ?> </label>
							
						</th>
						
						<td>
						
							<input type="checkbox"  name="enable_plugin_reff_code" id="enable_plugin_reff_code" value="1" <?php echo(isset($gen_settings['enable_plugin_reff_code']) && $gen_settings['enable_plugin_reff_code'] == '1')?'checked':'';?>>
							
						</td>
						<td></td>
						
					</tr>
					
					
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
						<th>
							<label><?php _e('Points Expiry Date','phoen-rewpts'); ?> </label>
						</th>
						
						<td>
						
							<input type="text"  name="phoen_points_expiry_month" class="phoen_points_expiry_month" value="<?php echo $phoen_points_expiry_month_val; ?>" > 
							
						</td>
						
					</tr>
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
						<th>
							<label><?php _e('Earning Reward Points Date','phoen-rewpts'); ?></label>
						</th>
						
						<td>
						
							<input type="text" step="any" name="phoen_points_assignment_date" class="phoen_points_assignment_dates" value="<?php echo $phoen_points_assignment_date_val; ?>" > 
							
						</td>
						
					</tr>
					
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
						<th>
							<label><?php _e('Set the Limit to Redeem Reward Points Per Order','phoen-rewpts'); ?> </label>
						</th>
						
						<td>
						
							<input type="number" min="0" step="any" name="limit_use_points" class="limit_use_points" value="<?php echo $limit_use_points_val; ?>" >
						
						</td>
						
					</tr>
					
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
					
						<td colspan="2">
						
							<input type="submit" value="<?php _e('Save','phoen-rewpts'); ?>" name="rewpts_submit" id="submit" class="button button-primary">
						
						</td>
						
					</tr>
		
				</tbody>
				
			</table>
			
		</form>
		
	</div>