<?php
/**
 * Logger class
 */

namespace underDEV\AdvancedCronManagerPRO;
use underDEV\AdvancedCronManager\Cron\Events;

class Logger {

	/**
	 * Events class
	 * @var object
	 */
	private $events;

	/**
	 * LogsLibrary class
	 * @var object
	 */
	private $library;

	/**
	 * License Manager class
	 * @var object
	 */
	private $license_manager;

	/**
	 * LogOptions class
	 * @var object
	 */
	private $options;

	/**
	 * Current event object
	 * Used in observer actions
	 * @var object
	 */
	public $current_event;

	/**
	 * Current logs for the event
	 * @var array
	 */
	private $logs = array();

	/**
	 * Class constructor
	 */
	public function __construct( Events $events, LogsLibrary $library, License\Manager $license_manager, LogOptions $options ) {

		$this->events          = $events;
		$this->library         = $library;
		$this->license_manager = $license_manager;
		$this->options         = $options;

		$this->time_offset     = get_option( 'gmt_offset' ) * 3600;
		$this->date_format     = get_option( 'date_format' );
		$this->time_format     = get_option( 'time_format' );
		$this->datetime_format = $this->date_format . ' ' . $this->time_format;

	}

	/**
	 * Adds cron actions to observe them
	 * @return void
	 */
	public function add_actions() {

		if ( ! $this->options->is_active( 'log_executions' ) ) {
			return;
		}

		foreach ( $this->events->get_events( true ) as $event_hash => $event ) {

			// log all the events as the first executed action
			add_action( $event->hook, function() use ( $event ) {

				$this->resolve_current_event( $event );

				if ( empty( $this->current_event ) ) {
					return;
				}

				$this->track_performance();

				// catch warnings and notices
				if ( $this->options->is_active( 'log_warnings' ) ) {

					set_error_handler( function( $errno, $errstr, $errfile, $errline ) {

						$error = $errstr . ' in ' . $errfile . ' at line ' . $errline;
				        $this->log( $error, 'warning' );

				        do_action( 'advanced-cron-manager/error', $this, $event, $error, 'warning' );

					}, E_WARNING | E_NOTICE );

				}

				// catch fatal errors

				add_action( 'shutdown', function() use ( $event ) {

					$error = error_get_last();

				    if ( $error['type'] == E_ERROR || $error['type'] == E_USER_ERROR ) {

				    	if ( $this->options->is_active( 'log_errors' ) ) {

					    	$error = 'Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ' at line ' . $error['line'];
					        $this->log( $error, 'error' );

					        do_action( 'advanced-cron-manager/error', $this, $event, $error, 'fatal' );

				        }

				        // see below in the next action how to use this
				        do_action( 'advanced-cron-manager/log', $this );

				        $this->resolve_performance_stats();

				        // calling now because script is about to end
						$this->save_logs();

				    }

				} );

			}, -99999999999999999, 0 );

			// log all the events as the last executed action
			add_action( $event->hook, function() use ( $event ) {

				$this->resolve_current_event( $event );

				if ( empty( $this->current_event ) ) {
					return;
				}

				// restore previous error handler
				if ( $this->options->is_active( 'log_errors' ) ) {
					restore_error_handler();
				}


				/**
				 * USAGE. In your action, just call:
				 *
				 * add_action( 'advanced-cron-manager/log', function( $logger ) {
				 *     $logger->log( 'I am a damn good log' );
				 * } );
				 *
				 * For more detailed guide go to: https://www.wpart.co/how-to-debug-wp-cron/
				 */
				do_action( 'advanced-cron-manager/log', $this );

				$this->resolve_performance_stats();

				// Save the logs to database
				$this->save_logs();

			}, 99999999999999999, 0 );

		}

	}

	/**
	 * Resolves the currently executed event either looking
	 * for a global event set by ACM or grabbing the first event
	 * from the top of the schedule
	 * @param  object $current_action_event event passed to the observer action
	 * @return mixed                        false if current event cannot be resolved
	 */
	public function resolve_current_event( $current_action_event ) {

		global $acm_current_event;

		// check if cron job has been executed manually or via scheduler
		if ( is_object( $acm_current_event ) ) {

			// check if this is the executed event
			if ( $acm_current_event->hash != $current_action_event->hash ) {
				return false;
			}

		} else {

			// cron executed via scheduler, so we have to grab the first event in the row
			foreach ( $this->events->get_events( true ) as $search_event ) {

				// first hit, it's ok because the array is ordered by execution time
				// and all events are rescheduled right a way
				if ( $search_event->hash == $current_action_event->hash ) {
					$acm_current_event = $search_event;
				}

			}

		}

		$this->current_event = $acm_current_event;

	}

	/**
	 * Saves the log to later insert it into database
	 * @param  mixed  $thing anything which is worth saving,
	 *                       will be serialized if needed
	 * @param  string $type  this is only for internal use!
	 * @return void
	 */
	public function log( $thing, $type = 'custom' ) {
		$this->logs[] = array( $type => maybe_serialize( $thing ) );
	}

	/**
	 * Saves logs in the database and cleanups the class properties
	 * @return void
	 */
	public function save_logs() {

		if ( ! $this->license_manager->is_license_valid() ) {
			return;
		}

		// save in database
		$this->library->save_log( $this->current_event, $this->logs );

		// cleanup for next events
		$this->current_event         = null;
		$this->logs                  = array();
		$this->starting_memory_usage = 0;
		$this->starting_time         = 0;

	}

	/**
	 * Tracks event performance: execution time and memory usage
	 * @return void
	 */
	public function track_performance() {

		if ( ! $this->options->is_active( 'log_performance' ) ) {
			return;
		}

		$this->starting_memory_usage = memory_get_usage();
		$this->starting_time         = microtime( true );

	}

	/**
	 * Resolves performance stats as a log entry
	 * @return void
	 */
	public function resolve_performance_stats() {

		if ( ! $this->options->is_active( 'log_performance' ) ) {
			return;
		}

		$memory_usage = memory_get_usage() - $this->starting_memory_usage ;
		$units        = array( 'b', 'kb', 'mb', 'gb', 'tb', 'pb' );
		$i            = floor( log( $memory_usage, 1024 ) );

		if ( ! isset( $units[ $i ] ) ) {
			$memory_usage_h = 'NaN';
		} else {
			$memory_usage_h = @round( $memory_usage / pow( 1024, $i ), 2 ) . ' ' . $units[ $i ];
		}

		$this->log( sprintf( __( 'Memory used: %s', 'advanced-cron-manager' ), $memory_usage_h ), 'performance' );

		$execution_time = ( microtime( true ) - $this->starting_time );

		$this->log( sprintf( __( 'Execution time: %s', 'advanced-cron-manager' ), $execution_time . ' s' ), 'performance' );

	}

}
