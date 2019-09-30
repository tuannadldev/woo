<?php if ( ! defined( 'ABSPATH' ) ) exit;
	
if ( ! empty( $_POST ) && check_admin_referer( 'phoen_rewpts_set_point_form_action', 'phoen_rewpts_set_point_form_action_form_nonce_field' ) ) {

	if(isset($_POST['phoen_rewpts_set_point_submit'] )){
	
		$phoen_rewpts_point_type    = (isset($_POST['phoen_rewpts_point_type']))?sanitize_text_field( $_POST['phoen_rewpts_point_type'] ):'fixed_price';
		$phoen_percentage_wide_rewar_points_global    = (isset($_POST['phoen_percentage_wide_rewar_points_global']))?sanitize_text_field( $_POST['phoen_percentage_wide_rewar_points_global'] ):'0';
		$reward_money    = (isset($_POST['reward_money']))?sanitize_text_field( $_POST['reward_money'] ):'0';
		$reward_point    = (isset($_POST['reward_point']))?sanitize_text_field( $_POST['reward_point'] ):'0';
		$reedem_point    = (isset($_POST['reedem_point']))?sanitize_text_field( $_POST['reedem_point'] ):'0';
		$reedem_money    = (isset($_POST['reedem_money']))?sanitize_text_field( $_POST['reedem_money'] ):'0';
		$first_login_points    = (isset($_POST['first_login_points']))?sanitize_text_field( $_POST['first_login_points'] ):'0';
		$first_order_points    = (isset($_POST['first_order_points']))?sanitize_text_field( $_POST['first_order_points'] ):'0';
		$first_review_points    = (isset($_POST['first_review_points']))?sanitize_text_field( $_POST['first_review_points'] ):'0';
		$use_payment_gateway    = (isset($_POST['use_payment_gateway']))?sanitize_text_field( $_POST['use_payment_gateway'] ):'0';
		$gift_birthday_points    = (isset($_POST['gift_birthday_points']))?sanitize_text_field( $_POST['gift_birthday_points'] ):'0';
		$link_referral_points    = (isset($_POST['link_referral_points']))?sanitize_text_field( $_POST['link_referral_points'] ):'0';
		
		$phoe_rewpts_value = array(
			
			'point_type'=>$phoen_rewpts_point_type,
			
			'phoen_percentage_wide_rewar_points_global'=>$phoen_percentage_wide_rewar_points_global,
			
			'reward_money'=>$reward_money,
			
			'reward_point'=>$reward_point,
			
			'reedem_point'=>$reedem_point,
			
			'reedem_money'=>$reedem_money,
			
			'first_login_points'=>$first_login_points,
			
			'first_order_points'=>$first_order_points,
			
			'first_review_points'=>$first_review_points,
			
			'use_payment_gateway'=>$use_payment_gateway,
			
			'gift_birthday_points'=>$gift_birthday_points,
			
			'link_referral_points'=>$link_referral_points,
			
		);
		
		update_option('phoe_set_point_value',$phoe_rewpts_value);
		
		
		$phoen_range_from = isset($_POST['phoen_range_from'])?$_POST['phoen_range_from']:'';
		$phoen_range_to = isset($_POST['phoen_range_to'])?$_POST['phoen_range_to']:'';
		$phoen_range_points = isset($_POST['phoen_range_points'])?$_POST['phoen_range_points']:'';
		
		
		$phoen_range_from_update = isset($_POST['phoen_range_from_update'])?$_POST['phoen_range_from_update']:'';
		$phoen_range_to_update = isset($_POST['phoen_range_to_update'])?$_POST['phoen_range_to_update']:'';
		$phoen_range_points_update = isset($_POST['phoen_range_points_update'])?$_POST['phoen_range_points_update']:'';
		
	
		
		if(!empty($phoen_range_from_update))
		{
			$phoen_rew_points_from = array_merge($phoen_range_from,$phoen_range_from_update);
			$phoen_rew_point_to = array_merge($phoen_range_to,$phoen_range_to_update);
			$phoen_rew_point_val = array_merge($phoen_range_points,$phoen_range_points_update);
	
		}else{
			
			$phoen_rew_points_from = $phoen_range_from;
			$phoen_rew_point_to = $phoen_range_to;
			$phoen_rew_point_val = $phoen_range_points;
		}
		
		
		
		if(!empty($phoen_rew_point_val))
		{
			
			$phoen_points_range_array=array();
			
			foreach($phoen_rew_points_from as $keys=>$phoen_rewar_range_val)
			{
				if($phoen_rewar_range_val!='')
				{
					
					$phoen_points_range_array[$keys]=array(
			
						'form_range'=>$phoen_rewar_range_val,
						'to_range'=>$phoen_rew_point_to[$keys],
						'points_range'=>$phoen_rew_point_val[$keys]
			
					);
					
				}
			
			}
	
			update_option('phoen_points_range_array',$phoen_points_range_array);
			
		}
		
		
		
		
		
	}
	
}
	$phoen_range_points_data = get_option('phoen_points_range_array');
	$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
	$point_type = (isset($phoen_rewpts_set_point_data['point_type']))?$phoen_rewpts_set_point_data['point_type'] :'fixed_price';
	$phoen_percentage_wide_rewar_points_global = (isset($phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global']))?sanitize_text_field( $phoen_rewpts_set_point_data['phoen_percentage_wide_rewar_points_global'] ):'0';
	$reward_money = (isset($phoen_rewpts_set_point_data['reward_money']))?sanitize_text_field( $phoen_rewpts_set_point_data['reward_money'] ):'0';
	$reward_point = (isset($phoen_rewpts_set_point_data['reward_point']))?sanitize_text_field( $phoen_rewpts_set_point_data['reward_point'] ):'0';
	$reedem_point = (isset($phoen_rewpts_set_point_data['reedem_point']))?sanitize_text_field( $phoen_rewpts_set_point_data['reedem_point'] ):'0';
	$reedem_money = (isset($phoen_rewpts_set_point_data['reedem_money']))?sanitize_text_field( $phoen_rewpts_set_point_data['reedem_money'] ):'0';
	$first_login_points = (isset($phoen_rewpts_set_point_data['first_login_points']))?sanitize_text_field( $phoen_rewpts_set_point_data['first_login_points'] ):'0';
	$first_order_points = (isset($phoen_rewpts_set_point_data['first_order_points']))?sanitize_text_field( $phoen_rewpts_set_point_data['first_order_points'] ):'0';
	$first_review_points = (isset($phoen_rewpts_set_point_data['first_review_points']))?sanitize_text_field( $phoen_rewpts_set_point_data['first_review_points'] ):'0';
	$use_payment_gateway = (isset($phoen_rewpts_set_point_data['use_payment_gateway']))?sanitize_text_field( $phoen_rewpts_set_point_data['use_payment_gateway'] ):'0';
	$gift_birthday_points = (isset($phoen_rewpts_set_point_data['gift_birthday_points']))?sanitize_text_field( $phoen_rewpts_set_point_data['gift_birthday_points'] ):'0';
	$link_referral_points = (isset($phoen_rewpts_set_point_data['link_referral_points']))?sanitize_text_field( $phoen_rewpts_set_point_data['link_referral_points'] ):'0';

 ?>

<div id="phoeniixx_phoe_book_wrap_profile-page"  class=" phoeniixx_phoe_book_wrap_profile_div">

	<form method="post" id="phoeniixx_phoe_book_wrap_profile_form" action="" >
	
		<?php wp_nonce_field( 'phoen_rewpts_set_point_form_action', 'phoen_rewpts_set_point_form_action_form_nonce_field' ); ?>
		
		<table class="form-table">
			
			<tbody>	
				
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
					
						<label><?php _e('Select Points Type','phoen-rewpts'); ?> </label>
						
					</th>
					
					<td>
					
						<select name="phoen_rewpts_point_type" id="phoen_rewpts_point_type">
							
							<option  value="fixed_price" <?php selected( $point_type, 'fixed_price' ); ?>><?php esc_html_e('Fixed Price','phoen-rewpts');?></option>
							
							<option value="percentage_price" <?php selected( $point_type, 'percentage_price' ); ?>><?php esc_html_e('Percentage Price','phoen-rewpts');?></option>
						
						</select >
					
					</td>
					
				</tr>
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap" id="phoen_rewpts_point_type_div" style="<?php echo ( $point_type!= 'percentage_price' )?'display:none;':''; ?>">
			
					<th>
						<label><?php _e( 'Set Percentage(%) For Product Price', 'phoen-rewpts' ); ?></label>
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" name="phoen_percentage_wide_rewar_points_global" class="phoen_percentage_wide_rewar_points_global" value="<?php echo $phoen_percentage_wide_rewar_points_global; ?>" >
					
					</td>
					
				</tr>
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap" id="phoen_rewpts_point_type_div2" style="<?php echo ( $point_type== 'percentage_price' )?'display:none;':''; ?>">
			
					<th>
					<?php  $curr=get_woocommerce_currency_symbol(); ?>
						
						
						<label><?php _e('Get Points For','phoen-rewpts'); ?> </label>
						
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" class="reward_money"  name="reward_money" value="<?php echo $reward_money; ?>"><?php echo  $curr; ?> =
						
						<input type="number" min="0" step="any" class="reward_point" name="reward_point" value="<?php echo $reward_point; ?>" ><?php _e('Points','phoen-rewpts'); ?> 	
						
					</td>
					
				</tr>
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap" >
			
					<th>
					<?php  $curr=get_woocommerce_currency_symbol(); ?>
						
					<label><?php _e('Redemption Value For','phoen-rewpts'); ?> </label>
						
					</th>
					<td><input type="number" min="0" step="any" class="reedem_point" name="reedem_point" value="<?php echo $reedem_point; ?>" ><?php _e('Points =','phoen-rewpts'); ?>
						
					<input type="number" min="0" step="any" name="reedem_money" class="reedem_money" value="<?php echo $reedem_money; ?>" >
					
					<?php echo $curr; ?>
					
					
						
					</td>
					
				</tr>
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
						<label><?php _e('First Account SignUp Points','phoen-rewpts'); ?> </label>
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" name="first_login_points" class="first_login_points" value="<?php echo $first_login_points; ?>" >
					
					</td>
					
				</tr>
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
						<label><?php _e('First Order Points','phoen-rewpts'); ?></label>
					</th>
					
					<td>
					
						<input type="number"  min="0" step="any" name="first_order_points" class="first_order_points" value="<?php echo $first_order_points; ?>" >
					
					</td>
					
				</tr>
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
						<label><?php _e('First Review Points','phoen-rewpts'); ?></label>
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" name="first_review_points" class="first_review_points" value="<?php echo $first_review_points; ?>" >
					
					</td>
					
				</tr>
				
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
						<label><?php _e('Use Payment Gateway Points (Only For Pay Pal ) ','phoen-rewpts'); ?></label>
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" name="use_payment_gateway" class="use_payment_gateway" value="<?php echo $use_payment_gateway; ?>" > 
						
					</td>
					
				</tr>
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
						<label><?php _e('Gift Birthday Points','phoen-rewpts'); ?></label>
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" name="gift_birthday_points" class="gift_birthday_points" value="<?php echo $gift_birthday_points; ?>" >
					
					</td>
					
				</tr>
				
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
						<label><?php _e('Referral Points','phoen-rewpts'); ?></label>
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" name="link_referral_points" class="link_referral_points" value="<?php echo $link_referral_points; ?>" >
					
					</td>
					
				</tr>
			
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
					<th>
						<label><?php _e('Bonus Points (Range)','phoen-rewpts'); ?></label>
					</th>
						<td colspan="2">
						<?php if(!empty($phoen_range_points_data))
								{
									foreach($phoen_range_points_data as $keys=>$phoen_range_val)
									{
										
										?>
										<div class="phoeniixx_rewd_min_max_div">
										
											<label><?php _e('From','phoen-rewpts'); ?> </label>
											<input type="number" min="0" name="phoen_range_from_update[]" value="<?php echo isset($phoen_range_val['form_range'])?$phoen_range_val['form_range']:''; ?>">
											
											<label><?php _e('To','phoen-rewpts'); ?> </label>
											<input type="number" min="0" name="phoen_range_to_update[]" value="<?php echo isset($phoen_range_val['to_range'])?$phoen_range_val['to_range']:''; ?>">
											
											<label><?php _e('Points','phoen-rewpts'); ?> </label>
											<input type="number" min="0" name="phoen_range_points_update[]" value="<?php echo isset($phoen_range_val['points_range'])?$phoen_range_val['points_range']:''; ?>">
											
											<button name="remove_b" class="phoe_remove_range_disc_div button">x</button>
											
										</div>	
										
										<?php
										
									}
								} ?>
							<div class="phoeniixx_red_points_div" style="display:none;">
								
									<div class="phoeniixx_rewd_min_max_div">
									
										<label><?php _e('From','phoen-rewpts'); ?> </label>
										<input type="number" min="0" name="phoen_range_from[]" value="">
										
										<label><?php _e('To','phoen-rewpts'); ?> </label>
										<input type="number" min="0" name="phoen_range_to[]" value="">
										
										<label><?php _e('Points','phoen-rewpts'); ?> </label>
										<input type="number" min="0" name="phoen_range_points[]" value="">
										
										<button name="remove_b" class="phoe_remove_range_disc_div button">x</button>
										
									</div>	
									
							   
							</div>
							<!--Blank div for include html -->
							<div class="phoeniixx_range_html_content_div">

							</div>
							<br />
							<input type="button" value="Add Range" class="phoe_range_add_disc_more button">
						</td>	
						
					</tr>
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
				<td colspan="2">
				
					<input type="submit" value="<?php _e('Save','phoen-rewpts'); ?>" name="phoen_rewpts_set_point_submit" id="submit" class="button button-primary">
				
				</td>
				
			</tr>	
				
			</tbody>
			
		</table>
		
	</form>
	
</div>
<script>
jQuery(document).ready(function (){
	var phoen_music_val = jQuery('.phoeniixx_red_points_div').html();

	jQuery('.phoe_range_add_disc_more').click(function(){
   
		jQuery('.phoeniixx_range_html_content_div').append(phoen_music_val);

	});
	jQuery(document).on('click','.phoe_remove_range_disc_div',function(){

		jQuery(this).parent('div').remove();

	}); 
	
})


</script>