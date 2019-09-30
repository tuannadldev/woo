<?php
/**
 * LogDisplayer class
 */

namespace underDEV\AdvancedCronManagerPRO;
use underDEV\Utils;

class LogOptions {

	/**
	 * Database class
	 * @var object
	 */
	private $db;

	/**
	 * View class
	 * @var instance of underDEV\AdvancedCronManagePRO\Utils\View
	 */
	private $view;

	/**
	 * Ajax class
	 * @var instance of underDEV\AdvancedCronManagePRO\Utils\Ajax
	 */
	private $ajax;

	/**
	 * License Manager class
	 * @var object
	 */
	private $license_manager;

	/**
	 * Class constructor
	 */
	public function __construct( Database $db, Utils\View $view, Utils\Ajax $ajax, License\Manager $license_manager ) {

		$this->db              = $db;
		$this->view            = $view;
		$this->ajax            = $ajax;
		$this->license_manager = $license_manager;

		$this->option_name = 'acm_pro_log_options';

		$this->default = array(
			'log_executions'  => 0,
			'log_warnings'    => 0,
			'log_errors'      => 0,
			'log_performance' => 0,
			'display_section' => 0,
			'logs_number'     => 1000
		);

	}

	/**
	 * Loads log settings part
	 * There are used $this->view instead of passed instance
	 * because we want to separate scopes
	 * @param  object $view instance of parent view
	 * @return void
	 */
	public function load_log_settings_part( $view ) {

		if ( ! $this->license_manager->is_license_valid() ) {
			return;
		}

		$this->view->set_var( 'settings', $this->get_settings() );

		$this->view->get_view( 'log-settings' );

	}

	/**
	 * Saves settings
	 * Called by AJAX
	 * @return void
	 */
	public function save_settings() {

		$this->ajax->verify_nonce( 'acm/logs/settings/save' );

		$errors = array();

		$form_options = array_map( function( $val ) {
			return 0;
		}, $this->default );

		$form_data = wp_parse_args( $_REQUEST['data'], $form_options );

		update_option( $this->option_name, $form_data );

		$this->ajax->response( __( 'Settings has been saved', 'advanced-cron-manager' ), $errors );

	}

	/**
	 * Gets Settings
	 * Supports lazy loading
	 * @param  boolean $force if refresh stored events
	 * @return array          saved settings
	 */
	public function get_settings( $force = false ) {

		if ( empty( $this->settings ) || $force ) {
			$this->settings = get_option( $this->option_name, $this->default );
		}

		return $this->settings;

	}

	/**
	 * Check if particular option is active
	 * @param  string  $setting setting name
	 * @return mixed            false if not active, value if active
	 */
	public function is_active( $setting = '' ) {

		$settings = $this->get_settings();

		return isset( $settings[ $setting ] ) && ! empty( $settings[ $setting ] ) ? $settings[ $setting ] : false;

	}

}
