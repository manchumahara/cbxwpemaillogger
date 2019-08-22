<?php

	/**
	 * Define the internationalization functionality
	 *
	 * Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 */

	/**
	 * Define the internationalization functionality.
	 *
	 * Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * @since      1.0.0
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXWPEmailLogger_i18n {


		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain(
				'cbxwpemaillogger',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);

		}


	}
