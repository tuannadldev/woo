<?php
/**
 * Cron error trigger class
 */

namespace underDEV\AdvancedCronManagerPRO\Integration;

use BracketSpace\Notification\Abstracts\Trigger;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Cron error trigger class
 */
class CronErrorTrigger extends Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct(
			'advanced-cron-manager/event-fail',
			__( 'Event failed to execute', 'advanced-cron-manager' )
		);

		$this->add_action( 'advanced-cron-manager/error', 10, 4 );

		$this->set_group( __( 'Advanced Cron Manager', 'advanced-cron-manager' ) );

		$this->set_description(
			__( 'Fires when any cron event fail', 'advanced-cron-manager' )
		);

	}

	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * You can use the action method arguments as usually.
	 *
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $logger, $event, $error, $error_type ) {

		$this->logger      = $logger;
		$this->event       = $event;
		$this->error       = $error;
		$this->error_type  = $error_type;
		$this->time_called = time() + $this->logger->time_offset;

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'event_hook',
			'name'        => __( 'Event hook', 'advanced-cron-manager' ),
			'description' => 'cron_hook',
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->event->hook;
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'event_schedule',
			'name'        => __( 'Event schedule', 'advanced-cron-manager' ),
			'description' => 'daily',
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->event->schedule;
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'event_args',
			'name'        => __( 'Event args', 'advanced-cron-manager' ),
			'description' => '1, single',
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return implode( ', ', $trigger->event->args );
			},
		) ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'time_called',
			'name' => __( 'Time called', 'advanced-cron-manager' ),
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'error_type',
			'name'        => __( 'Error type', 'advanced-cron-manager' ),
			'description' => 'fatal or warning (includes warnings and notices)',
			'resolver'    => function( $trigger ) {
				return $trigger->error_type;
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'error_message',
			'name'        => __( 'Error message', 'advanced-cron-manager' ),
			'description' => 'Fatal error: Call to undefined function dupa() in /var/www/html/wp-content/plugins/cron-dummy.php at line 69',
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->error;
			},
		) ) );

    }

}
