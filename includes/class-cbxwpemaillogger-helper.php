<?php
	/**
	 * The file that defines the plugin helper class
	 *
	 * A class contains static method for quick usages
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 */

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}
?>
<?php

	/**
	 * Helper class with static methods
	 *
	 * Class CBXWPEmailLoggerHelper
	 */
	class CBXWPEmailLoggerHelper {

		/**
		 * Get all  core tables list
		 */
		public static function getAllDBTablesList() {
			global $wpdb;

			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			$table_names                     = array();
			$table_names['cbxwpemaillogger'] = $table_cbxwpemaillogger;


			return apply_filters( 'cbxwpemaillogger_table_list', $table_names );
		}//end method getAllDBTablesList

		/**
		 * List all global option name with prefix cbxuseronline_
		 */
		public static function getAllOptionNames() {
			global $wpdb;

			$prefix       = 'cbxwpemaillogger_';
			$option_names = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'", ARRAY_A );

			return apply_filters( 'cbxwpemaillogger_option_names', $option_names );
		}//end method getAllOptionNames

		/**
		 * Get IP address
		 *
		 * @return string|void
		 */
		public static function get_ipaddress() {

			if ( empty( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {

				$ip_address = $_SERVER["REMOTE_ADDR"];
			} else {

				$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}

			if ( strpos( $ip_address, ',' ) !== false ) {

				$ip_address = explode( ',', $ip_address );
				$ip_address = $ip_address[0];
			}

			return esc_attr( $ip_address );
		}//end method get_ipaddress

		/**
		 * Returns bulk actions
		 *
		 * @return array
		 */
		public static function StatusOptions() {
			$status_arr = array();

			$status_arr['delete'] = esc_html__( 'Delete', 'cbxwpemaillogger' );

			return $status_arr;
		}//end method StatusOptions

		/**
		 * Readable timestamp
		 *
		 * @param $timestamp
		 *
		 * @return false|string
		 */
		public static function DateReadableFormat( $timestamp, $format = 'M j, Y' ) {
			$format = ( $format == '' ) ? 'M j, Y' : $format;

			return date( $format, strtotime( $timestamp ) );
		}//end method dateReadableFormat

		/**
		 * Get Single review by review id
		 *
		 * @param int $post_id
		 * @param int $user_id
		 *
		 * @return null|string
		 */
		public static function SingleLog( $log_id = 0 ) {
			global $wpdb;
			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			$log_id = intval( $log_id );

			$log = null;
			if ( $log_id > 0 ) {

				$where_sql = $sql_select = '';
				$where_sql = $wpdb->prepare( "log.id=%d", $log_id );

				$sql_select = "SELECT log.* FROM $table_cbxwpemaillogger AS log";

				$log = $wpdb->get_row( "$sql_select WHERE $where_sql ", 'ARRAY_A' );
			}

			return $log;
		}//end method SingleLog


		/**
		 * Delete $log_old_days days old log
		 *
		 * @param int $log_old_days
		 */
		public static function delete_old_log( $log_old_days = 30 ) {

			global $wpdb;
			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			$sql_select = "SELECT log.* FROM $table_cbxwpemaillogger AS log";

			$logs = $wpdb->get_results( "$sql_select WHERE log.date_created <= NOW() - INTERVAL $log_old_days DAY", 'ARRAY_A' );

			if ( is_array( $logs ) && sizeof( $logs ) > 0 ) {
				foreach ( $logs as $log ) {
					$id = intval( $log['id'] );

					do_action( 'cbxwpemaillogger_log_delete_before', $id );

					$delete_status = $wpdb->query( $wpdb->prepare( "DELETE FROM $table_cbxwpemaillogger WHERE id=%d", $id ) );

					if ( $delete_status !== false ) {
						do_action( 'cbxwpemaillogger_log_delete_after' );
					}
				}
			}
		}//end method delete_old_log
	}//end class CBXWPEmailLoggerHelper