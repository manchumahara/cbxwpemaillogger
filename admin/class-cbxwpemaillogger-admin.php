<?php

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/admin
	 */

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/admin
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXWPEmailLogger_Admin {

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
		 * @param      string $plugin_name The name of this plugin.
		 * @param      string $version     The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version     = $version;

			//get plugin base file name
			$this->plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $plugin_name . '.php' );

			//get instance of setting api
			$this->settings_api = new CBXWPEmailLoggerSettings();
		}//end of constructor method


		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links.
		 *
		 * @return  array
		 */
		public static function plugin_action_links( $links ) {
			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=cbxwpemailloggersettings' ) . '" aria-label="' . esc_attr__( 'View settings', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Settings', 'cbxwpemaillogger' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		}//end method plugin_action_links

		/**
		 * Initialize setting
		 */
		public function admin_init() {
			//set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );
			//initialize settings
			$this->settings_api->admin_init();
		}//end method admin_init

		/**
		 * Global Setting Sections and titles
		 *
		 * @return type
		 */
		public function get_settings_sections() {
			return apply_filters( 'cbxwpemaillogger_setting_sections',
				array(
					array(
						'id'    => 'cbxwpemaillogger_log',
						'title' => esc_html__( 'Email Log', 'cbxwpemaillogger' ),
					),
					array(
						'id'    => 'cbxwpemaillogger_email',
						'title' => esc_html__( 'Email Control', 'cbxwpemaillogger' ),
					),
					array(
						'id'    => 'cbxwpemaillogger_smtps',
						'title' => esc_html__( 'Email Sending', 'cbxwpemaillogger' ),
					),
					array(
						'id'    => 'cbxwpemaillogger_tools',
						'title' => esc_html__( 'Tools', 'cbxwpemaillogger' ),
					),
				) );
		}//end method get_settings_sections

		/**
		 * Global Setting Fields
		 *
		 * @return array
		 */
		public function get_settings_fields() {
			global $wpdb;

			$table_names = CBXWPEmailLoggerHelper::getAllDBTablesList();
			$table_html  = '<p id="cbxwpemaillogger_plg_gfig_info"><strong>' . esc_html__( 'Following database tables will be reset/deleted.', 'cbxwpemaillogger' ) . '</strong></p>';

			$table_counter = 1;

			foreach ( $table_names as $key => $value ) {
				$table_html .= '<p>' . str_pad( $table_counter, 2, '0', STR_PAD_LEFT ) . '. ' . $wpdb->prefix . esc_html( $key ) . ' - (<code>' . esc_html( $value ) . '</code>)</p>';
				$table_counter ++;
			}

			$table_html .= '<p><strong>' . esc_html__( 'Following option values created by this plugin will be deleted from wordpress option table', 'cbxwpemaillogger' ) . '</strong></p>';


			$option_values = CBXWPEmailLoggerHelper::getAllOptionNames();

			$table_counter = 1;

			foreach ( $option_values as $key => $value ) {
				$table_html .= '<p>' . str_pad( $table_counter, 2, '0', STR_PAD_LEFT ) . '. ' . esc_html( $value['option_name'] ) . ' - ' . intval( $value['option_id'] ) . ' - (<code style="overflow-wrap: break-word; word-break: break-all;">' . $value['option_value'] . '</code>)</p>';

				$table_counter ++;
			}

			$custom_mailer = CBXWPEmailLoggerHelper::getCustomMailer();

			$smtp_email_servers_list = CBXWPEmailLoggerHelper::getSMTPHostServers( true );


			$settings_builtin_fields = array(
				'cbxwpemaillogger_log'   => array(
					'email_log_enable'        => array(
						'name'    => 'email_log_enable',
						'label'   => esc_html__( 'Email Log Control', 'cbxwpemaillogger' ),
						'desc'    => '<p>' . esc_html__( 'Control Email logging, default is enabled on after plugin activated.', 'cbxwpemaillogger' ) . '</p>',
						'type'    => 'radio',
						'options' => array(
							1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
							0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
						),
						'default' => 1,
					),
					'delete_old_log'          => array(
						'name'              => 'delete_old_log',
						'label'             => esc_html__( 'Delete Old email logs', 'cbxwpemaillogger' ),
						'desc'              => '<p>' . esc_html__( 'If enabled it will check everyday if there is any x days old emails. Number of days(x) is configured in next field. This plugin needs to deactivate and activate again to make this feature work.', 'cbxwpemaillogger' ) . '</p>',
						'type'              => 'radio',
						'options'           => array(
							'yes' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
							'no'  => esc_html__( 'No', 'cbxwpemaillogger' ),
						),
						'default'           => 'no',
						'sanitize_callback' => 'esc_html',
					),
					'log_old_days'            => array(
						'name'              => 'log_old_days',
						'label'             => esc_html__( 'Number of days', 'cbxwpemaillogger' ),
						'desc'              => '<p>' . esc_html__( 'Number of days email will be deleted as old based on email send date', 'cbxwpemaillogger' ) . '</p>',
						'type'              => 'text',
						'default'           => '30',
						'sanitize_callback' => 'absint',
					),
					'enable_store_attachment' => array(
						'name'    => 'enable_store_attachment',
						'label'   => esc_html__( 'Save Attachment Files', 'cbxwpemaillogger' ),
						'desc'    => '<p>' . esc_html__( 'If enabled attachments will be stored. If log deleted attachments will be deleted from the stored location. Sometimes attachment are sent from dynamically generated contents which is deleted from memory after email is sent, if not stored separately then email resend feature will not be able to attach email. This feature is default disabled.', 'cbxwpemaillogger' ) . '</p>',
						'type'    => 'radio',
						'options' => array(
							1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
							0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
						),
						'default' => 0,
					),
				),
				'cbxwpemaillogger_email' => array(
					'email_smtp_enable'     => array(
						'name'    => 'email_smtp_enable',
						'label'   => esc_html__( 'Control Email Sending', 'cbxwpemaillogger' ),
						'desc'    => '<p>' . __( 'Control email sending, default is disabled on after plugin activated. <strong>If disabled, this plugin will not touch any email sending feature.</strong>', 'cbxwpemaillogger' ) . '</p>',
						'type'    => 'radio',
						'options' => array(
							1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
							0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
						),
						'default' => 0,
					),
					'smtp_from_email'       => array(
						'name'              => 'smtp_from_email',
						'label'             => esc_html__( 'Override From Email', 'cbxwpemaillogger' ),
						'desc'              => '<p>' . esc_html__( 'Leave blank/empty to use default', 'cbxwpemaillogger' ) . '</p>',
						'type'              => 'text',
						'default'           => sanitize_email( get_option( 'admin_email' ) ),
						'sanitize_callback' => 'sanitize_email',
					),
					'smtp_from_name'        => array(
						'name'              => 'smtp_from_name',
						'label'             => esc_html__( 'Override From Name', 'cbxwpemaillogger' ),
						'desc'              => '<p>' . esc_html__( 'Leave blank/empty to use default', 'cbxwpemaillogger' ) . '</p>',
						'type'              => 'text',
						'default'           => esc_html( get_option( 'blogname' ) ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'smtp_email_returnpath' => array(
						'name'              => 'smtp_email_returnpath',
						'label'             => esc_html__( 'Email Return path', 'cbxwpemaillogger' ),
						'desc'              => '<p>' . esc_html__( 'If blank will ignore', 'cbxwpemaillogger' ) . '</p>',
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_email',
					),
					'mailer'                => array(
						'name'    => 'mailer',
						'label'   => esc_html__( 'Emailer', 'cbxwpemaillogger' ),
						'desc'    => '<p>' . esc_html__( 'Default is wordpress default', 'cbxwpemaillogger' ) . '</p>',
						'type'    => 'select',
						'default' => 'default',
						'options' => array(
							'default' => esc_html__( 'WordPress Default', 'cbxwpemaillogger' ),
							'custom'  => esc_html__( 'Custom Mailer(Choose from Email Sending Tab)', 'cbxwpemaillogger' ),
						),
					),

				),
				'cbxwpemaillogger_smtps' => array(
					'custom_mailer'      => array(
						'name'    => 'custom_mailer',
						'label'   => esc_html__( 'Choose Custom Mailer', 'cbxwpemaillogger' ),
						'type'    => 'select',
						'default' => 'custom_smtp',
						'options' => $custom_mailer
					),
					'smtp_email_servers' => array(
						'name'    => 'smtp_email_servers',
						'label'   => esc_html__( 'SMTP Host Servers', 'cbxwpemaillogger' ),
						'type'    => 'repeat',
						'default' => array(
							'0' => array(
								'smtp_email_enable'   => 1,
								'smtp_email_host'     => 'localhost',
								'smtp_email_port'     => '25',
								'smtp_email_secure'   => 'none',
								'smtp_email_auth'     => 0,
								'smtp_email_username' => '',
								'smtp_email_password' => '',
							),
							'1' => array(
								'smtp_email_enable'   => 0,
								'smtp_email_host'     => 'localhost',
								'smtp_email_port'     => '25',
								'smtp_email_secure'   => 'none',
								'smtp_email_auth'     => 0,
								'smtp_email_username' => '',
								'smtp_email_password' => '',
							),
							'2' => array(
								'smtp_email_enable'   => 0,
								'smtp_email_host'     => 'localhost',
								'smtp_email_port'     => '25',
								'smtp_email_secure'   => 'none',
								'smtp_email_auth'     => 0,
								'smtp_email_username' => '',
								'smtp_email_password' => '',
							),
							'3' => array(
								'smtp_email_enable'   => 0,
								'smtp_email_host'     => 'localhost',
								'smtp_email_port'     => '25',
								'smtp_email_secure'   => 'none',
								'smtp_email_auth'     => 0,
								'smtp_email_username' => '',
								'smtp_email_password' => '',
							),
							'4' => array(
								'smtp_email_enable'   => 0,
								'smtp_email_host'     => 'localhost',
								'smtp_email_port'     => '25',
								'smtp_email_secure'   => 'none',
								'smtp_email_auth'     => 0,
								'smtp_email_username' => '',
								'smtp_email_password' => '',
							),
						),
						'fields'  => array(
							'smtp_email_enable'   => array(
								'name'    => 'smtp_email_enable',
								'label'   => esc_html__( 'Enable Service', 'cbxwpemaillogger' ),
								'type'    => 'radio',
								'default' => 0,
								'options' => array(
									'1' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
									'0' => esc_html__( 'No', 'cbxwpemaillogger' )
								),
							),
							'smtp_email_host'     => array(
								'name'    => 'smtp_email_host',
								'label'   => esc_html__( 'SMTP Host', 'cbxwpemaillogger' ),
								'type'    => 'text',
								'default' => 'localhost',
							),
							'smtp_email_port'     => array(
								'name'              => 'smtp_email_port',
								'label'             => esc_html__( 'SMTP Port', 'cbxwpemaillogger' ),
								'type'              => 'text',
								'default'           => '25',
								'sanitize_callback' => 'absint',
							),
							'smtp_email_secure'   => array(
								'name'    => 'smtp_email_secure',
								'label'   => esc_html__( 'SMTP Secure', 'cbxwpemaillogger' ),
								'type'    => 'select',
								'default' => 'none',
								'options' => array(
									'none' => esc_html__( 'None(Port: 25)', 'cbxwpemaillogger' ),
									'ssl'  => esc_html__( 'SSL(Port: 465)', 'cbxwpemaillogger' ),
									'tls'  => esc_html__( 'TLS(Port: 465)', 'cbxwpemaillogger' ),
								),
							),
							'smtp_email_auth'     => array(
								'name'    => 'smtp_email_auth',
								'label'   => esc_html__( 'SMTP Authentication', 'cbxwpemaillogger' ),
								'type'    => 'radio',
								'default' => 0,
								'options' => array(
									0 => esc_html__( 'No', 'cbxwpemaillogger' ),
									1 => esc_html__( 'Yes', 'cbxwpemaillogger' ),
								),
							),
							'smtp_email_username' => array(
								'name'              => 'smtp_email_username',
								'label'             => esc_html__( 'SMTP Username', 'cbxwpemaillogger' ),
								'type'              => 'text',
								'default'           => '',
								'sanitize_callback' => 'sanitize_text_field',
							),
							'smtp_email_password' => array(
								'name'              => 'smtp_email_password',
								'label'             => esc_html__( 'SMTP Password', 'cbxwpemaillogger' ),
								'type'              => 'password',
								'default'           => '',
								'sanitize_callback' => 'sanitize_text_field',
							),

						)
					),
					'smtp_email_server'  => array(
						'name'    => 'smtp_email_server',
						'label'   => esc_html__( 'Choose SMTP Server', 'cbxwpemaillogger' ),
						'desc'    => esc_html__( 'List is showing only enabled servers', 'cbxwpemaillogger' ),
						'type'    => 'select',
						'default' => - 1,
						'options' => $smtp_email_servers_list
					),
					/*'smtp_email_host'     => array(
						'name'    => 'smtp_email_host',
						'label'   => esc_html__( 'SMTP Host', 'cbxwpemaillogger' ),
						'type'    => 'text',
						'default' => 'localhost',
					),
					'smtp_email_port'     => array(
						'name'              => 'smtp_email_port',
						'label'             => esc_html__( 'SMTP Port', 'cbxwpemaillogger' ),
						'type'              => 'text',
						'default'           => '25',
						'sanitize_callback' => 'absint',
					),
					'smtp_email_secure'   => array(
						'name'    => 'smtp_email_secure',
						'label'   => esc_html__( 'SMTP Secure', 'cbxwpemaillogger' ),
						'type'    => 'select',
						'default' => 'none',
						'options' => array(
							'none' => esc_html__( 'None(Port: 25)', 'cbxwpemaillogger' ),
							'ssl'  => esc_html__( 'SSL(Port: 465)', 'cbxwpemaillogger' ),
							'tls'  => esc_html__( 'TLS(Port: 465)', 'cbxwpemaillogger' ),
						),
					),
					'smtp_email_auth'     => array(
						'name'    => 'smtp_email_auth',
						'label'   => esc_html__( 'SMTP Authentication', 'cbxwpemaillogger' ),
						'type'    => 'radio',
						'default' => 0,
						'options' => array(
							0 => esc_html__( 'No', 'cbxwpemaillogger' ),
							1 => esc_html__( 'Yes', 'cbxwpemaillogger' ),
						),
					),
					'smtp_email_username' => array(
						'name'              => 'smtp_email_username',
						'label'             => esc_html__( 'SMTP Username', 'cbxwpemaillogger' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'smtp_email_password' => array(
						'name'              => 'smtp_email_password',
						'label'             => esc_html__( 'SMTP Password', 'cbxwpemaillogger' ),
						'type'              => 'password',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),*/
				),
				'cbxwpemaillogger_tools' => array(
					'delete_global_config' => array(
						'name'              => 'delete_global_config',
						'label'             => esc_html__( 'On Uninstall delete plugin data', 'cbxwpemaillogger' ),
						'desc'              => '<p>' . esc_html__( 'Delete Global Config data and custom table created by this plugin on uninstall.',
								'cbxwpemaillogger' ) . '</p>' . '<p>' . wp_kses( __( '<strong>Please note that this process can not be undone and it is recommended to keep full database backup before doing this.</strong>', 'cbxwpemaillogger' ), array( 'strong' => array() ) ) . '</p>' . $table_html,
						'type'              => 'radio',
						'options'           => array(
							'yes' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
							'no'  => esc_html__( 'No', 'cbxwpemaillogger' ),
						),
						'default'           => 'no',
						'sanitize_callback' => 'esc_html',
					),
				),
			);

			$settings_fields = array(); //final setting array that will be passed to different filters

			$sections = $this->get_settings_sections();


			foreach ( $sections as $section ) {
				if ( ! isset( $settings_builtin_fields[ $section['id'] ] ) ) {
					$settings_builtin_fields[ $section['id'] ] = array();
				}
			}

			foreach ( $sections as $section ) {
				$settings_fields[ $section['id'] ] = apply_filters( 'cbxwpemaillogger_global_' . esc_attr( $section['id'] ) . '_fields',
					$settings_builtin_fields[ $section['id'] ] );
			}

			$settings_fields = apply_filters( 'cbxwpemaillogger_global_fields', $settings_fields ); //final filter if need

			return $settings_fields;
		}//end method get_settings_fields

		/**
		 * Create admin menu's
		 */
		public function admin_pages() {

			$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

			//review listing page
			$email_logger_menu_hook = add_menu_page( esc_html__( 'CBX SMTP and Email Logger Dashboard', 'cbxwpemaillogger' ),
				esc_html__( 'CBX SMTP & Logs', 'cbxwpemaillogger' ),
				'manage_options',
				'cbxwpemaillogger',
				array( $this, 'display_cbxwpemaillogger_listing_page' ),
				'dashicons-email',
				'6' );

			//add screen option save option
			if ( $page == 'cbxwpemaillogger' ) {
				add_action( "load-$email_logger_menu_hook", array( $this, 'cbxwpemaillogger_review_listing' ) );
			}

			//add settings for this plugin
			$setting_page_hook = add_submenu_page( 'cbxwpemaillogger',
				esc_html__( 'Setting', 'cbxwpemaillogger' ),
				esc_html__( 'Setting', 'cbxwpemaillogger' ),
				'manage_options',
				'cbxwpemailloggersettings',
				array( $this, 'display_plugin_admin_settings' ) );


		}//end method admin_pages

		/**
		 * Admin listing menu callback
		 */
		public function display_cbxwpemaillogger_listing_page() {
			$view = isset( $_REQUEST['view'] ) ? esc_attr( wp_unslash( $_REQUEST['view'] ) ) : 'list';


			if ( $view == 'list' ) {
				include( cbxwpemaillogger_locate_template( 'admin/cbxwpemaillogger-logs.php' ) );
			} /*else if($view == 'body'){
				$log_id = isset($_REQUEST['log_id'])? intval($_REQUEST['log_id']) : 0;
				$item = CBXWPEmailLoggerHelper::SingleLog($log_id);
				include( cbxwpemaillogger_locate_template('admin/cbxwpemaillogger-body.php') );
			}*/
			else {
				$log_id = isset( $_REQUEST['log_id'] ) ? intval( $_REQUEST['log_id'] ) : 0;

				$item = CBXWPEmailLoggerHelper::SingleLog( $log_id );

				include( cbxwpemaillogger_locate_template( 'admin/cbxwpemaillogger-log.php' ) );
			}

		}//end method display_cbxwpemaillogger_listing_page

		/**
		 * Set options for log listing result
		 *
		 * @param $new_status
		 * @param $option
		 * @param $value
		 *
		 * @return mixed
		 */
		public function cbxscratingreview_listing_per_page( $new_status, $option, $value ) {
			if ( 'cbxwpemaillogger_listing_per_page' == $option ) {
				return $value;
			}

			return $new_status;
		}//end cbxscratingreview_listing_per_page

		/**
		 * Display settings
		 * @global type $wpdb
		 */
		public function display_plugin_admin_settings() {
			global $wpdb;

			$plugin_data = get_plugin_data( plugin_dir_path( __DIR__ ) . '/../' . $this->plugin_basename );

			include( cbxwpemaillogger_locate_template( 'admin/admin-settings-display.php' ) );
		}//end method display_plugin_admin_settings

		/**
		 * Add screen option for log listing
		 */
		public function cbxwpemaillogger_review_listing() {
			$option = 'per_page';

			$args = array(
				'label'   => esc_html__( 'Number of items per page', 'cbxwpemaillogger' ),
				'default' => 50,
				'option'  => 'cbxwpemaillogger_listing_per_page',
			);

			add_screen_option( $option, $args );
		}//end method cbxwpemaillogger_review_listing

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles( $hook ) {

			$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

			if ( $page == 'cbxwpemaillogger' ) {
				wp_register_style( 'ply', plugin_dir_url( __FILE__ ) . '../assets/js/ply/ply.css', array(), $this->version, 'all' );
				wp_register_style( 'flatpickr-min',
					plugin_dir_url( __FILE__ ) . '../assets/js/flatpickr/flatpickr.min.css',
					array(),
					$this->version );
				wp_register_style( 'cbxwpemaillogger', plugin_dir_url( __FILE__ ) . '../assets/css/cbxwpemaillogger-admin.css', array( 'ply', 'flatpickr-min' ), $this->version, 'all' );
				wp_enqueue_style( 'cbxwpemaillogger' );
			}

			if ( $page == 'cbxwpemailloggersettings' ) {
				wp_register_style( 'hideshowpassword', plugin_dir_url( __FILE__ ) . '../assets/js/hideshowpassword/example.wink.css', array(), $this->version );
				wp_register_style( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/css/select2.min.css', array(), $this->version );
				wp_register_style( 'cbxwpemaillogger-setting',
					plugin_dir_url( __FILE__ ) . '../assets/css/cbxwpemaillogger-setting.css',
					array( 'select2' ),
					$this->version );

				wp_enqueue_style( 'hideshowpassword' );
				wp_enqueue_style( 'select2' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'cbxwpemaillogger-setting' );
			}
		}//end method enqueue_styles

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts( $hook ) {
			$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

			if ( $page == 'cbxwpemaillogger' ) {
				wp_register_script( 'ply', plugin_dir_url( __FILE__ ) . '../assets/js/ply/ply.min.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'flatpickr',
					plugin_dir_url( __FILE__ ) . '../assets/js/flatpickr/flatpickr.min.js',
					array( 'jquery' ),
					$this->version,
					true );
				wp_register_script( 'cbxwpemaillogger', plugin_dir_url( __FILE__ ) . '../assets/js/cbxwpemaillogger-admin.js', array( 'jquery', 'ply', 'flatpickr' ), $this->version, true );

				//adding translation and other variables from php to js for single post edit screen
				$cbxwpemaillogger_js_vars = array(
					'search_placeholder'  => esc_html__( 'Search Term', 'cbxwpemaillogger' ),
					'upload_btn'          => esc_html__( 'Upload', 'cbxwpemaillogger' ),
					'upload_title'        => esc_html__( 'Select Media', 'cbxwpemaillogger' ),
					'delete'              => esc_html__( 'Delete', 'cbxwpemaillogger' ),
					'deleteconfirm'       => esc_html__( 'Are you sure to delete? On successful delete information will be lost forever.', 'cbxwpemaillogger' ),
					'resendconfirm'       => esc_html__( 'Are you sure to Resend? This action can not be reversed.', 'cbxwpemaillogger' ),
					'deleteconfirmok'     => esc_html__( 'Sure', 'cbxwpemaillogger' ),
					'deleteconfirmcancel' => esc_html__( 'Oh! No', 'cbxwpemaillogger' ),
					'ajaxurl'             => admin_url( 'admin-ajax.php' ),
					'nonce'               => wp_create_nonce( 'cbxwpemaillogger' ),
				);

				wp_localize_script( 'cbxwpemaillogger', 'cbxwpemaillogger_dashboard', apply_filters( 'cbxwpemaillogger_js_vars', $cbxwpemaillogger_js_vars ) );

				add_thickbox();

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'ply' );
				wp_enqueue_script( 'flatpickr' );
				wp_enqueue_script( 'cbxwpemaillogger' );
			}


			if ( $page == 'cbxwpemailloggersettings' ) {
				wp_register_script( 'hideshowpassword', plugin_dir_url( __FILE__ ) . '../assets/js/hideshowpassword/hideShowPassword.min.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/js/select2.min.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'cbxwpemaillogger-setting',
					plugin_dir_url( __FILE__ ) . '../assets/js/cbxwpemaillogger-setting.js',
					array(
						'jquery',
						'hideshowpassword',
						'select2',
						'wp-color-picker',
					),
					$this->version,
					true );

				$cbxwpemaillogger_setting_js_vars = apply_filters( 'cbxwpemaillogger_setting_js_vars',
					array(
						'please_select' => esc_html__( 'Please Select', 'cbxwpemaillogger' ),
						'upload_title'  => esc_html__( 'Select Media File', 'cbxwpemaillogger' ),
					) );
				wp_localize_script( 'cbxwpemaillogger-setting', 'cbxwpemaillogger_setting', $cbxwpemaillogger_setting_js_vars );

				wp_enqueue_script( 'jquery' );
				wp_enqueue_media();

				wp_enqueue_script( 'hideshowpassword' );
				wp_enqueue_script( 'select2' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'cbxwpemaillogger-setting' );
			}
		}//end method enqueue_scripts


		/**
		 * Insert email log into database
		 */
		public function insert_log( $atts ) {
			//$to, $subject, $message, $headers, $attachments


			$setting          = $this->settings_api;
			$email_log_enable = intval( $setting->get_option( 'email_log_enable', 'cbxwpemaillogger_log', 1 ) );


			if ( $email_log_enable == 0 ) {
				return $atts;
			}


			global $wpdb;
			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';


			$to = $atts['to'];
			if ( ! is_array( $to ) ) {
				$to = explode( ',', $to );
			}


			$subject = isset( $atts['subject'] ) ? wp_unslash( sanitize_text_field( $atts['subject'] ) ) : '';
			//$body    = isset( $atts['message'] ) ? wp_unslash(sanitize_textarea_field($atts['message'])) : ( isset( $atts['html'] ) ? wp_unslash(sanitize_textarea_field($atts['html'])) : '' );
			$body = isset( $atts['message'] ) ? wp_unslash( $atts['message'] ) : ( isset( $atts['html'] ) ? wp_unslash( $atts['html'] ) : '' );
			//$htm


			$headers     = isset( $atts['headers'] ) ? $atts['headers'] : array();
			$attachments = isset( $atts['attachments'] ) ? $atts['attachments'] : array();

			if ( ! is_array( $attachments ) ) {
				$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
			}


			if ( ! is_array( $headers ) ) {
				// Explode the headers out, so this function can take both
				// string headers and an array of headers.
				$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			}


			$attachments_store = array();

			if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
				foreach ( $attachments as $attachment ) {
					$file_name           = basename( $attachment );
					$attachments_store[] = $file_name;

				}
			}


			$email_data = array();

			$email_data['atts']        = $atts; //keep the blueprint
			$email_data['body']        = $body;
			$email_data['headers']     = $headers; //raw header data
			$email_data['attachments'] = $attachments_store; //raw attachment info data

			//parse header information
			$headers_arr = array();
			$cc          = $bcc = $reply_to = array();

			$email_source = '';


			if ( is_array( $headers ) && sizeof( $headers ) > 0 ) {
				foreach ( (array) $headers as $header ) {
					if ( strpos( $header, ':' ) === false ) {
						if ( false !== stripos( $header, 'boundary=' ) ) {
							$parts    = preg_split( '/boundary=/i', trim( $header ) );
							$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
						}
						continue;
					}
					// Explode them out
					list( $name, $content ) = explode( ':', trim( $header ), 2 );

					// Cleanup crew
					$name    = trim( $name );
					$content = trim( $content );


					switch ( strtolower( $name ) ) {
						case 'x-wpcf7-content-type':
							$email_source = 'contact-form-7';

							break;
						// Mainly for legacy -- process a From: header if it's there
						case 'from':
							$bracket_pos = strpos( $content, '<' );
							if ( $bracket_pos !== false ) {
								// Text before the bracketed email is the "From" name.
								if ( $bracket_pos > 0 ) {
									$from_name = substr( $content, 0, $bracket_pos - 1 );
									$from_name = str_replace( '"', '', $from_name );
									$from_name = trim( $from_name );
								}

								$from_email = substr( $content, $bracket_pos + 1 );
								$from_email = str_replace( '>', '', $from_email );
								$from_email = trim( $from_email );

								// Avoid setting an empty $from_email.
							} elseif ( '' !== trim( $content ) ) {
								$from_email = trim( $content );
							}
							break;
						case 'content-type':
							if ( strpos( $content, ';' ) !== false ) {
								list( $type, $charset_content ) = explode( ';', $content );
								$content_type = trim( $type );
								if ( false !== stripos( $charset_content, 'charset=' ) ) {
									$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
								} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
									$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
									$charset  = '';
								}

								// Avoid setting an empty $content_type.
							} elseif ( '' !== trim( $content ) ) {
								$content_type = trim( $content );
							}
							break;
						case 'cc':
							$cc = array_merge( (array) $cc, explode( ',', $content ) );
							break;
						case 'bcc':
							$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
							break;
						case 'reply-to':
							$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
							break;
						default:
							// Add it to our grand headers array
							$headers[ trim( $name ) ] = trim( $content );
							break;
					}
				}
			}

			//$email_data['headers_arr']  = $headers_arr;

			// From email and name
			// If we don't have a name from the input headers
			if ( ! isset( $from_name ) ) {
				$from_name = 'WordPress';
			}

			/* If we don't have an email from the input headers default to wordpress@$sitename
			 * Some hosts will block outgoing mail from this address if it doesn't exist but
			 * there's no easy alternative. Defaulting to admin_email might appear to be another
			 * option but some hosts may refuse to relay mail from an unknown domain. See
			 * https://core.trac.wordpress.org/ticket/5007.
			 */

			if ( ! isset( $from_email ) ) {
				// Get the site domain and get rid of www.
				$sitename = strtolower( $_SERVER['SERVER_NAME'] );
				if ( substr( $sitename, 0, 4 ) == 'www.' ) {
					$sitename = substr( $sitename, 4 );
				}

				$from_email = 'wordpress@' . $sitename;
			}

			/**
			 * Filters the email address to send from.
			 *
			 * @since 2.2.0
			 *
			 * @param string $from_email Email address to send from.
			 */
			$from_email = apply_filters( 'wp_mail_from', $from_email );

			/**
			 * Filters the name to associate with the "from" email address.
			 *
			 * @since 2.3.0
			 *
			 * @param string $from_name Name associated with the "from" email address.
			 */
			$from_name = apply_filters( 'wp_mail_from_name', $from_name );


			$address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

			foreach ( $address_headers as $address_header => $addresses ) {
				if ( empty( $addresses ) ) {
					continue;
				}

				foreach ( (array) $addresses as $address ) {

					// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
					$recipient_name = '';

					if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
						if ( count( $matches ) == 3 ) {
							$recipient_name = $matches[1];
							$address        = $matches[2];
						}
					}

					switch ( $address_header ) {
						case 'to':
							$headers_arr['email_to'][] = array( 'recipient_name' => $recipient_name, 'address' => $address );
							break;
						case 'cc':

							$headers_arr['email_cc'][] = array( 'recipient_name' => $recipient_name, 'address' => $address );
							break;
						case 'bcc':

							$headers_arr['email_bcc'][] = array( 'recipient_name' => $recipient_name, 'address' => $address );
							break;
						case 'reply_to':
							$headers_arr['email_reply_to'][] = array( 'recipient_name' => $recipient_name, 'address' => $address );
							break;
					}

				}
			}


			$headers_arr['email_from'] = array( 'from_name' => $from_name, 'from_email' => $from_email );
			$email_data['headers_arr'] = $headers_arr;


			$data = array(
				'date_created' => current_time( 'mysql' ),
				'subject'      => sanitize_text_field( $subject ),
				'email_data'   => maybe_serialize( $email_data ),
				'ip_address'   => CBXWPEmailLoggerHelper::get_ipaddress(),
				'src_tracked'  => sanitize_text_field( wp_unslash( $email_source ) ),
			);

			$data = apply_filters( 'cbxwpemaillogger_log_entry_data', $data );


			$data_format = array(
				'%s', // date_created
				'%s', // subject
				'%s', // email_data
				'%s', // ip_address
				'%s' // src_tracked
			);

			$data_format = apply_filters( 'cbxwpemaillogger_log_entry_data_format', $data_format );


			$log_insert_status = $wpdb->insert(
				$table_cbxwpemaillogger,
				$data,
				$data_format
			);

			if ( $log_insert_status != false ) {
				$log_id = $wpdb->insert_id;

				//we will set a new email header

				$enable_store_attachment = intval( $setting->get_option( 'enable_store_attachment', 'cbxwpemaillogger_log', 0 ) );
				if ( $enable_store_attachment && is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
					$this->store_email_attachments( $log_id, $attachments );
				}

				$headers_t = isset( $atts['headers'] ) ? $atts['headers'] : array();

				if ( empty( $headers_t ) ) {
					$headers_t = array();
				} else {
					if ( ! is_array( $headers_t ) ) {
						// Explode the headers out, so this function can take both
						// string headers and an array of headers.
						$headers_t = explode( "\n", str_replace( "\r\n", "\n", $headers_t ) );
					}
				}

				$headers_t[] = "x-cbxwpemaillogger-id: $log_id";


				$atts['headers'] = $headers_t;
			}


			return $atts;
		}//end method insert_log

		/**
		 * Store email attachment files
		 *
		 * @param int   $log_id
		 * @param array $attachments
		 */
		public function store_email_attachments( $log_id = 0, $attachments = array() ) {
			$log_id = intval( $log_id );
			if ( $log_id > 0 && is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
				$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

				global $wp_filesystem;
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();

				$log_folder_dir = $dir_info['cbxwpemaillogger_base_dir'] . $log_id;
				if ( ! $wp_filesystem->exists( $log_folder_dir ) ) {
					$created = wp_mkdir_p( $log_folder_dir );
					if ( $created ) {
						$folder_exists = 1;
					} else {
						$folder_exists = 0;
					}
				}

				foreach ( $attachments as $attachment ) {
					$file_name = basename( $attachment );

					$wp_filesystem->copy( $attachment, $log_folder_dir . '/' . $file_name, true );
				}
			}
		}//end method store_email_attachments

		/**
		 * Email log resend ajax handle
		 */
		public function email_resend() {
			check_ajax_referer( 'cbxwpemaillogger',
				'security' );

			//only logged in user and user who has option change capability can change this.
			if ( is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {

				$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

				if ( $id > 0 ) {

					global $wpdb;

					$item = CBXWPEmailLoggerHelper::SingleLog( $id );

					$email_data = maybe_unserialize( $item['email_data'] );

					$atts = isset( $email_data['atts'] ) ? $email_data['atts'] : array();


					if ( is_array( $atts ) && sizeof( $atts ) > 0 ) {

						list( $to, $subject, $message, $headers, $attachments ) = array_values( $atts );

						$attachments_t = array();
						if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
							$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

							global $wp_filesystem;
							require_once( ABSPATH . '/wp-admin/includes/file.php' );
							WP_Filesystem();

							$log_folder_dir = $dir_info['cbxwpemaillogger_base_dir'] . $id;

							foreach ( $attachments as $attachment ) {
								$file_name = basename( $attachment );

								if ( $wp_filesystem->exists( $log_folder_dir . '/' . $file_name ) ) {
									$attachments_t[] = $log_folder_dir . '/' . $file_name;
								}
							}

							$attachments = $attachments_t;
						}


						/*if(is_array($headers) && sizeof($headers) > 0){

						}
						else{
							if($headers == '') $headers = '';
						}*/

						$email_type = esc_attr( $item['email_type'] );
						set_transient( 'cbxwpemaillogger_resend_filter_mail_content_type', $email_type );

						add_filter( 'wp_mail_content_type', array( $this, 'resend_filter_mail_content_type' ) );
						$report = wp_mail( $to, $subject, $message, $headers, $attachments );
						remove_filter( 'wp_mail_content_type', array( $this, 'resend_filter_mail_content_type' ) );

						if ( $report ) {
							$return = array(
								'message' => esc_html__( 'Email ReSend successfully sent.', 'cbxwpemaillogger' ),
								'success' => 1,

							);
						} else {
							$return = array(
								'message' => esc_html__( 'Email ReSend but failed.', 'cbxwpemaillogger' ),
								'success' => 1,

							);
						}


						wp_send_json( $return );
					}

				}
			}//if user allowed

			$return = array(
				'message' => esc_html__( 'Failed to send or not enough access to send', 'cbxwpemaillogger' ),
				'success' => 0,

			);

			wp_send_json( $return );

		}//end method email_resend

		/**
		 * Send email same origin content type format while resending
		 *
		 * @param string $content_type
		 *
		 * @return mixed|string
		 */
		public function resend_filter_mail_content_type( $content_type = 'text/plain' ) {

			$email_type = get_transient( 'cbxwpemaillogger_resend_filter_mail_content_type' );
			if ( $email_type !== false ) {
				delete_transient( 'cbxwpemaillogger_resend_filter_mail_content_type' );

				return $email_type;
			}

			return $content_type;
		}//end method resend_filter_mail_content_type

		/**
		 * Download attachments
		 */
		public function download_attachment() {
			check_ajax_referer( 'cbxwpemaillogger', 'cbxwpemaillogger_nonce' );

			//only logged in user and user who has option change capability can change this.
			if ( is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {
				$log_id = isset( $_REQUEST['log_id'] ) ? absint( $_REQUEST['log_id'] ) : 0;
				$file   = isset( $_REQUEST['file'] ) ? wp_unslash( sanitize_text_field( $_REQUEST['file'] ) ) : '';

				if ( $log_id > 0 && $file != '' ) {

					$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();
					global $wp_filesystem;
					require_once( ABSPATH . '/wp-admin/includes/file.php' );
					WP_Filesystem();

					$file_path = $dir_info['cbxwpemaillogger_base_dir'] . $log_id . '/' . $file;

					if ( $wp_filesystem->exists( $file_path ) ) {

						// Prevent browsers from MIME-sniffing the content-type:
						header( 'X-Content-Type-Options: nosniff' );

						header( 'Content-Type: application/octet-stream' );
						header( 'Content-Disposition: attachment; filename="' . $file . '"' );
						header( 'Content-Length: ' . CBXWPEmailLoggerHelper::fix_integer_overflow( filesize( $file_path ) ) );
						header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s T', filemtime( $file_path ) ) );
						readfile( $file_path );
					}

				}
			}//if user loggedin and has permission to manage options

			die();

		}//end method download_attachment


		/**
		 * Email sent fail hook callback
		 *
		 * @param $wp_error
		 */
		public function email_sent_failed( $wp_error ) {

			$setting          = $this->settings_api;
			$email_log_enable = intval( $setting->get_option( 'email_log_enable', 'cbxwpemaillogger_log', 1 ) );

			if ( $email_log_enable == 0 ) {
				return;
			}

			if ( ! ( $wp_error instanceof \WP_Error ) ) {
				return;
			}

			$mail_error_data = $wp_error->get_error_data( 'wp_mail_failed' );
			$mail_error_message = sanitize_text_field(wp_unslash($wp_error->get_error_message()));



			$headers = isset( $mail_error_data['headers'] ) ? $mail_error_data['headers'] : array();

			if ( isset( $headers['x-cbxwpemaillogger-id'] ) && intval( $headers['x-cbxwpemaillogger-id'] ) > 0 ) {

				$log_id = intval( $headers['x-cbxwpemaillogger-id'] );


				//$code = isset($mail_error_data['phpmailer_exception_code'])? $mail_error_data['phpmailer_exception_code'] : '';

				global $wpdb;
				$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

				$wpdb->update(
					$table_cbxwpemaillogger,
					array(
						'status' => 0,
						'error_message' => $mail_error_message,
					),
					array( 'id' => intval( $log_id ) ),
					array(
						'%d',    // status
						'%s'    // status
					),
					array( '%d' )
				);
			}


		}//end method email_sent_failed

		/**
		 * Email log delete ajax handle
		 */
		public function email_log_delete() {
			check_ajax_referer( 'cbxwpemaillogger',
				'security' );

			if ( is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {
				$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

				if ( $id > 0 ) {
					global $wpdb;

					$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

					do_action( 'cbxwpemaillogger_log_delete_before', $id );

					$delete_status = $wpdb->query( $wpdb->prepare( "DELETE FROM $table_cbxwpemaillogger WHERE id=%d", $id ) );

					if ( $delete_status !== false ) {


						do_action( 'cbxwpemaillogger_log_delete_after', $id );

						$return = array(
							'message' => esc_html__( 'Email log successfully.',
								'cbxwpemaillogger' ),
							'success' => 1,

						);

						wp_send_json( $return );
					}

				}
			}

			$return = array(
				'message' => esc_html__( 'Failed to delete or not enough access to delete',
					'cbxwpemaillogger' ),
				'success' => 0,

			);

			wp_send_json( $return );

		}//end method email_log_delete

		/**
		 * Delete attachment folder after log delete
		 *
		 * @param int $id
		 */
		public function delete_attachments_after_log_delete( $id = 0 ) {

			$id = intval( $id );
			if ( $id > 0 ) {
				//delete attachment folder
				$delete_status = CBXWPEmailLoggerHelper::deleteLogFolder( $id );
			}

			return $delete_status;

		}//end method

		public function delete_attachments_folder() {

			$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

			if ( intval( $dir_info['folder_exists'] ) == 1 ) {
				$cbxwpemaillogger_base_dir = $dir_info['cbxwpemaillogger_base_dir'];

				global $wp_filesystem;
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();


				$status = $wp_filesystem->delete( $cbxwpemaillogger_base_dir, true, 'd' );
			}

		}//end method delete_attachments_folder


		/**
		 * Delete old from scheduled event
		 */
		public function delete_old_log() {

			$settings = new CBXWPEmailLoggerSettings();

			$delete_old_log = $settings->get_option( 'delete_old_log', 'cbxwpemaillogger_log', 'no' );

			if ( $delete_old_log == 'yes' ) {

				$log_old_days = intval( $settings->get_option( 'log_old_days', 'cbxwpemaillogger_log', '30' ) );

				if ( $log_old_days > 0 ) {

					CBXWPEmailLoggerHelper::delete_old_log( $log_old_days );
				}
			}

		}//end method delete_old_log

		/**
		 * Override from email address
		 *
		 * @param $original_email_address
		 *
		 * @return string
		 */
		public function wp_mail_from_custom( $original_email_address ) {
			$setting = $this->settings_api;

			$email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'cbxwpemaillogger_email', 0 ) );
			$smtp_from_email   = sanitize_email( $setting->get_option( 'smtp_from_email', 'cbxwpemaillogger_email', sanitize_email( get_option( 'admin_email' ) ) ) );


			if ( $email_smtp_enable && $smtp_from_email != '' ) {
				$original_email_address = $smtp_from_email;
			}

			return $original_email_address;
		}//end method wp_mail_from_custom

		/**
		 * Override from email name
		 *
		 * @param $original_email_address
		 *
		 * @return string
		 */
		public function wp_mail_from_name_custom( $original_email_name ) {
			$setting = $this->settings_api;

			$email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'cbxwpemaillogger_email', 0 ) );

			$smtp_from_name = sanitize_text_field( $setting->get_option( 'smtp_from_name', 'cbxwpemaillogger_email', sanitize_text_field( get_option( 'blogname' ) ) ) );

			if ( $email_smtp_enable && $smtp_from_name != '' ) {
				$original_email_name = $smtp_from_name;
			}

			return $original_email_name;
		}//end method wp_mail_from_name_custom

		public function phpmailer_init_extend( $phpmailer ) {
			global $wpdb;

			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			$content_type = $phpmailer->ContentType;

			$custom_headers = $phpmailer->getCustomHeaders();
			if ( is_array( $custom_headers ) && sizeof( $custom_headers ) > 0 ) {
				foreach ( $custom_headers as $custom_header ) {
					if ( is_array( $custom_header ) && isset( $custom_header[0] ) && esc_attr( $custom_header[0] ) == 'x-cbxwpemaillogger-id' ) {
						$insert_id = isset( $custom_header[1] ) ? intval( $custom_header[1] ) : 0;
						if ( $insert_id > 0 ) {
							$log_update_status = $wpdb->update(
								$table_cbxwpemaillogger,
								array( 'email_type' => esc_attr( $content_type ) ),
								array( 'id' => $insert_id ),
								array( '%s' ),
								array( '%d' )
							);
						}
						break;
					}
				}
			}//end email type update

			$setting = $this->settings_api;

			$email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'cbxwpemaillogger_email', 0 ) );


			if ( $email_smtp_enable ) {
				$smtp_email_returnpath = sanitize_email( $setting->get_option( 'smtp_email_returnpath', 'cbxwpemaillogger_email', '' ) );
				$mailer                = esc_attr( sanitize_text_field( $setting->get_option( 'mailer', 'cbxwpemaillogger_email', 'default' ) ) );



				if ( $smtp_email_returnpath != '' ) {
					$phpmailer->AddCustomHeader( 'Return-Path: ' . $smtp_email_returnpath );
					$phpmailer->Sender = $smtp_email_returnpath;
				}

				if ( $mailer == 'custom' ) {
					//if custom emailer then we need to choose which emailer we can use

					$custom_mailer = esc_attr( sanitize_text_field( $setting->get_option( 'custom_mailer', 'cbxwpemaillogger_smtps', 'custom_smtp' ) ) );



					if ( $custom_mailer == 'custom_smtp' ) {

						$smtp_email_server = intval( $setting->get_option( 'smtp_email_server', 'cbxwpemaillogger_smtps', - 1 ) );

						$smtp_email_servers_list = CBXWPEmailLoggerHelper::getSMTPHostServers( true );



						if ( is_array( $smtp_email_servers_list ) && sizeof( $smtp_email_servers_list ) > 0 && isset( $smtp_email_servers_list[ $smtp_email_server ] ) ) {

							$smtp_config = CBXWPEmailLoggerHelper::getSMTPHostServer($smtp_email_server);



							$phpmailer->Mailer = "smtp";

							$host   = isset($smtp_config['smtp_email_host'])? sanitize_text_field($smtp_config['smtp_email_host']): 'localhost';
							$port   = isset($smtp_config['smtp_email_port'])? intval($smtp_config['smtp_email_port']): 25;

							$secure   = isset($smtp_config['smtp_email_secure'])? esc_attr(sanitize_text_field($smtp_config['smtp_email_secure'])): 'none' ;
							if ( $secure == 'none' ) {
								$secure = '';
							}

							$auth   = isset($smtp_config['smtp_email_auth'])? intval($smtp_config['smtp_email_auth']): 0;

							$username   = isset($smtp_config['smtp_email_username'])? sanitize_text_field($smtp_config['smtp_email_username']): '';
							$password   = isset($smtp_config['smtp_email_password'])? sanitize_text_field($smtp_config['smtp_email_password']): '';

							//$phpmailer->From = $this->wsOptions["from"];
							//$phpmailer->FromName = $this->wsOptions["fromname"];
							//$phpmailer->Sender = $phpmailer->From; //Return-Path
							//$phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName); //Reply-To

							$phpmailer->Host       = $host;
							$phpmailer->Port       = $port;
							$phpmailer->SMTPSecure = $secure;
							$phpmailer->SMTPAuth   = ( $auth ) ? true : false;

							if ( $phpmailer->SMTPAuth ) {
								$phpmailer->Username = $username;
								$phpmailer->Password = $password;
							}

						}


					}


				}
			}

		}//end method phpmailer_init_extend


		/**
		 * If we need to do something in upgrader process is completed for poll plugin
		 *
		 * @param $upgrader_object
		 * @param $options
		 */
		public function plugin_upgrader_process_complete( $upgrader_object, $options ) {
			if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
				foreach ( $options['plugins'] as $each_plugin ) {
					if ( $each_plugin == CBXWPEMAILLOGGER_BASE_NAME ) {
						CBXWPEmailLoggerHelper::createTables();

						set_transient( 'cbxwpemaillogger_upgraded_notice', 1 );
					}
				}
			}

		}//end method plugin_upgrader_process_complete

		/**
		 * Show a notice to anyone who has just installed the plugin for the first time
		 * This notice shouldn't display to anyone who has just updated this plugin
		 */
		public function plugin_activate_upgrade_notices() {
			// Check the transient to see if we've just activated the plugin
			if ( get_transient( 'cbxwpemaillogger_activated_notice' ) ) {
				echo '<div class="notice notice-success is-dismissible">';
				echo '<p>' . sprintf( __( 'Thanks for installing/deactivating <strong>CBX Email SMTP & Logger</strong> V%s - <a href="%s" target="_blank">Codeboxr Team</a>', 'cbxwpemaillogger' ), CBXWPEMAILLOGGER_PLUGIN_VERSION, 'https://codeboxr.com' ) . '</p>';
				echo '<p>' . sprintf( __( 'Check Plugin <a href="%s">Setting</a> and <a href="%s" target="_blank">Documentation</a>', 'cbxwpemaillogger' ), admin_url('admin.php?page=cbxwpemailloggersettings'), 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' ) . '</p>';
				echo '</div>';




				// Delete the transient so we don't keep displaying the activation message
				delete_transient( 'cbxwpemaillogger_activated_notice' );

				//$this->plugin_compatibility_check();
			}

			// Check the transient to see if we've just activated the plugin
			if ( get_transient( 'cbxwpemaillogger_upgraded_notice' ) ) {

				echo '<p>' . sprintf( __( 'Thanks for upgrading <strong>CBX Email SMTP & Logger</strong> V%s - <a href="%s" target="_blank">Codeboxr Team</a>', 'cbxwpemaillogger' ), CBXWPEMAILLOGGER_PLUGIN_VERSION, 'https://codeboxr.com' ) . '</p>';
				echo '<p>' . sprintf( __( 'Check Plugin <a href="%s">Setting</a> and <a href="%s" target="_blank">Documentation</a>', 'cbxwpemaillogger' ), admin_url('admin.php?page=cbxwpemailloggersettings'), 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' ) . '</p>';

				// Delete the transient so we don't keep displaying the activation message
				delete_transient( 'cbxwpemaillogger_upgraded_notice' );

				//$this->plugin_compatibility_check();
			}
		}//end method plugin_activate_upgrade_notices

		/**
		 * Check plugin compatibility
		 */
		public function plugin_compatibility_check() {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

		}//end method plugin_compatibility_check


	}//end class CBXWPEmailLogger_Admin
