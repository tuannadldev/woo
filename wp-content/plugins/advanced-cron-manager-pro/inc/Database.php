<?php
/**
 * Database class
 */

namespace underDEV\AdvancedCronManagerPRO;

class Database {

	/**
	 * Version of database tables
	 * @var int
	 */
	private $db_version = 8;

	/**
	 * Instance of WPDB Class
	 * @var object
	 */
	protected $wpdb;

	/**
	 * Class constructor
	 */
	public function __construct() {

		global $wpdb;

		$this->wpdb            = $wpdb;
		$this->cron_logs_table = $wpdb->prefix . 'acm_cron_logs';

	}

	/**
	 * Install tables
	 * @return boolean result
	 */
	public function install() {

		if ( ! $this->check_if_update() ) {
			return false;
		}

		$charset_collate = '';

		if ( ! empty( $this->wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$this->wpdb->charset}";
		}

		if ( ! empty( $this->wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$this->wpdb->collate}";
		}

		$sql = "
		CREATE TABLE {$this->cron_logs_table} (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			hook text NOT NULL,
			schedule text NOT NULL,
			args text NOT NULL,
			logged_time bigint(20) NOT NULL,
			diff float NOT NULL,
			errors text NOT NULL,
			UNIQUE KEY ID (ID)
		) $charset_collate;
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

		update_option( 'acm_pro_db_version', $this->db_version );

		return true;

	}

	/**
	 * Check if update database
	 * @return boolean true if update needed
	 */
	public function check_if_update() {

		$current_version = get_option( 'acm_pro_db_version' );

		return $current_version === false || $current_version < $this->db_version;

	}

	/**
	 * Inserts log into database table
	 * @param  array $data array of data which needs to be saved
	 * @return mixed       false on failure or numers of inserted rows
	 */
	public function insert_log( $data ) {

		$data = wp_parse_args( $data, array(
			'hook'        => '',
			'schedule'    => '',
			'args'        => '',
			'logged_time' => time(),
			'diff'        => 0,
			'errors'      => '',
		) );

		return $this->wpdb->insert(
			$this->cron_logs_table,
			$data,
			array( '%s', '%s', '%s', '%d', '%f', '%s' )
		);

	}

	/**
	 * Gets logs from database
	 * @param  array   $where    where clause array
	 * @param  integer $page     page number
	 * @param  integer $per_page how many logs per page
	 * @return array
	 */
	public function get_logs( $where = array(), $page = 1, $per_page = 10 ) {

		if ( empty( $where ) || ! is_array( $where ) ) {
			$where_clause = 1;
		} else {

			$where_clauses = array();

			foreach ( $where as $key => $value ) {
				$where_clauses[] = $this->wpdb->prepare( "{$key}=%s", $value );
			}

			$where_clause = implode( ' AND ', $where_clauses );

		}

		if ( $page == 1 ) {
			$offset = 0;
		} else {
			$offset = ( $page -1 ) * $per_page;
		}

		$logs = $this->wpdb->get_results( $this->wpdb->prepare(
			"SELECT * FROM {$this->cron_logs_table}
			WHERE {$where_clause}
			ORDER BY `logged_time` DESC
			LIMIT %d,%d",
			$offset,
			$per_page
		) );

		return $logs;

	}

	/**
	 * Counts logs number
	 * @param  array   $where    where clause array
	 * @return integer
	 */
	public function count( $where = array() ) {

		if ( empty( $where ) || ! is_array( $where ) ) {
			$where_clause = 1;
		} else {

			$where_clauses = array();

			foreach ( $where as $key => $value ) {
				$where_clauses[] = $this->wpdb->prepare( "{$key}=%s", $value );
			}

			$where_clause = implode( ' AND ', $where_clauses );

		}

		return $this->wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->cron_logs_table} WHERE {$where_clause}"
		);

	}

	/**
	 * Clears olders logs
	 * @param  integer $count how many logs should be removed
	 * @return void
	 */
	public function clear_last_logs( $count = 0 ) {

		if ( $count == 0 ) {
			return;
		}

		$this->wpdb->query( $this->wpdb->prepare(
			"DELETE FROM {$this->cron_logs_table}
			ORDER BY logged_time ASC
			LIMIT %d",
			$count
		) );

	}



}
