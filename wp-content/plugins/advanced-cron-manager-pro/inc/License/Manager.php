<?php
/**
 * License Manager class
 */

namespace underDEV\AdvancedCronManagerPRO\License;
use underDEV\Utils;

class Manager {

	/**
	 * License key
	 * @var string
	 */
	protected $license_key;

	/**
	 * Ajax class
	 * @var object
	 */
	private $ajax;

	/**
	 * Class constructor
	 */
	public function __construct( Utils\Ajax $ajax ) {

		$this->plugin_name        = 'advanced-cron-manager-pro';
		$this->plugin_store       = 'https://bracketspace.com';
		$this->license_transient  = 'acmpro_license';
		$this->license_key_option = 'acmpro_license_key';
		$this->ajax               = $ajax;

	}

	/**
	 * Checks license status
	 * @return mixed      object with license data or false if license key is not provided
	 */
	public function check_license() {

		$license_data = $this->get_license();

		if ( false === $license_data ) {

			$license_data = $this->call_store( 'check_license' );

			if ( ! empty( $license_data ) ) {

				$error_message = $this->check_license_error( $license_data );

				if ( $error_message ) {
					$errors[] = $error_message;
				} else if ( isset( $license_data->site_count ) && $license_data->site_count == 0 ) {
					// fix for remotely deactivated license
					$this->remove_license_key();
				} else {

					/*if ( $license_data->license != 'valid' ) {
						$license_data = $this->call_store( 'activate_license' );
					}*/

					$this->set_license( $license_data );

				}

			}

		}

		return $license_data;

	}

	/**
	 * Activate license via AJAX
	 * @return void
	 */
	public function ajax_activate() {

		$this->ajax->verify_nonce( 'acm/license/activate' );

		$errors = array();

		$license_key = trim( $_REQUEST['license'] );

		if ( empty( $license_key ) ) {
			$errors[] = __( 'Please provide license key', 'advanced-cron-manager' );
		}

		if ( empty( $errors ) ) {

			$this->set_license_key( $license_key );

			$license_data = $this->call_store( 'activate_license' );

			if ( empty( $license_data ) ) {
				$errors[] = __( 'License couldn\'t be activated', 'advanced-cron-manager' );
			} else {

				$error_message = $this->check_license_error( $license_data );

				if ( $error_message ) {
					$errors[] = $error_message;
					$this->call_store( 'deactivate_license' );
					$this->remove_license_key();
				} else {
					$this->set_license( $license_data );
				}

			}

		}

		$this->ajax->response( __( 'License has been activated', 'advanced-cron-manager' ), $errors );

	}

	/**
	 * Deactivate license  via AJAX
	 * @return void
	 */
	public function ajax_deactivate() {

		$this->ajax->verify_nonce( 'acm/license/deactivate' );

		$license_data = $this->call_store( 'deactivate_license' );

		if ( empty( $license_data ) ) {
			$errors[] = __( 'License couldn\'t be deactivated', 'advanced-cron-manager' );
		} else {
			$this->remove_license();
			$this->remove_license_key();
		}

		$this->ajax->response( __( 'License has been deactivated', 'advanced-cron-manager' ), $errors );

	}

	/**
	 * Checks for license error
	 * @param  object $license_data license data returned from API
	 * @return mixed                false if no errors or error message
	 */
	public function check_license_error( $license_data ) {

		$error = false;

		if ( isset( $license_data->error ) ) {

			switch ( $license_data->error ) {
				case 'expired':
					$error = sprintf( __( 'License expired since %s', 'advanced-cron-manager'), $license_data->expires );
					break;

				case 'missing':
					$error = __( 'Your license key couldn\'t be found', 'advanced-cron-manager' );
					break;

				case 'no_activations_left':
					$error = sprintf( __( 'You exceeded maximum amount of licensed sites (%s)', 'advanced-cron-manager'), $license_data->max_sites );
					break;

				case 'revoked':
					$error = __( 'Your license has been disabled', 'advanced-cron-manager' );
					break;

				default:
					$error = __( 'Please check if your license is correct', 'advanced-cron-manager' );
					break;
			}

		}

		return $error;

	}

	/**
	 * Checks if license is valid
	 * @return boolean true if still valid
	 */
	public function is_license_valid() {
		return $this->check_license() && $this->check_license()->license == 'valid';
	}

	/**
	 * Sets license key
	 * @return void
	 */
	public function set_license_key( $key ) {

		if ( ! empty( $key ) ) {
			update_option( $this->license_key_option, $key );
			$this->license_key = $key;
		}

	}

	/**
	 * Removes license key
	 * @return void
	 */
	public function remove_license_key() {

		delete_option( $this->license_key_option );
		$this->license_key = false;

	}

	/**
	 * Gets license key
	 * @return string license key
	 */
	public function get_license_key() {

		if ( empty( $this->license_key ) ) {
			$this->license_key = get_option( $this->license_key_option, '' );
		}

		return trim( $this->license_key );

	}

	/**
	 * Sets license transient
	 * @param object $license license object retrieved from EDD API
	 */
	private function set_license( $license ) {

		if ( $license->license == 'valid' ) {
			$transient_time = DAY_IN_SECONDS;
		} else {
			$transient_time = 600;
		}

		set_transient( $this->license_transient, $license, $transient_time );

	}

	/**
	 * Removes license transient
	 */
	private function remove_license() {

		delete_transient( $this->license_transient );

	}

	/**
	 * Gets license from transient
	 * @param mixed false or license object
	 */
	private function get_license() {

		return get_transient( $this->license_transient );

	}

	/**
	 * Calls the store
	 * @param  string $action action name
	 * @return mixed          false on error or response object
	 */
	public function call_store( $action = null ) {

		$license_key = $this->get_license_key();

		if ( empty( $license_key ) || empty( $action ) ) {
			return false;
		}

		$api_params = array(
			'edd_action' => $action,
			'license'    => $license_key,
			'item_name'  => urlencode( $this->plugin_name ),
			'url'        => home_url()
		);

		$response = wp_remote_post(
			$this->plugin_store,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

}
