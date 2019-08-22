<?php

	/**
	 * Fired during plugin activation
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 */

	/**
	 * Fired during plugin activation.
	 *
	 * This class defines all code necessary to run during the plugin's activation.
	 *
	 * @since      1.0.0
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXWPEmailLogger_Activator {

		/**
		 * Short Description. (use period)
		 *
		 * Long Description.
		 *
		 * @since    1.0.0
		 */
		public static function activate() {
			//check if can activate plugin
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}


			$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
			check_admin_referer( "activate-plugin_{$plugin}" );

			//create tables
			CBXWPEmailLogger_Activator::createTables();
		}//end method activate

		/**
		 * Create  necessary tables needed for 'cbxscratingreview'
		 */
		public static function createTables() {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			//tables
			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			//create rating log table
			$table_rating_log_sql = "CREATE TABLE $table_cbxwpemaillogger (
                          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,                             
                          date_created datetime NOT NULL COMMENT 'created date',                                                                              
                          subject varchar(255) NOT NULL DEFAULT '' COMMENT 'email subject',
                          email_data longtext NOT NULL DEFAULT '' COMMENT 'email body and header',
                          ip_address varchar(45) NOT NULL,                                                       
                          status tinyint(3) NOT NULL DEFAULT 1 COMMENT '1 means sent, 0 means failed',                                                                            
                          PRIMARY KEY (id)
                        ) $charset_collate; ";

			dbDelta( $table_rating_log_sql );
		}//end method createTables

	}//end method CBXWPEmailLogger_Activator
