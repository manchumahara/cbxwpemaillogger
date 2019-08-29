<?php

	/**
	 * Fired during plugin deactivation
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 */

	/**
	 * Fired during plugin deactivation.
	 *
	 * This class defines all code necessary to run during the plugin's deactivation.
	 *
	 * @since      1.0.0
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXWPEmailLogger_Deactivator {

		/**
		 * Short Description. (use period)
		 *
		 * Long Description.
		 *
		 * @since    1.0.0
		 */
		public static function deactivate() {
			wp_clear_scheduled_hook( 'cbxwpemaillogger_daily_event' );
		}

	}//end class CBXWPEmailLogger_Deactivator
