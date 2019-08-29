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
						'id'    => 'cbxwpemaillogger_general',
						'title' => esc_html__( 'General Setting', 'cbxwpemaillogger' ),
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
				$table_html .= '<p>' . str_pad( $table_counter, 2, '0', STR_PAD_LEFT ) . '. ' . $wpdb->prefix . $key . ' - (<code>' . $value . '</code>)</p>';
				$table_counter ++;
			}

			$table_html .= '<p><strong>' . esc_html__( 'Following option values created by this plugin will be deleted from wordpress option table',
					'cbxwpemaillogger' ) . '</strong></p>';


			$option_values = CBXWPEmailLoggerHelper::getAllOptionNames();

			$table_counter = 1;

			foreach ( $option_values as $key => $value ) {
				$table_html .= '<p>' . str_pad( $table_counter, 2, '0', STR_PAD_LEFT ) . '. ' . $value['option_name'] . ' - ' . $value['option_id'] . ' - (<code style="overflow-wrap: break-word; word-break: break-all;">' . $value['option_value'] . '</code>)</p>';

				$table_counter ++;
			}

			$settings_builtin_fields = array(
				'cbxwpemaillogger_general' => array(
					'delete_old_log' => array(
						'name'    => 'delete_old_log',
						'label'   => esc_html__( 'Delete Old email logs', 'cbxwpemaillogger' ),
						'desc'    => '<p>' . esc_html__( 'If enabled it will check everyday if there is any x days old emails. Number of days(x) is configured in next field. This plugin needs to deactivate and activate again to make this feature work.', 'cbxwpemaillogger' ) . '</p>',
						'type'    => 'radio',
						'options' => array(
							'yes' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
							'no'  => esc_html__( 'No', 'cbxwpemaillogger' ),
						),
						'default' => 'no',
					),
					'log_old_days'   => array(
						'name'    => 'log_old_days',
						'label'   => esc_html__( 'Number of days', 'cbxwpemaillogger' ),
						'desc'    => '<p>' . esc_html__( 'Number of days email will be deleted as old based on email send date', 'cbxwpemaillogger' ) . '</p>',
						'type'    => 'text',
						'default' => '30',
					),
				),
				'cbxwpemaillogger_tools'   => array(
					'delete_global_config' => array(
						'name'    => 'delete_global_config',
						'label'   => esc_html__( 'On Uninstall delete plugin data', 'cbxwpemaillogger' ),
						'desc'    => '<p>' . __( 'Delete Global Config data and custom table created by this plugin on uninstall.',
								'cbxwpemaillogger' ) . '</p>' . '<p>' . __( '<strong>Please note that this process can not be undone and it is recommended to keep full database backup before doing this.</strong>',
								'cbxwpemaillogger' ) . '</p>' . $table_html,
						'type'    => 'radio',
						'options' => array(
							'yes' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
							'no'  => esc_html__( 'No', 'cbxwpemaillogger' ),
						),
						'default' => 'no',
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
				$settings_fields[ $section['id'] ] = apply_filters( 'cbxwpemaillogger_global_' . $section['id'] . '_fields',
					$settings_builtin_fields[ $section['id'] ] );
			}

			$settings_fields = apply_filters( 'cbxwpemaillogger_global_fields', $settings_fields ); //final filter if need

			return $settings_fields;
		}//end method get_settings_fields

		/**
		 * Create admin menu's
		 */
		public function admin_pages() {

			//review listing page
			$email_logger_menu_hook = add_menu_page( esc_html__( 'CBX Email Logger Dashboard', 'cbxwpemaillogger' ), esc_html__( 'CBX Email Logs', 'cbxwpemaillogger' ), 'manage_options', 'cbxwpemaillogger',
				array( $this, 'display_cbxwpemaillogger_listing_page' ), 'dashicons-email', '6' );

			//add screen option save option
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'cbxwpemaillogger' ) {
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
			$view = isset( $_REQUEST['view'] ) ? esc_attr( $_REQUEST['view'] ) : 'list';


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
		}

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
				'option'  => 'cbxwpemaillogger_listing_per_page'
			);

			add_screen_option( $option, $args );
		}//end method cbxwpemaillogger_review_listing

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles( $hook ) {

			$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

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
				wp_register_style( 'select2',
					plugin_dir_url( __FILE__ ) . '../assets/js/select2/css/select2.min.css',
					array(),
					$this->version );
				wp_register_style( 'cbxwpemaillogger-setting',
					plugin_dir_url( __FILE__ ) . '../assets/css/cbxwpemaillogger-setting.css',
					array( 'select2' ),
					$this->version );

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
			$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

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
					'upload_title'        => esc_html__( 'Window Title', 'cbxwpemaillogger' ),
					'delete'              => esc_html__( 'Delete', 'cbxwpemaillogger' ),
					'deleteconfirm'       => esc_html__( 'Are you sure to delete? On successful delete information will be lost forever.', 'cbxwpemaillogger' ),
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
				wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/js/select2.min.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'cbxwpemaillogger-setting',
					plugin_dir_url( __FILE__ ) . '../assets/js/cbxwpemaillogger-setting.js',
					array(
						'jquery',
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

			global $wpdb;
			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';


			$to = $atts['to'];
			if ( ! is_array( $to ) ) {
				$to = explode( ',', $to );
			}


			$subject = isset( $atts['subject'] ) ? $atts['subject'] : '';
			$body    = isset( $atts['message'] ) ? $atts['message'] : ( isset( $atts['html'] ) ? $atts['html'] : '' );
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


			$email_data = array();

			$email_data['atts']        = $atts; //keep the blueprint
			$email_data['body']        = $body;
			$email_data['headers']     = $headers; //raw header data
			$email_data['attachments'] = $attachments; //raw attachment info data

			//parse header information
			$headers_arr = array();
			$cc          = $bcc = $reply_to = array();


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
				'ip_address'   => CBXWPEmailLoggerHelper::get_ipaddress()
			);

			$data = apply_filters( 'cbxwpemaillogger_log_entry_data', $data );

			$data_format = array(
				'%s', // date_created
				'%s', // subject
				'%s', // attachment
				'%s', // email_data
				'%s' // email_data
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

				//write_log('added header: x-cbxwpemaillogger-id');
				//write_log($headers);
				$atts['headers'] = $headers_t;
			}


			return $atts;
		}//end method insert_log


		/**
		 * Email sent fail hook callback
		 *
		 * @param $wp_error
		 */
		public function email_sent_failed( $wp_error ) {
			if ( ! ( $wp_error instanceof \WP_Error ) ) {
				return;
			}

			$mail_error_data = $wp_error->get_error_data( 'wp_mail_failed' );


			$headers = isset( $mail_error_data['headers'] ) ? $mail_error_data['headers'] : array();

			if ( isset( $headers['x-cbxwpemaillogger-id'] ) && intval( $headers['x-cbxwpemaillogger-id'] ) > 0 ) {
				$log_id = intval( $headers['x-cbxwpemaillogger-id'] );


				//$code = isset($mail_error_data['phpmailer_exception_code'])? $mail_error_data['phpmailer_exception_code'] : '';

				global $wpdb;
				$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

				$wpdb->update(
					$table_cbxwpemaillogger,
					array(
						'status' => 0
					),
					array( 'id' => intval( $log_id ) ),
					array(
						'%d'    // status
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
						do_action( 'cbxwpemaillogger_log_delete_after' );

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
		 * Email log delete ajax handle
		 */
		public function email_resend() {
			check_ajax_referer( 'cbxwpemaillogger',
				'security' );

			if ( is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {
				$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

				if ( $id > 0 ) {
					global $wpdb;


					$item = CBXWPEmailLoggerHelper::SingleLog( $id );

					$email_data = maybe_unserialize( $item['email_data'] );

					$atts = isset( $email_data['atts'] ) ? $email_data['atts'] : array();


					if ( is_array( $atts ) && sizeof( $atts ) > 0 ) {

						list( $to, $subject, $message, $headers, $attachments ) = array_values( $atts );

						$report = wp_mail( $to, $subject, $message, $headers, $attachments );

						if ( $report ) {
							$return = array(
								'message' => esc_html__( 'Email ReSend and Successfully sent.',
									'cbxwpemaillogger' ),
								'success' => 1,

							);
						} else {
							$return = array(
								'message' => esc_html__( 'Email ReSend but failed.',
									'cbxwpemaillogger' ),
								'success' => 1,

							);
						}


						wp_send_json( $return );
					}

				}
			}

			$return = array(
				'message' => esc_html__( 'Failed to send or not enough access to send',
					'cbxwpemaillogger' ),
				'success' => 0,

			);

			wp_send_json( $return );

		}//end method email_resend

		/**
		 * Delete old from scheduled event
		 */
		public function delete_old_log() {

			$settings = new CBXWPEmailLoggerSettings();

			$delete_old_log = $settings->get_option( 'delete_old_log', 'cbxwpemaillogger_general', 'no' );

			if ( $delete_old_log == 'yes' ) {

				$log_old_days = intval( $settings->get_option( 'log_old_days', 'cbxwpemaillogger_general', '30' ) );

				if ( $log_old_days > 0 ) {

					CBXWPEmailLoggerHelper::delete_old_log( $log_old_days );
				}
			}

		}//end method delete_old_log

	}//end class CBXWPEmailLogger_Admin
