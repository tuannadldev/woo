<?php
/**
 * Log settings part
 */

$settings = $this->get_var( 'settings' );

if ( ! empty( $settings['log_executions'] ) ) {
	$display_dependants = '';
} else {
	$display_dependants = 'style="display: none;"';
}

?>

<div id="log-settings">

	<div class="tile">

		<h3 class="tile-header"><?php _e( 'Log settings', 'advanced-cron-manager' ); ?></h3>

		<div class="tile-content">

			<form id="log-settings-form">

				<label class="master-setting">
					<input type="checkbox" name="log_executions" value="1" <?php checked( $settings['log_executions'], 1 ); ?>>
					<?php esc_html_e( 'Log cron executions', 'advanced-cron-manager' ); ?>
				</label>

				<label class="dependants" <?php echo $display_dependants; ?>>
					<input type="checkbox" name="log_warnings" value="1" <?php checked( $settings['log_warnings'], 1 ); ?>>
					<?php esc_html_e( 'Log PHP warnings and notices', 'advanced-cron-manager' ); ?>
				</label>

				<label class="dependants" <?php echo $display_dependants; ?>>
					<input type="checkbox" name="log_errors" value="1" <?php checked( $settings['log_errors'], 1 ); ?>>
					<?php esc_html_e( 'Log PHP fatal errors', 'advanced-cron-manager' ); ?>
				</label>

				<label class="dependants" <?php echo $display_dependants; ?>>
					<input type="checkbox" name="log_performance" value="1" <?php checked( $settings['log_performance'], 1 ); ?>>
					<?php esc_html_e( 'Log performance stats', 'advanced-cron-manager' ); ?>
				</label>

				<label>
					<input type="checkbox" name="display_section" value="1" <?php checked( $settings['display_section'], 1 ); ?>>
					<?php esc_html_e( 'Display all logs section', 'advanced-cron-manager' ); ?>
				</label>

				<label class="dependants" <?php echo $display_dependants; ?>>
					<?php esc_html_e( 'Logs limit', 'advanced-cron-manager' ); ?>
					<input type="number" class="widefat" min="0" name="logs_number" value="<?php echo esc_attr( $settings['logs_number'] ); ?>">
					<p class="description"><?php esc_html_e( 'Set to 0 to never clear the logs. If limit will be hit the oldest logs will be removed automatically', 'advanced-cron-manager' ); ?></p>
				</label>

				<input type="submit" class="button-secondary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'acm/logs/settings/save' ) ) ?>" value="<?php esc_attr_e( 'Save settings', 'advanced-cron-manager' ); ?>">

			</form>

		</div>

	</div>

</div>
