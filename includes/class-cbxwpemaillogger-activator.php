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


			$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field($_REQUEST['plugin']) : '';
			check_admin_referer( "activate-plugin_{$plugin}" );

			//create tables
			CBXWPEmailLogger_Activator::createTables();

			$settings = new CBXWPEmailLoggerSettings();

			$delete_old_log = $settings->get_option( 'delete_old_log', 'cbxwpemaillogger_log', 'no' );

			if ( $delete_old_log == 'yes' ) {
				if ( ! wp_next_scheduled( 'cbxwpemaillogger_daily_event' ) ) {
					wp_schedule_event( time(), 'daily', 'cbxwpemaillogger_daily_event' );
				}
			}

			set_transient( 'cbxwpemaillogger_activated_notice', 1 );


		}//end method activate

		/**
		 * Create  necessary tables needed for 'cbxscratingreview'
		 */
		public static function createTables() {
			CBXWPEmailLoggerHelper::createTables();
		}//end method createTables

	}//end method CBXWPEmailLogger_Activator
