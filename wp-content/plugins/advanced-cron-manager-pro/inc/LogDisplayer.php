<?php
/**
 * LogDisplayer class
 */

namespace underDEV\AdvancedCronManagerPRO;
use underDEV\Utils;
use underDEV\AdvancedCronManager\Cron\Events;

class LogDisplayer {

	/**
	 * View class
	 * @var object
	 */
	public $view;

	/**
	 * Ajax class
	 * @var object
	 */
	public $ajax;

	/**
	 * LogsLibrary class
	 * @var object
	 */
	private $library;

	/**
	 * Events class
	 * @var object
	 */
	private $events;

	/**
	 * LogOptions class
	 * @var object
	 */
	private $options;

	/**
	 * Class constructor
	 */
	public function __construct( Utils\View $view, Utils\Ajax $ajax, LogsLibrary $library, Events $events, LogOptions $options ) {

		$this->view    = $view;
		$this->ajax    = $ajax;
		$this->library = $library;
		$this->events  = $events;
		$this->options = $options;

	}

	/**
	 * Displays tab
	 * @param  object $table_row_view View object
	 * @return void
	 */
	public function display_tab( $table_row_view ) {

		$event = $table_row_view->get_var( 'event' );

		$this->view->set_var( 'event_hash', $event->hash, true );
		$this->view->set_var( 'logs', $this->library->get_event_logs( $event ), true );
		$this->view->set_var( 'total_logs', $this->library->count_logs( $event ), true );
		$this->view->set_var( 'logs_library', $this->library, true );

		$this->view->get_view( 'logs-table' );

	}

	/**
	 * Displays implementation info
	 * @param  object $table_row_view View object
	 * @return void
	 */
	public function display_implementation( $table_row_view ) {

		$this->view->get_view( 'implementation-info' );

	}

	/**
	 * Displays section
	 * @param  object $table_row_view View object
	 * @return void
	 */
	public function display_section( $table_row_view ) {

		if ( ! $this->options->is_active( 'display_section' ) ) {
			return;
		}

		$this->view->set_var( 'extended', true );
		$this->view->set_var( 'logs', $this->library->get_logs() );
		$this->view->set_var( 'total_logs', $this->library->count_logs() );
		$this->view->set_var( 'logs_library', $this->library );

		$this->view->get_view( 'logs-section' );

	}

	/**
	 * Refreshed logs table for event
	 * Called via AJAX
	 * @return void
	 */
	public function refresh_logs() {

		ob_start();

		if ( ! empty( $_REQUEST['event'] ) ) {

			$event = $this->events->get_event_by_hash( $_REQUEST['event'] );
			$this->view->set_var( 'logs', $this->library->get_event_logs( $event ) );

		} else {

			$this->view->set_var( 'logs', $this->library->get_logs() );
			$this->view->set_var( 'extended', true );

		}

		$this->view->set_var( 'logs_library', $this->library );

		$this->view->get_view( 'logs-table' );

		$this->ajax->success( ob_get_clean() );

	}

	/**
	 * Loads more logs
	 * Called via AJAX
	 * @return void
	 */
	public function load_more() {

		ob_start();

		if ( ! empty( $_REQUEST['event'] ) ) {

			$event = $this->events->get_event_by_hash( $_REQUEST['event'] );
			$this->view->set_var( 'logs', $this->library->get_event_logs( $event, $_REQUEST['page'] ) );

		} else {

			$this->view->set_var( 'logs', $this->library->get_logs( $_REQUEST['page'] ) );
			$this->view->set_var( 'extended', true );

		}

		$this->view->set_var( 'logs_library', $this->library );

		$this->view->get_view( 'logs-rows' );

		$this->ajax->success( ob_get_clean() );

	}

}
