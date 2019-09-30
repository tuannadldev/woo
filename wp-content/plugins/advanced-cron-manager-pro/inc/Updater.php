<?php

namespace underDEV\AdvancedCronManagerPRO;

class Updater {

	/**
	 * Class constructor
	 */
	public function __construct( $plugin_version, $plugin_file, License\Manager $manager ) {

		$this->plugin_version = $plugin_version;
		$this->plugin_file    = $plugin_file;
		$this->manager        = $manager;

	}

	/**
	 * Check if there are any update for the plugin
	 * @return void
	 */
	public function update() {

		if ( ! $this->manager->is_license_valid() ) {
			return;
		}

		new \EDD_SL_Plugin_Updater( $this->manager->plugin_store, $this->plugin_file, array(
			'version' 	=> $this->plugin_version,
			'license' 	=> $this->manager->get_license_key(),
			'item_name' => $this->manager->plugin_name,
			'author' 	=> 'Kuba Mikita'
		) );

	}

	/**
	 * Upgrade plugin data
	 * Will not perform if acm_pro_upgraded_v1 option is set
	 * @return void
	 */
	public function upgrade() {

		$upgrade_already_done = get_option( 'acm_pro_upgraded_v1' );

		if ( $upgrade_already_done ) {
			return;
		}

		$this->upgrade_v1_license_settings();
		$this->upgrade_v1_settings();
		$this->upgrade_v1_logs_table();

		update_option( 'acm_pro_upgraded_v1', true );

		// cleanup
		$this->v1_cleanup();

	}

	/**
	 * Upgrade license settings
	 * @return void
	 */
	public function upgrade_v1_license_settings() {

		$old_options = get_option( 'acm_settings' );

		if ( empty( $old_options ) ) {
			return;
		}

		if ( isset( $old_options['license'] ) && ! empty( $old_options['license'] ) ) {
			update_option( 'acmpro_license_key', $old_options['license'] );
		}

	}

	/**
	 * Upgrade plugin settings stored in acm_settings option
	 * @return void
	 */
	public function upgrade_v1_settings() {

		$old_options = get_option( 'acm_settings' );

		if ( empty( $old_options ) ) {
			return;
		}

		$defaults = array(
			'log_executions'  => 0,
			'log_warnings'    => 0,
			'log_errors'      => 0,
			'log_performance' => 0,
			'display_section' => 0,
			'logs_number'     => 1000
		);

		$upgraded_options = wp_parse_args( array(
			'log_executions'  => $old_options['log'],
			'logs_number'     => $old_options['keep_in_db']
		), $defaults );

		update_option( 'acm_pro_log_options', $upgraded_options );

	}

	/**
	 * Upgrade plugin logs table
	 * @return void
	 */
	public function upgrade_v1_logs_table() {

		global $wpdb;

		$tablename = $wpdb->prefix . 'acm_cron_logs';

		// process data

		$logs = $wpdb->get_results(
			"SELECT * FROM {$tablename}
			WHERE 1
			ORDER BY `logged_time` DESC"
		);

		if ( empty( $logs ) ) {
			return;
		}

		foreach ( $logs as $key => $log ) {

			if ( ! empty( $log->args ) ) {
				$logs[ $key ]->args = serialize( explode( '<br>', $log->args ) );
			}

			if ( ! empty( $log->errors ) ) {
				$logs[ $key ]->errors = serialize( array(
					array( 'error' => $log->errors )
				) );
			}

		}

		// update

		foreach ( $logs as $key => $log ) {

			$wpdb->update(
				$tablename,
				array(
					'args'   => $log->args,
					'errors' => $log->errors,
				),
				array( 'ID' => $log->ID ),
				array(
					'%s',
					'%s'
				),
				array( '%d' )
			);

		}

	}

	/**
	 * Cleans up obsolete things from v1
	 * @return void
	 */
	public function v1_cleanup() {

		delete_option( 'acm_settings' );

	}

}
