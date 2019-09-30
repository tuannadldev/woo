<?php

	function phoen_reword_woo_add_custom_general_fields() {

		global $woocommerce, $post;
		
		$phoen_rewpts_set_point_data = get_option('phoe_set_point_value',true);
		$reward_product_point_price_type=isset($phoen_rewpts_set_point_data['point_type'])?$phoen_rewpts_set_point_data['point_type']:'fixed_price';
		
		$product_field_type_ids = get_post_meta( $post->ID, '_product_reward_point_val', true );
		$product_percentage_points = get_post_meta( $post->ID, '_product_percentage_point_val', true );		
		
		$product_field_type_ids = isset($product_field_type_ids)?$product_field_type_ids:'';
		$product_percentage_points = isset($product_percentage_points)?$product_percentage_points:'';
		
		  ?>
		  <div class="options_group">
		  <?php if($reward_product_point_price_type=='fixed_price'):?>
			<p class="form-field">
				<label for="phoen_reword"><?php _e( 'Product Fixed Point:', 'phoen-rewpts' ); ?></label>
				<input type="number" min="0" step="any" name="phoen_product_wide_rewar_points" value="<?php echo $product_field_type_ids; ?>" >
			</p>
			<?php else: ?>
			<p class="form-field">
				<label for="phoen_reword"><?php _e( 'Product Percentage Points:', 'phoen-rewpts' ); ?></label>
				<input type="number" min="0" step="any" name="phoen_percentage_wide_rewar_points" value="<?php echo $product_percentage_points; ?>" >
			</p>
		  <?php endif; ?>
		  </div>
		  <?php
		 
			
	}

	function phoen_reword_woo_add_custom_general_fields_save( $post_id ){
		
		$phoen_product_points = isset($_POST['phoen_product_wide_rewar_points'])?$_POST['phoen_product_wide_rewar_points']:'';
		$phoen_percentage_wide_rewar_points = isset($_POST['phoen_percentage_wide_rewar_points'])?$_POST['phoen_percentage_wide_rewar_points']:'';
		
		update_post_meta( $post_id, '_product_reward_point_val', $phoen_product_points);
		update_post_meta( $post_id, '_product_percentage_point_val', $phoen_percentage_wide_rewar_points);
		
	}
		
		
	//add custome fields on products 
	add_action( 'woocommerce_product_options_general_product_data', 'phoen_reword_woo_add_custom_general_fields' );

	// Save Fields
	add_action( 'woocommerce_process_product_meta', 'phoen_reword_woo_add_custom_general_fields_save' );
?>