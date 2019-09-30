<?php
/**
 * Logs accordion in Event's logs tab
 */

$logs         = $this->get_var( 'logs' );
$logs_library = $this->get_var( 'logs_library' );
$total_logs   = $this->get_var( 'total_logs' );
$event_hash   = $this->get_var( 'event_hash' );

?>

<?php if ( empty( $logs ) ): ?>

	<p class="not-found"><?php esc_html_e( 'No saved logs for this task yet', 'advanced-cron-manager' ); ?></p>

<?php else: ?>

	<ul class="logs-accordion">
		<?php $this->get_view( 'logs-rows' ); ?>
	</ul>

	<?php if ( $total_logs > $logs_library->per_page ): ?>
		<a href="javascript:void(0);" class="button-secondary load-more-logs" data-total="<?php echo esc_attr( $total_logs ); ?>" data-page="1" data-event="<?php echo esc_attr( $event_hash ); ?>"><?php esc_html_e( 'Load more logs', 'advanced-cron-manager' ); ?></a>
	<?php endif ?>

<?php endif ?>
