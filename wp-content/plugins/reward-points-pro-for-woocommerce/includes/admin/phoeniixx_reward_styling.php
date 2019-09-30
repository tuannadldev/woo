<?php if ( ! defined( 'ABSPATH' ) ) exit; 

	if ( ! empty( $_POST ) && check_admin_referer( 'phoen_rewpts_btncreate_action', 'phoen_rewpts_btncreate_action_field' ) ) {

		if(isset( $_POST['custom_btn'] )){
			
			$apply_btn_title    = (isset($_POST['apply_btn_title']))?sanitize_text_field( $_POST['apply_btn_title'] ):'APPLY POINTS';
			
			$apply_reward_amount_title    = isset($_POST['apply_reward_amount_title'])?$_POST['apply_reward_amount_title'] :'Reward Amount';
			
			$phoen_select_text = isset($_POST['phoen_select_text'])?$_POST['phoen_select_text'] :'';
		
			$apply_topmargin    = (isset($_POST['apply_topmargin']))?sanitize_text_field( $_POST['apply_topmargin'] ):'8';
			
			$apply_rightmargin    = (isset($_POST['apply_rightmargin']))?sanitize_text_field( $_POST['apply_rightmargin'] ):'10';
			
			$apply_bottommargin    = (isset($_POST['apply_bottommargin']))?sanitize_text_field( $_POST['apply_bottommargin'] ):'8';
			
			$apply_leftmargin    = (isset($_POST['apply_leftmargin']))?sanitize_text_field( $_POST['apply_leftmargin'] ):'10';
							
			$apply_btn_bg_col    = (isset($_POST['apply_btn_bg_col']))?sanitize_text_field( $_POST['apply_btn_bg_col'] ):'';
			
			$apply_btn_txt_col    = (isset($_POST['apply_btn_txt_col']))?sanitize_text_field( $_POST['apply_btn_txt_col'] ):'#000000';
			
			$apply_btn_txt_hov_col    = (isset($_POST['apply_btn_txt_hov_col']))?sanitize_text_field( $_POST['apply_btn_txt_hov_col'] ):'';
			
			$apply_btn_hov_col    = (isset($_POST['apply_btn_hov_col']))?sanitize_text_field( $_POST['apply_btn_hov_col'] ):'';
			
			$apply_btn_border_style    = (isset($_POST['apply_btn_border_style']))?sanitize_text_field( $_POST['apply_btn_border_style'] ):'none';
			
			$apply_btn_border    = (isset($_POST['apply_btn_border']))?sanitize_text_field( $_POST['apply_btn_border'] ):'0';
			
			$apply_btn_bor_col    = (isset($_POST['apply_btn_bor_col']))?sanitize_text_field( $_POST['apply_btn_bor_col'] ):'';
			
			$apply_btn_rad    = (isset($_POST['apply_btn_rad']))?sanitize_text_field( $_POST['apply_btn_rad'] ):'0';
				
			$remove_btn_title    = (isset($_POST['remove_btn_title']))?sanitize_text_field( $_POST['remove_btn_title'] ):'REMOVE POINTS';
			
			$phoen_select_text_product_page    = (isset($_POST['phoen_select_text_product_page']))?sanitize_text_field( $_POST['phoen_select_text_product_page'] ):'';
			
			
			
			
			$div_bg_col    = (isset($_POST['div_bg_col']))?sanitize_text_field( $_POST['div_bg_col'] ):'#fff';
			$div_border_style    = (isset($_POST['div_border_style']))?sanitize_text_field( $_POST['div_border_style'] ):'solid';
			$div_border    = (isset($_POST['div_border']))?sanitize_text_field( $_POST['div_border'] ):'1';
			$div_bor_col    = (isset($_POST['div_bor_col']))?sanitize_text_field( $_POST['div_bor_col'] ):'#ccc';
			$div_rad    = (isset($_POST['div_rad']))?sanitize_text_field( $_POST['div_rad'] ):'0';
			
			$btn_settings=array(
				
					'apply_btn_title'		=>		$apply_btn_title,
					
					'apply_topmargin'		=>		$apply_topmargin,
					
					'apply_rightmargin'	=>		$apply_rightmargin,
					
					'apply_bottommargin'	=>		$apply_bottommargin,
					
					'apply_leftmargin'	=>		$apply_leftmargin,
					
					'apply_btn_bg_col'	=>		$apply_btn_bg_col,
					
					'apply_btn_txt_col'	=>		$apply_btn_txt_col,
					
					'apply_btn_txt_hov_col'=>$apply_btn_txt_hov_col,
					
					'apply_btn_hov_col'	=>		$apply_btn_hov_col,
					
					'apply_btn_border_style'=>	$apply_btn_border_style,
					
					'apply_btn_border'	=>		$apply_btn_border,
					
					'apply_btn_bor_col'	=>		$apply_btn_bor_col,
					
					'apply_btn_rad'		=>		$apply_btn_rad,
					
					'remove_btn_title'	=>		$remove_btn_title,
					
					'div_bg_col'		=>		$div_bg_col,
					
					'div_border_style'	=>		$div_border_style,
					
					'div_border'		=>		$div_border,
					
					'div_bor_col'		=>		$div_bor_col,
					
					'div_rad'=>$div_rad,
					
					'apply_reward_amount_title'=>$apply_reward_amount_title,
					
					'phoen_select_text'=>$phoen_select_text,
				
					'phoen_select_text_product_page'=>$phoen_select_text_product_page,
					
					
			);
			
			update_option('phoen_rewpts_custom_btn_styling',$btn_settings);
			
		}
	}
	 
	 
