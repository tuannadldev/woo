<?php
/**
 * License Manager class
 */

namespace underDEV\AdvancedCronManagerPRO\License;
use underDEV\Utils;

class Settings {

	/**
	 * View class
	 * @var object
	 */
	private $view;

	/**
	 * License Manager class
	 * @var object
	 */
	private $manager;

	/**
	 * Class constructor
	 */
	public function __construct( Utils\View $view, Manager $manager ) {

		$this->view    = $view;
		$this->manager = $manager;

	}

	/**
	 * Loads license settings part
	 * There are used $this->view instead of passed instance
	 * because we want to separate scopes
	 * @param  object $view instance of parent view
	 * @return void
	 */
	public function load_license_settings_part() {

		$license = $this->manager->check_license();

		if ( $license == false ) {
			$status         = 'not_activated';
			$status_message = __( 'License not activated', 'advanced-cron-manager' );
			$button_label   = __( 'Activate license', 'advanced-cron-manager' );
		} else {

			$status         = $license->license;
			$button_label   = __( 'Activate license', 'advanced-cron-manager' );

			switch ( $license->license ) {
				case 'valid':
					$status_message = sprintf( __( 'License active. Expires %s', 'advanced-cron-manager'), $license->expires );
					$button_label   = __( 'Deactivate license', 'advanced-cron-manager' );
					break;

				case 'invalid':
					$status_message = __( 'Invalid license key', 'advanced-cron-manager' );
					break;

				case 'expired':
					$status_message = sprintf( __( 'License expired since %s', 'advanced-cron-manager'), $license->expires );
					break;

				case 'disabled':
					$status_message = __( 'Your license has been disabled', 'advanced-cron-manager' );
					break;

				default:
					$status_message = __( 'Undefined license status', 'advanced-cron-manager' );
					break;
			}

		}

		$this->view->set_var( 'license', $license );
		$this->view->set_var( 'license_key', $this->manager->get_license_key() );
		$this->view->set_var( 'status', $status );
		$this->view->set_var( 'button_label', $button_label );

		$this->view->set_var( 'status_message', $status_message );

		if ( $status == 'valid' ) {
			$action = 'deactivate';
		} else {
			$action = 'activate';
		}

		$this->view->set_var( 'action', $action );
		$this->view->set_var( 'nonce', wp_create_nonce( 'acm/license/' . $action ) );

		$this->view->get_view( 'license-settings' );

	}

}
