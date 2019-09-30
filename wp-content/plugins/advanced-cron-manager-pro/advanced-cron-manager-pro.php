<?php
/**
 * Plugin Name: Advanced Cron Manager PRO
 * Description: Log cron execution times, errors and performance
 * Version: 2.3.6
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * License: GPL3
 * Text Domain: advanced-cron-manager-pro
 */

/**
 * Fire up Composer's autoloader
 */
require_once( 'vendor/autoload.php' );

$requirements = new underDEV_Requirements( __( 'Advanced Cron Manager PRO', 'advanced-cron-manager' ), array(
	'php'         => '5.4',
	'wp'          => '3.6',
	'plugins'     => array(
		'advanced-cron-manager/advanced-cron-manager.php' => array(
			'name' => 'Advanced Cron Manager',
			'version' => '2.2'
		),
	),
	'old_plugins' => array(
		'advanced-cron-manager/acm.php' => array(
			'name' => 'Advanced Cron Manager',
			'version' => '2.0'
		),
	)
) );

/**
 * Check if old plugins are active
 * @param  array $plugins array with plugins,
 *                        where key is the plugin file and value is the version
 * @return void
 */
function acm_pro_check_old_plugins( $plugins, $r ) {

	foreach ( $plugins as $plugin_file => $plugin_data ) {

		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			continue;
		}

		$plugin_api_data = @get_file_data( WP_PLUGIN_DIR . '/' . $plugin_file , array( 'Version' ) );

		if ( ! isset( $plugin_api_data[0] ) ) {
			continue;
		}

		$old_plugin_version = $plugin_api_data[0];

		if ( ! empty( $old_plugin_version ) && version_compare( $old_plugin_version, $plugin_data['version'], '<' ) ) {
			$r->add_error( sprintf( '%s plugin at least in version %s', $plugin_data['name'], $plugin_data['version'] ) );
		}

	}

}

if ( method_exists( $requirements, 'add_check' ) ) {
	$requirements->add_check( 'old_plugins', 'acm_pro_check_old_plugins' );
}

if ( ! $requirements->satisfied() ) {

	add_action( 'admin_notices', array( $requirements, 'notice' ) );
	return;

}

/**
 * Require EDD Plugin updater
 */
if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	require_once( 'inc/License/EDD_SL_Plugin_Updater.php' );
}

add_action( 'plugins_loaded', 'advanced_cron_manager_pro_bootstrap' );

function advanced_cron_manager_pro_bootstrap() {

	$plugin_version = '2.3.6';
	$plugin_file    = __FILE__;

	/**
	 * Instances and Closures
	 */

	$files = new underDEV\Utils\Files( $plugin_file );

	$view = function() use ( $files ) {
		return new underDEV\Utils\View( $files );
	};

	$ajax = function() {
		return new underDEV\Utils\Ajax;
	};

	$schedules_library = new underDEV\AdvancedCronManager\Cron\SchedulesLibrary( $ajax() );

	$schedules = function() use ( $schedules_library ) {
		return new underDEV\AdvancedCronManager\Cron\Schedules( $schedules_library );
	};

	$events = function() use ( $schedules ) {
		return new underDEV\AdvancedCronManager\Cron\Events( $schedules() );
	};

	$assets = new underDEV\AdvancedCronManagerPRO\Assets( $plugin_version, $files );

	$integration_notification = function() use ( $view ) {
		return new underDEV\AdvancedCronManagerPRO\Integration\Notification( $view() );
	};

	$license_manager = function() use ( $ajax ) {
		return new underDEV\AdvancedCronManagerPRO\License\Manager( $ajax() );
	};

	$license_settings = function() use ( $view, $license_manager ) {
		return new underDEV\AdvancedCronManagerPRO\License\Settings( $view(), $license_manager() );
	};

	$updater = new underDEV\AdvancedCronManagerPRO\Updater( $plugin_version, $plugin_file, $license_manager() );

	$database = function() {
		return new underDEV\AdvancedCronManagerPRO\Database();
	};

	$log_options = function() use ( $database, $view, $ajax, $license_manager ) {
		return new underDEV\AdvancedCronManagerPRO\LogOptions( $database(), $view(), $ajax(), $license_manager() );
	};

	$logs_library = function() use ( $database, $log_options ) {
		return new underDEV\AdvancedCronManagerPRO\LogsLibrary( $database(), $log_options() );
	};

	$logger = function() use ( $events, $logs_library, $license_manager, $log_options ) {
		return new underDEV\AdvancedCronManagerPRO\Logger( $events(), $logs_library(), $license_manager(), $log_options() );
	};

	$log_displayer = function() use ( $view, $ajax, $logs_library, $events, $log_options ) {
		return new underDEV\AdvancedCronManagerPRO\LogDisplayer( $view(), $ajax(), $logs_library(), $events(), $log_options() );
	};

	/**
	 * Actions
	 */

	// Check if plugin needs upgrading
	add_action( 'advanced-cron-manager/screen/enqueue', array( $updater, 'upgrade' ), 10, 1 );

	// Check for updates
	add_filter( 'admin_init', array( $updater, 'update' ) );

	// Install database tables
	add_filter( 'plugins_loaded', array( $database(), 'install' ), 20 );

	// Add scripts
	add_action( 'advanced-cron-manager/screen/enqueue', array( $assets, 'enqueue_admin' ), 10, 1 );

	// Disable default logs tab
	add_filter( 'advanced-cron-manager/screen/event/details/tabs/logs/display', '__return_false', 10, 1 );

	// Display logs tab
	add_action( 'advanced-cron-manager/screen/event/details/tab/logs', array( $log_displayer(), 'display_tab' ), 10, 1 );

	// Add info in implementation tab
	add_action( 'advanced-cron-manager/screen/event/details/tab/implementation', array( $log_displayer(), 'display_implementation' ), 20, 1 );

	// Add logs section
	add_action( 'advanced-cron-manager/screen/main', array( $log_displayer(), 'display_section' ), 30, 1 );

	// Reload logs on ajax
	add_action( 'wp_ajax_acm/logs/refresh', array( $log_displayer(), 'refresh_logs' ) );

	// Load more logs
	add_action( 'wp_ajax_acm/logs/load_more', array( $log_displayer(), 'load_more' ) );

	// Add sidebar section parts on the admin screen
	add_action( 'advanced-cron-manager/screen/sidebar', array( $license_settings(), 'load_license_settings_part' ), 20, 1 );
	add_action( 'advanced-cron-manager/screen/sidebar', array( $log_options(), 'load_log_settings_part' ), 30, 1 );

	// License actions
	add_action( 'wp_ajax_acm/license/activate', array( $license_manager(), 'ajax_activate' ) );
	add_action( 'wp_ajax_acm/license/deactivate', array( $license_manager(), 'ajax_deactivate' ) );

	// Log options
	add_action( 'wp_ajax_acm/logs/settings/save', array( $log_options(), 'save_settings' ) );

	// Add actions observer
	add_filter( 'plugins_loaded', array( $logger(), 'add_actions' ), 20 );

	// Integration with Notification plugin
	add_action( 'init', function() {
		if ( function_exists( 'notification_runtime' ) ) {
			register_trigger( new underDEV\AdvancedCronManagerPRO\Integration\CronErrorTrigger() );
			register_trigger( new underDEV\AdvancedCronManagerPRO\Integration\EventUnscheduledTrigger() );
			register_trigger( new underDEV\AdvancedCronManagerPRO\Integration\EventScheduledTrigger() );
		}
	} );

}
