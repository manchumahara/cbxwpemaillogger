<?php

	/**
	 * The plugin bootstrap file
	 *
	 * This file is read by WordPress to generate the plugin information in the plugin
	 * admin area. This file also includes all of the dependencies used by the plugin,
	 * registers the activation and deactivation functions, and defines a function
	 * that starts the plugin.
	 *
	 * @link              https://codeboxr.com
	 * @since             1.0.0
	 * @package           CBXWPEmailLogger
	 *
	 * @wordpress-plugin
	 * Plugin Name:       CBX Email Logger
	 * Plugin URI:        https://codeboxr.com/product/cbx-email-logger-for-wordpress/
	 * Description:       Logs email, tracks sent or failed status and more.
	 * Version:           1.0.2
	 * Author:            Codeboxr
	 * Author URI:        https://codeboxr.com
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       cbxwpemaillogger
	 * Domain Path:       /languages
	 */

	//CBX Email Logger

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	defined( 'CBXWPEMAILLOGGER_PLUGIN_NAME' ) or define( 'CBXWPEMAILLOGGER_PLUGIN_NAME', 'cbxwpemaillogger' );
	defined( 'CBXWPEMAILLOGGER_PLUGIN_VERSION' ) or define( 'CBXWPEMAILLOGGER_PLUGIN_VERSION', '1.0.2' );
	defined( 'CBXWPEMAILLOGGER_BASE_NAME' ) or define( 'CBXWPEMAILLOGGER_BASE_NAME', plugin_basename( __FILE__ ) );
	defined( 'CBXWPEMAILLOGGER_ROOT_PATH' ) or define( 'CBXWPEMAILLOGGER_ROOT_PATH', plugin_dir_path( __FILE__ ) );
	defined( 'CBXWPEMAILLOGGER_ROOT_URL' ) or define( 'CBXWPEMAILLOGGER_ROOT_URL', plugin_dir_url( __FILE__ ) );

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-cbxwpemaillogger-activator.php
	 */
	function activate_cbxwpemaillogger() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxwpemaillogger-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxwpemaillogger-activator.php';
		CBXWPEmailLogger_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-cbxwpemaillogger-deactivator.php
	 */
	function deactivate_cbxwpemaillogger() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxwpemaillogger-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxwpemaillogger-deactivator.php';
		CBXWPEmailLogger_Deactivator::deactivate();
	}

	function uninstall_cbxwpemaillogger() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxwpemaillogger-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxwpemaillogger-uninstall.php';
		CBXWPEmailLogger_Uninstall::uninstall();
	}

	register_activation_hook( __FILE__, 'activate_cbxwpemaillogger' );
	register_deactivation_hook( __FILE__, 'deactivate_cbxwpemaillogger' );
	register_uninstall_hook( __FILE__, 'uninstall_cbxwpemaillogger' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-cbxwpemaillogger.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_cbxwpemaillogger() {

		$plugin = new CBXWPEmailLogger();
		$plugin->run();

	}

	run_cbxwpemaillogger();
