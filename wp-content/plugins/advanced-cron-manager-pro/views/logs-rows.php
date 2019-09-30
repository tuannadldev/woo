<?php
/**
 * Logs accordion in Event's logs tab
 */

$logs            = $this->get_var( 'logs' );
$logs_library    = $this->get_var( 'logs_library' );
$extended        = $this->get_var( 'extended' );
$time_offset     = get_option( 'gmt_offset' ) * 3600;
$date_format     = get_option( 'date_format' );
$time_format     = get_option( 'time_format' );
$datetime_format = $date_format . ' ' . $time_format;

?>

<?php foreach ( $logs as $log ): ?>

	<?php $messages = $logs_library->translate_messages( $log->errors ); ?>

	<li>
		<a class="toggle <?php if ( $extended ) echo 'extended'; ?>" href="javascript:void(0);">

			<?php if ( $extended ): ?>
				<span class="hook"><?php echo esc_html( $log->hook ); ?></span> -
				<span class="schedule"><?php echo esc_html( $log->schedule ); ?></span> -
				<?php $args = unserialize( $log->args ) ?>
				<?php if ( ! empty( $args ) ): ?>
					<?php foreach ( $args as $arg ): ?>
						<span class="argument"><?php echo esc_html( $arg ); ?></span>
					<?php endforeach ?>
					 -
				<?php endif ?>

			<?php endif ?>

			<?php echo esc_html( date_i18n( $datetime_format, $log->logged_time + $time_offset ) ); ?>

			<?php if ( isset( $messages['error'] ) ): ?>
				<span class="label error"><?php esc_html_e( 'Error', 'advanced-cron-manager' ); ?></span>
			<?php endif ?>
			<?php if ( isset( $messages['warning'] ) ): ?>
				<span class="label warning"><?php esc_html_e( 'Warnings / Notices', 'advanced-cron-manager' ); ?></span>
			<?php endif ?>
			<?php if ( isset( $messages['custom'] ) ): ?>
				<span class="label custom"><?php esc_html_e( 'Custom logs', 'advanced-cron-manager' ); ?></span>
			<?php endif ?>

		</a>
		<ul class="inner">
			<li class="log performance">
				<?php echo esc_html( sprintf( __( 'Delay: %s', 'advanced-cron-manager' ), $log->diff . ' s' ) ); ?>
			</li>
			<?php foreach ( $messages as $type => $child_messages ): ?>
				<?php foreach ( $child_messages as $message ): ?>
					<li class="log <?php echo esc_attr( $type ); ?>">
						<?php echo $message; ?>
					</li>
				<?php endforeach ?>
			<?php endforeach ?>
		</ul>
	</li>

<?php endforeach ?>
