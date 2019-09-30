<?php
/**
 * LogsLibrary class
 */

namespace underDEV\AdvancedCronManagerPRO;

class LogsLibrary {

	/**
	 * Database class
	 * @var object
	 */
	private $db;

	/**
	 * LogOptions class
	 * @var object
	 */
	private $options;

	/**
	 * Class constructor
	 */
	public function __construct( Database $db, LogOptions $options ) {

		$this->db      = $db;
		$this->options = $options;

		$this->per_page = 10;

	}

	/**
	 * Saves log in the database
	 * Also cleans logs according to the limit
	 * @param  object $event Event object
	 * @param  array  $logs  logs
	 * @return mixed         false or 1
	 */
	public function save_log( $event, $logs ) {

		$result = $this->db->insert_log( array(
			'hook'     => $event->hook,
			'schedule' => $event->schedule,
			'args'     => serialize( $event->args ),
			'diff'     => microtime( true ) - $event->next_call,
			'errors'   => serialize( $logs )
		) );

		$this->maybe_clean();

		return $result;

	}

	/**
	 * Gets logs for particular Event
	 * @param  object $event Event object
	 * @param  int    $page  page number
	 * @return mixed         array of logs or null
	 */
	public function get_event_logs( $event, $page = 1 ) {

		if ( ! $event instanceof \underDEV\AdvancedCronManager\Cron\Object\Event ) {
			return null;
		}

		return $this->db->get_logs( array(
			'hook'     => $event->hook,
			'schedule' => $event->schedule,
			'args'     => serialize( $event->args )
		), $page, $this->per_page );

	}

	/**
	 * Gets all logs
	 * @param  int    $page  page number
	 * @return mixed         array of logs or null
	 */
	public function get_logs( $page = 1 ) {

		return $this->db->get_logs( 1, $page, $this->per_page );

	}

	/**
	 * Counts logs, for event or all
	 * @param  object $event Event object or null to count all logs
	 * @return int           number of logs
	 */
	public function count_logs( $event = null ) {

		if ( $event instanceof \underDEV\AdvancedCronManager\Cron\Object\Event ) {
			$where =  array(
				'hook'     => $event->hook,
				'schedule' => $event->schedule,
				'args'     => serialize( $event->args )
			);
		} else {
			$where = 1;
		}

		return $this->db->count( $where );

	}

	/**
	 * Check if logs has to be cleaned
	 * @return void
	 */
	public function maybe_clean() {

		$log_limit = $this->options->is_active( 'logs_number' );

		if ( ! $log_limit ) {
			return;
		}

		$total_logs = $this->count_logs();

		if ( $total_logs > $log_limit ) {
			$this->db->clear_last_logs( $total_logs - $log_limit );
		}

	}

	/**
	 * Translates messages from serialized array to nice, sorted array
	 * @param  string $messages serialized array
	 * @return array            array
	 */
	public function translate_messages( $messages ) {

		$return = array();

		if ( empty( $messages ) ) {
			return $return;
		}

		foreach ( unserialize( $messages ) as $message ) {

			foreach ( $message as $type => $thing ) {

				if ( ! isset( $return[ $type ] ) ) {
					$return[ $type ] = array();
				}

				if ( is_serialized( $thing ) ) {
					$value = '<pre>' . var_export( unserialize( $thing ), true ) . '</pre>';
				} else {
					$value = esc_html( $thing );
				}

				$return[ $type ][] = $value;

			}

		}

		return $return;

	}

}
