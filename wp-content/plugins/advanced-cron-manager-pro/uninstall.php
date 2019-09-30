<?php
/**
 * Uninstall file
 * Called when plugin is uninstalled
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_transient( 'acmpro_license' );
delete_option( 'acmpro_license_key' );
delete_option( 'acm_pro_db_version' );
delete_option( 'acm_pro_log_options' );
delete_option( 'acm_pro_upgraded_v1' );

global $wpdb;
$wpdb->query(
	$wpdb->prepare(
		"DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'acm_cron_logs'
	)
);
