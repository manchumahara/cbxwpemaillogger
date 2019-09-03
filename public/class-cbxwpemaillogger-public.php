<?php

	/**
	 * The public-facing functionality of the plugin.
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/public
	 */

	/**
	 * The public-facing functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the public-facing stylesheet and JavaScript.
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/public
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXWPEmailLogger_Public {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $version The current version of this plugin.
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 *
		 * @param      string $plugin_name The name of the plugin.
		 * @param      string $version     The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version     = $version;
		}

		/**
		 * Ajax email template viewer
		 */
		public function email_log_body() {
			if ( isset( $_REQUEST['action'] ) && esc_attr( sanitize_text_field($_REQUEST['action']) ) == 'cbxwpemaillogger_log_body' && is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {
				if ( ! wp_verify_nonce( wp_unslash($_REQUEST['_wpnonce']), 'cbxwpemaillogger' ) ) {
					// This nonce is not valid.
					die( 'Security check' );
				} else {
					$id = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : 0;

					$item = CBXWPEmailLoggerHelper::SingleLog( $id );

					$email_data = maybe_unserialize( $item['email_data'] );
					$body       = isset( $email_data['body'] ) ? $email_data['body'] : '';

					echo $body;

				}

				die();
			}
		}//end method email_log_body

	}//end class CBXWPEmailLogger_Public
