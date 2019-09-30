<?php
/**
 * Event unscheduled trigger class
 */

namespace underDEV\AdvancedCronManagerPRO\Integration;

use BracketSpace\Notification\Abstracts\Trigger;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Event unscheduled trigger class
 */
class EventUnscheduledTrigger extends Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct(
			'advanced-cron-manager/event-unscheduled',
			__( 'Event unscheduled', 'advanced-cron-manager' )
		);

		$this->add_action( 'advanced-cron-manager/event/unscheduled' );

		$this->set_group( __( 'Advanced Cron Manager', 'advanced-cron-manager' ) );

		$this->set_description(
			__( 'Fires when cron even is unscheduled with ACM', 'advanced-cron-manager' )
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
	public function action( $event ) {
		$time_offset = get_option( 'gmt_offset' ) * 3600;

		$this->event                = $event;
		$this->datetime_unscheduled = time() + $time_offset;
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
			'slug' => 'datetime_unscheduled',
			'name' => __( 'Date and Time unscheduled', 'advanced-cron-manager' ),
		) ) );

    }

}
