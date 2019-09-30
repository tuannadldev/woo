<?php
/**
 * Logs accordion in Event's logs tab
 */

?>

<div id="logs-section">

	<div class="tile">

		<h3 class="tile-header"><?php _e( 'Logs', 'advanced-cron-manager' ); ?></h3>

		<div class="tile-content">

			<?php $this->get_view( 'logs-table' ); ?>

		</div>

	</div>

</div>
