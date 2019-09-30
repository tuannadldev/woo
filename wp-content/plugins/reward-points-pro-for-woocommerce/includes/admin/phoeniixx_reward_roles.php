<?php if ( ! defined( 'ABSPATH' ) ) exit; 

	if ( ! empty( $_POST ) && check_admin_referer( 'phoen_rewpts_btncreate_action', 'phoen_rewpts_btncreate_action_field' ) ) {

		if(isset( $_POST['phoen_reword_role_submit'] )){
				
			if(isset($_POST['phoen_select_user'])){
				
				$phoen_select_user = array_map( 'sanitize_text_field', wp_unslash( $_POST['phoen_select_user']) );
			
				update_option('phoen_reword_roles',$phoen_select_user);
			}else{
				update_option('phoen_reword_roles',array('all'));
			}
			
			if(isset($_POST['reward_money']) && !empty($_POST['reward_money'])){
				
				$reward_money = array_map( 'absint', wp_unslash( $_POST['reward_money']) );
			
				update_option('phoen_reword_roles_reward_money',$reward_money);
				
			}
			if(isset($_POST['reward_point']) && !empty($_POST['reward_point'])){
				
				$reward_point = array_map( 'absint', wp_unslash( $_POST['reward_point']) );
			
				update_option('phoen_reword_roles_reward_point',$reward_point);
			}
			if(isset($_POST['reedem_money']) && !empty($_POST['reedem_money'])){
				
				$reedem_money = array_map( 'absint', wp_unslash( $_POST['reedem_money']) );
			
				update_option('phoen_reword_roles_reedem_money',$reedem_money);
				
			}
			if(isset($_POST['reedem_point']) && !empty($_POST['reedem_point'])){
				
				$reedem_point = array_map( 'absint', wp_unslash( $_POST['reedem_point']) );
			
				update_option('phoen_reword_roles_reedem_point',$reedem_point);
			}
			
			$enable_role_based = ( isset( $_POST['enable_role_based'] ) ? sanitize_text_field( $_POST['enable_role_based'] ) : "" );
			
			update_option('enable_role_based',$enable_role_based);
		}
	}
	 
	global $wp_roles; 
	
	$phoen_role_datas = get_option('phoen_reword_roles',true);
	
	$phoen_role_based_datas= get_option('enable_role_based',true);
	
	$phoen_reword_roles_reward_money = get_option('phoen_reword_roles_reward_money',true);
	
	$phoen_reword_roles_reward_point = get_option('phoen_reword_roles_reward_point',true);
	
	$phoen_reword_roles_reedem_money = get_option('phoen_reword_roles_reedem_money',true);
	
	$phoen_reword_roles_reedem_point = get_option('phoen_reword_roles_reedem_point',true);
	
?>
<div class="cat_mode">
		
	<form method="post" name="phoen_woo_btncreate">
		
		<?php 
			
			wp_nonce_field( 'phoen_rewpts_btncreate_action', 'phoen_rewpts_btncreate_action_field' ); 

			$phoen_customer_roles = $wp_roles->get_names();	
		
		?>
				
		<table class="form-table">
		
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Enable Role Based Reward','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
				
					<input type="checkbox"  name="enable_role_based" id="enable_role_based " value="1" <?php echo(isset($phoen_role_based_datas) && $phoen_role_based_datas == '1')?'checked':'';?>>
					
				</td>
				
			</tr>
		
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
					
						<label><?php _e('Select User Role To Assign Points','phoen-rewpts'); ?> </label>
						
					</th>
					
					<td>
					<?php 
						
							?>
						<select class="phoen_reword_select_to" multiple="multiple" name="phoen_select_user[]" style="width:300px;">
							
							<option value="all"  <?php  if( is_array($phoen_role_datas)){  if(in_array('all', $phoen_role_datas) || empty($phoen_role_datas)){ echo 'selected';} } ?>><? echo _e('All','phoen-rewpts');?></option>
							<?php 
							
							foreach($phoen_customer_roles as $key=>$phoen_role_data){ ?>
								
								<option value="<?php echo $key ; ?>" <?php if(!empty($phoen_role_datas) && is_array($phoen_role_datas)) { if(in_array($key, $phoen_role_datas)){echo 'selected';} }?>><?php echo $phoen_role_data ; ?></option>
								
							<?php } ?>
						
						</select>
						
					</td>
					
				</tr>	
				<?php  $curr=get_woocommerce_currency_symbol(); ?>
				<?php 
				
				foreach($phoen_customer_roles as $key=>$phoen_role_data){ ?>
					
					<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
					
						<label><?php echo esc_html('Get Points For '.$phoen_role_data); ?> </label>
						
					</th>
					
					<td>
					
						<input type="number" min="0" step="any" class="reward_money"  name="reward_money[<?php echo $key;?>]" value="<?php echo !empty($phoen_reword_roles_reward_money[$key])?$phoen_reword_roles_reward_money[$key]:0; ?>"><?php echo  $curr; ?> =
						
						<input type="number" min="0" step="any" class="reward_point" name="reward_point[<?php echo $key;?>]" value="<?php echo !empty($phoen_reword_roles_reward_point[$key])?$phoen_reword_roles_reward_point[$key]:0;?>" ><?php _e('Points','phoen-rewpts'); ?> 	
						
					</td>
					
				</tr>
				<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
					<th>
					
					<label><?php echo esc_html('Redemption Value For '.$phoen_role_data); ?> </label>
						
					</th>
					<td><input type="number" min="0" step="any" class="reedem_point" name="reedem_point[<?php echo $key;?>]" value="<?php echo !empty($phoen_reword_roles_reedem_point[$key])?$phoen_reword_roles_reedem_point[$key]:0; ?>" ><?php _e('Points =','phoen-rewpts'); ?>
					<input type="number" min="0" step="any" class="reedem_money" name="reedem_money[<?php echo $key;?>]" value="<?php echo !empty($phoen_reword_roles_reedem_money[$key])?$phoen_reword_roles_reedem_money[$key]:0; ?>" >
										
					<?php echo $curr; ?>
					
					
						
					</td>
					
				</tr>
				
			<?php	}
				?>
				
				
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
			
				<td colspan="2">
				
					<input type="submit" value="<?php _e('Save','phoen-rewpts'); ?>" name="phoen_reword_role_submit" id="submit" class="button button-primary">
				
				</td>
				
			</tr>	
					
		</table>
	
	</form>
	
</div>
	