?>
	<div class="cat_mode">
			
		<form method="post" name="phoen_woo_btncreate">
			
			<?php $gen_settings=get_option('phoen_rewpts_custom_btn_styling');
			
			$phoen_select_text = isset($gen_settings['phoen_select_text'])?$gen_settings['phoen_select_text']:'below_cart';
		
			$phoen_select_text_product_page = isset($gen_settings['phoen_select_text_product_page'])?$gen_settings['phoen_select_text_product_page']:'below_add_cart';
				
			wp_nonce_field( 'phoen_rewpts_btncreate_action', 'phoen_rewpts_btncreate_action_field' ); ?>
					
			<table class="form-table">
			
				<tr class="phoen-user-user-login-wrap">
					
					<th>
					
						<?php _e('Apply Button title','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						
						<input type="text" class="apply_btn_title" name="apply_btn_title" value="<?php echo(isset($gen_settings['apply_btn_title'])) ?$gen_settings['apply_btn_title']:'APPLY POINTS';?>">
					
					</td>
				
				</tr>
				<tr class="phoen-user-user-login-wrap" >
					
					<th>
					
						<?php _e('Remove Button title','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						
						<input type="text" class="remove_btn_title" name="remove_btn_title" value="<?php echo(isset($gen_settings['remove_btn_title'])) ?$gen_settings['remove_btn_title']:'REMOVE POINTS';?>">
					
					</td>
				
				</tr>
				
				<tr class="phoen-user-user-login-wrap" >
					
					<th>
					
						<?php _e('Coupon Name','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						
						<input type="text" class="apply_reward_amount_title" name="apply_reward_amount_title" value="<?php echo(isset($gen_settings['apply_reward_amount_title'])) ?$gen_settings['apply_reward_amount_title']:'Reward Amount';?>">
					
					</td>
				
				</tr>
				
				<tr class="phoen-user-user-login-wrap" >
					
					<th>
					
						<?php _e('Reward Points Notification Text Position on Cart Page','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<select name="phoen_select_text">
						
							<option value="below_cart" <?php if($phoen_select_text=='below_cart'){echo 'selected' ;} ?>><?php _e('Below','phoen-rewpts'); ?></option>
							<option value="above_cart" <?php if($phoen_select_text=='above_cart'){echo 'selected' ;} ?>><?php _e('Above','phoen-rewpts'); ?></option>
							
						</select>
					
					</td>
				
				</tr>
								
				<tr class="phoen-user-user-login-wrap" >
					
					<th>
					
						<?php _e('Reward Points Notification Text Position on Product Page','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<select name="phoen_select_text_product_page">
						
							<option value="below_add_cart" <?php if($phoen_select_text_product_page=='below_add_cart'){echo 'selected' ;} ?>><?php _e('Below Add To Cart','phoen-rewpts'); ?></option>
							<option value="above_add_cart" <?php if($phoen_select_text_product_page=='above_add_cart'){echo 'selected' ;} ?>><?php _e('Above Add To Cart','phoen-rewpts'); ?></option>
							
						</select>
					
					</td>
				
				</tr>
				
				
				
				<tr class="phoen-user-user-login-wrap">

				<th> 
				
					<?php _e('Padding','phoen-rewpts'); ?>
					
				</th>
					
					<td>
					
						<input class="btn_num"   placeholder="<?php _e('TOP','phoen-rewpts'); ?>" style="max-width:60px;font-size:12px;" min="0" name="apply_topmargin" 	type="number" value="<?php echo(isset($gen_settings['apply_topmargin'])) ?$gen_settings['apply_topmargin']:'8';?>">
							
						<input class="btn_num"  placeholder="<?php _e('RIGHT','phoen-rewpts'); ?>" style="max-width:65px;font-size:12px;" min="0" name="apply_rightmargin" 	type="number" value="<?php echo(isset($gen_settings['apply_rightmargin'])) ?$gen_settings['apply_rightmargin']:'10';?>">

						<input class="btn_num"  placeholder="<?php _e('BOTTOM','phoen-rewpts'); ?>" style="max-width:65px;font-size:12px;" min="0" name="apply_bottommargin" 	type="number" value="<?php echo(isset($gen_settings['apply_bottommargin'])) ?$gen_settings['apply_bottommargin']:'8';?>">
							
						<input class="btn_num"   placeholder="<?php _e('LEFT','phoen-rewpts'); ?>" style="max-width:65px;font-size:12px;" min="0" name="apply_leftmargin" 	type="number" value="<?php echo(isset($gen_settings['apply_leftmargin'])) ?$gen_settings['apply_leftmargin']:'10';?>"><span class="pixel-11"><?php _e('px','phoen-rewpts'); ?></span>

					</td>

				</tr>
				
				<tr class="phoen-user-user-login-wrap">
					
					<th>
					
						<?php _e('Button Background Color','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						
						<input class="btn_color_picker btn_bg_col" type="text" name="apply_btn_bg_col" value="<?php echo(isset($gen_settings['apply_btn_bg_col'])) ?$gen_settings['apply_btn_bg_col']:'#000000';?>">
					
					</td>
				
				</tr>
				
				</tr>
				
				<tr class="phoen-user-user-login-wrap">
					<th>
					
						<?php _e('Button Background Hover color','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<input class="btn_color_picker btn_hov_col" type="text" name="apply_btn_hov_col" value="<?php echo(isset($gen_settings['apply_btn_hov_col'])) ?$gen_settings['apply_btn_hov_col']:'';?>">
					
					</td>
					
				</tr>
				
				
				<tr class="phoen-user-user-login-wrap">
					<th>
					
						<?php _e('Button Text color','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						
						<input class="btn_color_picker btn_txt_col" type="text" name="apply_btn_txt_col" value="<?php echo(isset($gen_settings['apply_btn_txt_col'])) ?$gen_settings['apply_btn_txt_col']:'#fff';?>">
					
					</td>
					
				</tr>
				
				<tr class="phoen-user-user-login-wrap">
					<th>
					
						<?php _e('Button Text Hover color','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						
						<input class="btn_color_picker btn_txt_col" type="text" name="apply_btn_txt_hov_col" value="<?php echo(isset($gen_settings['apply_btn_txt_hov_col'])) ?$gen_settings['apply_btn_txt_hov_col']:'#fff';?>">
					
					</td>
					
				
				<tr class="phoen-user-user-login-wrap">
				
					<th>
					
						<?php _e('Border Style','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<?php $st = (isset($gen_settings['apply_btn_border_style'])) ? $gen_settings['apply_btn_border_style'] : 'none'; ?>
						
						<select  name="apply_btn_border_style" class="btn_border_style">
							
							<option value="none" <?php if($st=='none') echo 'selected';?>><?php _e('None','phoen-rewpts'); ?></option>
							
							<option value="solid" <?php if($st=='solid') echo 'selected';?>><?php _e('Solid','phoen-rewpts'); ?></option>
							
							<option value="dashed" <?php if($st=='dashed') echo 'selected';?>><?php _e('Dashed','phoen-rewpts'); ?></option>
							
							<option value="dotted" <?php if($st=='dotted') echo 'selected';?>><?php _e('Dotted','phoen-rewpts'); ?></option>
							
							<option value="double" <?php if($st=='double') echo 'selected';?>><?php _e('Double','phoen-rewpts'); ?></option>

						</select>
						
					</td>
					
				</tr>
				
				<tr class="btn_bor phoen-user-user-login-wrap">
					<th>
					
						<?php _e('Button Border Width','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<input class="btn_num" min="0" type="number" name="apply_btn_border" style="max-width:105px;" value="<?php echo(isset($gen_settings['apply_btn_border'])) ?$gen_settings['apply_btn_border']:'0';?>"><?php _e('px','phoen-rewpts'); ?>
					
					</td>
					
				</tr>
								
				<tr class="btn_bor phoen-user-user-login-wrap">
					<th>
					
						<?php _e('Button border color','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						<input class="btn_color_picker btn_bor_col"  type="text" name="apply_btn_bor_col" value="<?php echo(isset($gen_settings['apply_btn_bor_col'])) ?$gen_settings['apply_btn_bor_col']:'';?>">
					
					</td>
					
				</tr>
				
				<tr class="phoen-user-user-login-wrap">
				
					<th>
					
						<?php _e('Button Radius','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<input  class="btn_num" min="0" type="number" style="max-width:105px;" name="apply_btn_rad" value="<?php echo(isset($gen_settings['apply_btn_rad'])) ?$gen_settings['apply_btn_rad']:'0';?>"><?php _e('px','phoen-rewpts'); ?>
					
					</td>
					
				</tr>
				
				<tr class="phoen-user-user-login-wrap">
				
					<th>
					
						<?php _e('Button Box Background Color','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<input  class="btn_color_picker" type="text" style="max-width:105px;" name="div_bg_col" value="<?php echo(isset($gen_settings['div_bg_col'])) ?$gen_settings['div_bg_col']:'#fff';?>">
					
					</td>
					
				</tr>
				
				
				
				
				<tr class="phoen-user-user-login-wrap">
				
					<th>
					
						<?php _e('Button Box Border Style','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<?php $st = (isset($gen_settings['div_border_style'])) ? $gen_settings['div_border_style'] : 'solid'; ?>
						
						<select  name="div_border_style" class="div_border_style">
							
							<option value="none" <?php if($st=='none') echo 'selected';?>><?php _e('None','phoen-rewpts'); ?></option>
							
							<option value="solid" <?php if($st=='solid') echo 'selected';?>><?php _e('Solid','phoen-rewpts'); ?></option>
							
							<option value="dashed" <?php if($st=='dashed') echo 'selected';?>><?php _e('Dashed','phoen-rewpts'); ?></option>
							
							<option value="dotted" <?php if($st=='dotted') echo 'selected';?>><?php _e('Dotted','phoen-rewpts'); ?></option>
							
							<option value="double" <?php if($st=='double') echo 'selected';?>><?php _e('Double','phoen-rewpts'); ?></option>

						</select>
						
					</td>
					
				</tr>
				
				<tr class="btn_bor phoen-user-user-login-wrap">
					<th>
					
						<?php _e('Button Box Border Width','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<input class="btn_num" min="0"  type="number" name="div_border" style="max-width:105px;" value="<?php echo(isset($gen_settings['div_border'])) ?$gen_settings['div_border']:'1';?>"><?php _e('px','phoen-rewpts'); ?>
					
					</td>
					
				</tr>
								
				<tr class="btn_bor phoen-user-user-login-wrap">
					<th>
					
						<?php _e('Button Box border color','phoen-rewpts'); ?>
						
					</th>
					
					<td>
						<input class="btn_color_picker btn_bor_col"  type="text" name="div_bor_col" value="<?php echo(isset($gen_settings['div_bor_col'])) ?$gen_settings['div_bor_col']:'#ccc';?>">
					
					</td>
					
				</tr>
				
				<tr class="phoen-user-user-login-wrap">
				
					<th>
					
						<?php _e('Button Box Radius','phoen-rewpts'); ?>
						
					</th>
					
					<td>
					
						<input  class="btn_num" min="0" type="number" style="max-width:105px;" name="div_rad" value="<?php echo(isset($gen_settings['div_rad'])) ?$gen_settings['div_rad']:'0';?>"><?php _e('px','phoen-rewpts'); ?>
					
					</td>
					
				</tr>
				
				
				
				<tr class="phoen-user-user-login-wrap">
				
					<td>
					
						<input type="submit" class="button button-primary" value="<?php _e('Save','phoen-rewpts'); ?>" name="custom_btn">
						
						
					</td>
					
				</tr>
						
			</table>
		
		</form>
		
	</div>