<?php
/**
 * Assets class
 * Loads plugin assets
 */

namespace underDEV\AdvancedCronManagerPRO;
use underDEV\Utils;
use underDEV\AdvancedCronManager\AdminScreen;

class Assets {

	/**
	 * Current plugin version
	 * @var string
	 */
	public $plugin_version;

	/**
	 * Files class
	 * @var object
	 */
	public $files;

	public function __construct( $version, Utils\Files $files ) {

		$this->plugin_version = $version;
		$this->files          = $files;

	}

	/**
	 * Enqueue admin scripts
	 * @return void
	 */
	public function enqueue_admin( $current_page_hook ) {

		wp_enqueue_style( 'advanced-cron-manager-pro', $this->files->asset_url( 'css', 'style.css' ), array( 'advanced-cron-manager' ), $this->plugin_version );
		wp_enqueue_script( 'advanced-cron-manager-pro', $this->files->asset_url( 'js', 'scripts.min.js' ), array( 'advanced-cron-manager' ), $this->plugin_version, true );

		wp_localize_script( 'advanced-cron-manager-pro', 'advanced_cron_manager_pro', array(
			'i18n' => array(
				'saving'       => __( 'Saving...', 'advanced-cron-manager' ),
				'deactivating' => __( 'Deactivating...', 'advanced-cron-manager' ),
				'activated'    => __( 'License activated', 'advanced-cron-manager' ),
				'deactivated'  => __( 'License deactivated', 'advanced-cron-manager' ),
				'loading'      => __( 'Loading...', 'advanced-cron-manager' ),
			)
		) );

	}

}
