<?php
/**
 * License settings part
 */
?>

<div id="license-settings">

	<div class="tile">

		<h3 class="tile-header"><?php _e( 'License', 'advanced-cron-manager' ); ?></h3>

		<div class="tile-content">

			<div class="status <?php echo esc_attr( $this->get_var( 'status' ) ); ?>">
				<?php echo $this->get_var( 'status_message' ); ?>
			</div>

			<form id="license-form">

				<input type="text" class="widefat license-key" name="license_key" value="<?php esc_attr_e( $this->get_var( 'license_key' ), 'advanced-cron-manager' ); ?>" placeholder="<?php esc_attr_e( 'License key', 'advanced-cron-manager' ); ?>" <?php if ( $this->get_var( 'status' ) == 'valid' ) echo 'disabled="disabled"'; ?>>
				<input type="submit" class="button-secondary" data-nonce="<?php echo esc_attr( $this->get_var( 'nonce' ) ) ?>" data-action="<?php echo esc_attr( $this->get_var( 'action' ) ) ?>" value="<?php esc_attr_e( $this->get_var( 'button_label' ), 'advanced-cron-manager' ); ?>">

			</form>

		</div>

	</div>

</div>
