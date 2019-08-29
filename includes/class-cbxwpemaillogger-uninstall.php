<?php

	/**
	 * Fired during plugin deactivation
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.6
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 */

	/**
	 * Fired during plugin deactivation.
	 *
	 * This class defines all code necessary to run during the plugin's deactivation.
	 *
	 * @since      1.0.6
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 * @author     codeboxr <info@codeboxr.com>
	 */
	class CBXWPEmailLogger_Uninstall {

		/**
		 * Method for uninstall hook
		 *
		 * Long Description.
		 *
		 * @since    1.0.6
		 */
		public static function uninstall() {
			global $wpdb;

			$settings = new CBXWPEmailLoggerSettings();

			$delete_global_config = $settings->get_option( 'delete_global_config', 'cbxwpemaillogger_tools', 'no' );

			if ( $delete_global_config == 'yes' ) {
				$option_prefix = 'cbxwpemaillogger_';

				//delete plugin global options
				$option_values = CBXWPEmailLoggerHelper::getAllOptionNames();

				foreach ( $option_values as $option_value ) {
					delete_option( $option_value['option_name'] );
				}

				//delete tables created by this plugin

				$table_names  = CBXWPEmailLoggerHelper::getAllDBTablesList();
				$sql          = "DROP TABLE IF EXISTS " . implode( ', ', array_values( $table_names ) );
				$query_result = $wpdb->query( $sql );


				do_action( 'cbxwpemaillogger_plugin_uninstall', $table_names, $option_prefix );

			}//if enabled delete
		}//end method uninstall
	}//end class CBXWPEmailLogger_Uninstall
