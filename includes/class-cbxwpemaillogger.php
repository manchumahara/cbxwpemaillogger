<?php

	/**
	 * The file that defines the core plugin class
	 *
	 * A class definition that includes attributes and functions used across both the
	 * public-facing side of the site and the admin area.
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 */

	/**
	 * The core plugin class.
	 *
	 * This is used to define internationalization, admin-specific hooks, and
	 * public-facing site hooks.
	 *
	 * Also maintains the unique identifier of this plugin as well as the current
	 * version of the plugin.
	 *
	 * @since      1.0.0
	 * @package    CBXWPEmailLogger
	 * @subpackage CBXWPEmailLogger/includes
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXWPEmailLogger {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      CBXWPEmailLogger_Loader $loader Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $plugin_name The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $version The current version of the plugin.
		 */
		protected $version;

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->version     = CBXWPEMAILLOGGER_PLUGIN_VERSION;
			$this->plugin_name = CBXWPEMAILLOGGER_PLUGIN_NAME;

			$this->load_dependencies();
			$this->set_locale();
			$this->define_admin_hooks();
			$this->define_public_hooks();

		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - CBXWPEmailLogger_Loader. Orchestrates the hooks of the plugin.
		 * - CBXWPEmailLogger_i18n. Defines internationalization functionality.
		 * - CBXWPEmailLogger_Admin. Defines all hooks for the admin area.
		 * - CBXWPEmailLogger_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies() {

			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-loader.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-i18n.php';


			/**
			 * The class responsible for defining helper static methods
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cbxwpemaillogger-functions.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-helper.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-settings.php'; //add the setting api

			/**
			 * The class responsible for defining admin log table listing
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-logs.php';

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cbxwpemaillogger-admin.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cbxwpemaillogger-public.php';

			$this->loader = new CBXWPEmailLogger_Loader();

		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the CBXWPEmailLogger_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {

			$plugin_i18n = new CBXWPEmailLogger_i18n();

			$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_admin_hooks() {

			$plugin_admin = new CBXWPEmailLogger_Admin( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_filter( 'plugin_action_links_' . CBXWPEMAILLOGGER_BASE_NAME, $plugin_admin, 'plugin_action_links' );

			//create admin menu page
			$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_pages' );
			$this->loader->add_filter( 'set-screen-option', $plugin_admin, 'cbxscratingreview_listing_per_page', 10, 3 );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			$this->loader->add_filter( 'wp_mail', $plugin_admin, 'insert_log' );
			$this->loader->add_action( 'wp_mail_failed', $plugin_admin, 'email_sent_failed' );
			$this->loader->add_action( 'wp_ajax_cbxwpemaillogger_log_delete', $plugin_admin, 'email_log_delete' ); //email_log_delete
			$this->loader->add_action( 'wp_ajax_cbxwpemaillogger_log_resend', $plugin_admin, 'email_resend' ); //resend email

			$this->loader->add_action( 'wp_ajax_cbxwpemaillogger_download_attachment', $plugin_admin, 'download_attachment' ); //download attachment

			$this->loader->add_action('cbxwpemaillogger_log_delete_after', $plugin_admin, 'delete_attachments_after_log_delete');
			$this->loader->add_action('cbxwpemaillogger_log_all_delete_after', $plugin_admin, 'delete_attachments_folder');

			//cron event
			$this->loader->add_action( 'cbxwpemaillogger_daily_event', $plugin_admin, 'delete_old_log' ); //delete x days old logs every day

			//added from v1.0.3
			$this->loader->add_filter( 'wp_mail_from', $plugin_admin, 'wp_mail_from_custom', 99999 );
			$this->loader->add_filter( 'wp_mail_from_name', $plugin_admin, 'wp_mail_from_name_custom', 99999 );
			$this->loader->add_filter( 'phpmailer_init', $plugin_admin, 'phpmailer_init_extend', 99999 );

			//for upgrade process
			$this->loader->add_action( 'upgrader_process_complete', $plugin_admin,'plugin_upgrader_process_complete',10, 2);
			$this->loader->add_action( 'admin_notices', $plugin_admin,'plugin_activate_upgrade_notices');

		}//end method define_admin_hooks

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_public_hooks() {

			$plugin_public = new CBXWPEmailLogger_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'template_redirect', $plugin_public, 'email_log_body' );

		}//end method define_public_hooks

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->loader->run();
		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    CBXWPEmailLogger_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since     1.0.0
		 * @return    string    The version number of the plugin.
		 */
		public function get_version() {
			return $this->version;
		}

	}//end class CBXWPEmailLogger
