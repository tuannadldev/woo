<?php if ( ! defined( 'ABSPATH' ) ) exit;

$phoen_reward_page_setting_data = get_option('phoe_rewpts_page_settings_value');

$enable_dob_user_date=isset($phoen_reward_page_setting_data['enable_plugin_dob_date'])?$phoen_reward_page_setting_data['enable_plugin_dob_date']:'';
//ADD CUSTOM FIELDS IN USER PROFILE 
if($enable_dob_user_date=='1' )
{
	add_action( 'show_user_profile', 'phoen_reward_extra_user_profile_fields' );

	add_action( 'edit_user_profile', 'phoen_reward_extra_user_profile_fields' );
	
	add_action( 'personal_options_update', 'phoen_reward_save_extra_user_profile_fields' );

	add_action( 'edit_user_profile_update', 'phoen_reward_save_extra_user_profile_fields' );
}

function phoen_reward_extra_user_profile_fields( $user ) { ?>
 
	<table class="form-table">
		<tr>
		  <th><label for="phone"><?php _e( 'Date of Birth', 'phoen-rewpts' ); ?></label></th>
			<td>
				<input type="text" name="phoen_reward_dob_user" id="phoen_reward_dob_user" class="regular-text phoen_reward_dob_date" value="<?php echo  $phoen_get_dob = get_user_meta($user->ID, 'phoen_reward_dob_user_data', true) ; ?>" /><br />
				<span class="description"><?php _e('Please enter your Date of Birth','phoen-rewpts'); ?></span>
			</td>
		</tr>
	</table>
<?php
}

function phoen_reward_save_extra_user_profile_fields( $user_id ) {
  $saved = false;
  if ( current_user_can( 'edit_user', $user_id ) ) {
	update_user_meta( $user_id, 'phoen_reward_dob_user_data', $_POST['phoen_reward_dob_user'] );
	$saved = true;
  }
  return true;
} 
		