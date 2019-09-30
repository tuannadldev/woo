<?php if ( ! defined( 'ABSPATH' ) ) exit; 

	if ( ! empty( $_POST ) && check_admin_referer( 'phoen_rewpts_btncreate_action', 'phoen_rewpts_btncreate_action_field' ) ) {

		if(isset( $_POST['phoen_rewpts_notification_submit'] )){
		
			$enable_plugin_product_page    = (isset($_POST['enable_plugin_product_page']))?sanitize_text_field( $_POST['enable_plugin_product_page'] ):'0';
			
			$phoen_rewpts_notification_product_page    = isset($_POST['phoen_rewpts_notification_product_page'])?$_POST['phoen_rewpts_notification_product_page'] :'You Will get {points} Points On Completing This Order';
			
			$enable_plugin_cart_page    = (isset($_POST['enable_plugin_cart_page']))?sanitize_text_field( $_POST['enable_plugin_cart_page'] ):'0';
			
			$phoen_rewpts_notification_cart_page    = isset($_POST['phoen_rewpts_notification_cart_page'])?$_POST['phoen_rewpts_notification_cart_page'] :'You Will get {points} Points On Completing This Order';
			
			$enable_plugin_checkout_page    = (isset($_POST['enable_plugin_checkout_page']))?sanitize_text_field( $_POST['enable_plugin_checkout_page'] ):'0';
			
			$phoen_rewpts_notification_checkout_page    = isset($_POST['phoen_rewpts_notification_checkout_page'])?$_POST['phoen_rewpts_notification_checkout_page'] :'You Will get {points} Points On Completing This Order';
			
			$apply_box_enable_on_cart    = (isset($_POST['apply_box_enable_on_cart']))?sanitize_text_field( $_POST['apply_box_enable_on_cart'] ):'0';
	
			$phoen_apply_box_notification_cart_page    = isset($_POST['phoen_apply_box_notification_cart_page'])?$_POST['phoen_apply_box_notification_cart_page'] :'You can apply {points} Points to get {price} Discount.';

			$apply_box_enable_on_checkout    = (isset($_POST['apply_box_enable_on_checkout']))?sanitize_text_field( $_POST['apply_box_enable_on_checkout'] ):'0';
			
			$phoen_apply_box_notification_checkout_page    = isset($_POST['phoen_apply_box_notification_checkout_page'])?$_POST['phoen_apply_box_notification_checkout_page'] :'You can apply {points} Points to get {price} Discount.';
			
			$phoen_apply_box_notification_bonus_page    = isset($_POST['phoen_apply_box_notification_bonus_page'])?$_POST['phoen_apply_box_notification_bonus_page'] :'You Will get {points} Bonus Points On Completing This Order.';
			
			
			
			$phoen_points_expiry_before    = (isset($_POST['phoen_points_expiry_before']))?sanitize_text_field( $_POST['phoen_points_expiry_before'] ):'0';
			
			$phoen_points_notification_before    = (isset($_POST['phoen_points_notification_before']))?sanitize_text_field( $_POST['phoen_points_notification_before'] ):'0';
			
			$btn_settings=array(
				
				'enable_plugin_product_page'=>$enable_plugin_product_page ,
				'phoen_rewpts_notification_product_page'=>$phoen_rewpts_notification_product_page ,
				'enable_plugin_cart_page'=>$enable_plugin_cart_page ,
				'phoen_rewpts_notification_cart_page'=>$phoen_rewpts_notification_cart_page ,
				'enable_plugin_checkout_page'=>$enable_plugin_checkout_page ,
				'phoen_rewpts_notification_checkout_page'=>$phoen_rewpts_notification_checkout_page ,
				'apply_box_enable_on_cart'=>$apply_box_enable_on_cart ,
				'phoen_apply_box_notification_cart_page'=>$phoen_apply_box_notification_cart_page ,
				'apply_box_enable_on_checkout'=>$apply_box_enable_on_checkout ,
				'phoen_apply_box_notification_checkout_page'=>$phoen_apply_box_notification_checkout_page ,
				'phoen_apply_box_notification_bonus_page'=>$phoen_apply_box_notification_bonus_page,
				'phoen_points_expiry_before'=>$phoen_points_expiry_before ,
				'phoen_points_notification_before'=>$phoen_points_notification_before ,
		
			);
			
			update_option('phoen_rewpts_notification_settings',$btn_settings);
			
		}
	}

?>
<div class="cat_mode">
		
	<form method="post" name="phoen_woo_btncreate">
		
	<?php 
	
		$phoen_rewpts_notification_data = get_option('phoen_rewpts_notification_settings',true);
		
		$enable_plugin_product_page = isset($phoen_rewpts_notification_data['enable_plugin_product_page'])?$phoen_rewpts_notification_data['enable_plugin_product_page']:'1';
		
		$phoen_rewpts_notification_product_page = isset($phoen_rewpts_notification_data['phoen_rewpts_notification_product_page'])?$phoen_rewpts_notification_data['phoen_rewpts_notification_product_page']:'You Will get {points} Points On Completing This Order';
		
		$enable_plugin_cart_page = isset($phoen_rewpts_notification_data['enable_plugin_cart_page'])?$phoen_rewpts_notification_data['enable_plugin_cart_page']:'1';
		
		$phoen_rewpts_notification_cart_page = isset($phoen_rewpts_notification_data['phoen_rewpts_notification_cart_page'])?$phoen_rewpts_notification_data['phoen_rewpts_notification_cart_page']:'You Will get {points} Points On Completing This Order';
		
		$enable_plugin_checkout_page = isset($phoen_rewpts_notification_data['enable_plugin_checkout_page'])?$phoen_rewpts_notification_data['enable_plugin_checkout_page']:'1';
		
		$phoen_rewpts_notification_checkout_page = isset($phoen_rewpts_notification_data['phoen_rewpts_notification_checkout_page'])?$phoen_rewpts_notification_data['phoen_rewpts_notification_checkout_page']:'You Will get {points} Points On Completing This Order';
		
		$phoen_rewpts_notification_cart_apply_box = isset($phoen_rewpts_notification_data['apply_box_enable_on_cart'])?$phoen_rewpts_notification_data['apply_box_enable_on_cart']:'1';
		
		$phoen_apply_box_notification_cart_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_cart_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_cart_page']:'You can apply {points} Points to get {price} Discount.';
		
		$phoen_rewpts_notification_checkout_apply_box = isset($phoen_rewpts_notification_data['apply_box_enable_on_checkout'])?$phoen_rewpts_notification_data['apply_box_enable_on_checkout']:'1';
			
		$phoen_apply_box_notification_checkout_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_checkout_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_checkout_page']:'You can apply {points} Points to get {price} Discount.';
		
		$phoen_apply_box_notification_bonus_page = isset($phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page'])?$phoen_rewpts_notification_data['phoen_apply_box_notification_bonus_page']:'You Will get {points} Bonus Points On Completing This Order';
		
		$phoen_points_expiry_before = isset($phoen_rewpts_notification_data['phoen_points_expiry_before'])?$phoen_rewpts_notification_data['phoen_points_expiry_before']:'0';
		
		$phoen_points_notification_before = isset($phoen_rewpts_notification_data['phoen_points_notification_before'])?$phoen_rewpts_notification_data['phoen_points_notification_before']:'0';
		
		wp_nonce_field( 'phoen_rewpts_btncreate_action', 'phoen_rewpts_btncreate_action_field' ); 
		
		if ( !function_exists( 'wc_help_tip' ) ) {
		
			require_once( ABSPATH . 'includes/wc-core-functions.php' );
		} 
  
	// Help tip text 
$tip = ''; 
  
// Allow sanitized HTML if true or escape 
$allow_html = false; 
  
// NOTICE! Understand what this does before running. 

		?>
		
		<table class="form-table">
		
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Enable Points Notification on Product Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
				
					<input type="checkbox"  name="enable_plugin_product_page" id="enable_plugin_product_page" value="1" <?php checked( $enable_plugin_product_page, 1 ); ?> >
					
				</td>
				
			</tr>
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Notification Message on Product Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
					<textarea rows="4" cols="30" name="phoen_rewpts_notification_product_page"><?php echo esc_html($phoen_rewpts_notification_product_page);?></textarea>
					<br />{points} number of points earned;				
				</td>
				
			</tr>
			
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Enable Points Notification on Cart Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
				
					<input type="checkbox"  name="enable_plugin_cart_page" id="enable_plugin_cart_page" value="1" <?php checked( $enable_plugin_cart_page, 1 ); ?> >
					
				</td>
				
			</tr>
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Notification Message on Cart Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
					<textarea rows="4" cols="30" name="phoen_rewpts_notification_cart_page"><?php echo esc_html($phoen_rewpts_notification_cart_page);?></textarea>
				<br />{points} number of points earned;
				</td>
				
			</tr>
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Enable Points Notification on Checkout Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
				
					<input type="checkbox"  name="enable_plugin_checkout_page" id="enable_plugin_checkout_page" value="1" <?php checked( $enable_plugin_checkout_page, 1 ); ?>>
					
				</td>
				
			</tr>
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Notification Message on Checkout Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
					<textarea rows="4" cols="30" name="phoen_rewpts_notification_checkout_page"><?php echo esc_html($phoen_rewpts_notification_checkout_page);?></textarea>
				<br />{points} number of points earned;
				</td>
				
			</tr>
			<tr class="phoen-user-user-login-wrap">
				
					<th>
					
						<label><?php _e('Enable Apply Point On Cart Page','phoen-rewpts'); ?> </label>
						
					</th>
					
					<td>
					
						<input type="checkbox"  name="apply_box_enable_on_cart" id="apply_box_enable_on_cart" value="1" <?php checked( $phoen_rewpts_notification_cart_apply_box, 1 ); ?>>
						
					</td>
					
			</tr>	
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Apply Box Notification Message on Cart Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
					<textarea rows="4" cols="30" name="phoen_apply_box_notification_cart_page"><?php echo esc_html($phoen_apply_box_notification_cart_page);?></textarea>
					<br />{points} number of points earned;<br />
					{price} amount of point;
				</td>
				
			</tr>

			
			<tr class="phoen-user-user-login-wrap">
			
				<th>
				
					<label><?php _e('Enable Apply Point On Checkout Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
				
					<input type="checkbox"  name="apply_box_enable_on_checkout" id="apply_box_enable_on_checkout" value="1" <?php checked( $phoen_rewpts_notification_checkout_apply_box, 1 ); ?>>
					
				</td>
				
			</tr>	
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Apply Box Notification Message on Checkout Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
					<textarea rows="4" cols="30" name="phoen_apply_box_notification_checkout_page"><?php echo esc_html($phoen_apply_box_notification_checkout_page);?></textarea>
					<br />{points} number of points earned;<br />
					{price} amount of point;
				</td>
				
			</tr>
			
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
				<th>
				
					<label><?php _e('Bonus Notification Message on Product/Cart/Checkout Page','phoen-rewpts'); ?> </label>
					
				</th>
				
				<td>
					<textarea rows="4" cols="30" name="phoen_apply_box_notification_bonus_page"><?php echo esc_html($phoen_apply_box_notification_bonus_page);?></textarea>
				<br />{points} number of points earned;<br />
				</td>
				
			</tr>
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
		
				<th>
					
					<label><?php _e('Sending Email Before Expiry Day','phoen-rewpts'); ?> </label>
				
				</th>
				
				<td>
				
					<input type="number" min="0" step="any" name="phoen_points_expiry_before" class="phoen_points_expiry_before" value="<?php echo $phoen_points_expiry_before; ?>" > 
					
				</td>
				
			</tr>
			
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
		
				<th>
					 
					<label><?php _e('Sending Email For Points Reminder Notification Before Months','phoen-rewpts'); ?> </label>
				
				</th>
				
				<td>
				
					<input type="number" min="0" step="any" name="phoen_points_notification_before" class="phoen_points_notification_before" value="<?php echo $phoen_points_notification_before; ?>" > 
					
				</td>
				
			</tr>
			<tr class="phoeniixx_phoe_rewpts_wrap phoen-user-user-login-wrap">
				
					<td colspan="2">
					
						<input type="submit" value="<?php _e('Save','phoen-rewpts'); ?>" name="phoen_rewpts_notification_submit" id="submit" class="button button-primary">
					
					</td>
					
				</tr>	
		</table>
	
	</form>
	
</div>