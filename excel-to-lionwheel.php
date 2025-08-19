<?php
/**
 * Plugin Name: Excel to Lionwheel
 * Plugin URI: 
 * Description: Push Excel files to Lionwheel
 * Version: 1.0.0
 * Requires at least: 5.7.2
 * Requires PHP: 7.4
 * Author: Exit-tech
 * License: GPL v2 or later
 * Text Domain: excel-to-lionwheel
 * Domain Path: /languages
 * Author URI: 
 *
 * @package           Excel_To_Lionwheel
 * @author            Exit-tech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'EXCEL_TO_LIONWHEEL_VERSION', '1.0.0' );
define( 'EXCEL_TO_LIONWHEEL_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXCEL_TO_LIONWHEEL_URL', plugin_dir_url( __FILE__ ) );
define( 'EXCEL_TO_LIONWHEEL_LOG_FILE', EXCEL_TO_LIONWHEEL_DIR . 'debug.log' );

// Include required files
require_once EXCEL_TO_LIONWHEEL_DIR . 'inc/admin/class-admin.php';
require_once EXCEL_TO_LIONWHEEL_DIR . 'inc/frontend/class-frontend.php';
require_once EXCEL_TO_LIONWHEEL_DIR . 'inc/admin/class-settings.php';

/**
 * Main plugin class
 */
class Excel_To_Lionwheel_Plugin {
	
	private static $instance = null;
	
	/**
	 * Get plugin instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		$this->load_textdomain();
		$this->init_classes();
		$this->init_hooks();
	}
	
	/**
	 * Initialize plugin classes
	 */
	private function init_classes() {
		$this->admin = new Excel_To_Lionwheel_Admin( 'excel-to-lionwheel', EXCEL_TO_LIONWHEEL_VERSION );
		$this->frontend = new Excel_To_Lionwheel_Frontend( 'excel-to-lionwheel', EXCEL_TO_LIONWHEEL_VERSION );
		$this->settings = new Excel_To_Lionwheel_Settings( 'excel-to-lionwheel', EXCEL_TO_LIONWHEEL_VERSION );
	}
	
	/**
	 * Load plugin textdomain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'excel-to-lionwheel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Common hooks
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}
	
	/**
	 * Plugin activation hook - create log file
	 */
	public function activate() {
		// Ensure the log directory exists
		$log_dir = dirname( EXCEL_TO_LIONWHEEL_LOG_FILE );
		if ( ! file_exists( $log_dir ) ) {
			wp_mkdir_p( $log_dir );
		}

		// Create log file if it doesn't exist or is empty
		if ( ! file_exists( EXCEL_TO_LIONWHEEL_LOG_FILE ) || filesize( EXCEL_TO_LIONWHEEL_LOG_FILE ) === 0 ) {
			file_put_contents( EXCEL_TO_LIONWHEEL_LOG_FILE, "=== Excel to Lionwheel Debug Log ===\n" . date('Y-m-d H:i:s') . " - Plugin activated and log file initialized\n" );
		}
	}

	/**
	 * Log message to debug file
	 *
	 * @param string $message Message to log
	 * @param string $level Log level (info, warning, error)
	 */
	public static function log( $message, $level = 'info' ) {
	    $options = get_option( 'excel_to_lionwheel_options' );
	    $debug_mode = isset( $options['debug_mode'] ) ? $options['debug_mode'] : false;
	    
	    // Always create log file if not exist
	    if ( ! file_exists( EXCEL_TO_LIONWHEEL_LOG_FILE ) ) {
	        $log_dir = dirname( EXCEL_TO_LIONWHEEL_LOG_FILE );
	        if ( ! file_exists( $log_dir ) ) {
	            wp_mkdir_p( $log_dir );
	        }
	        file_put_contents( EXCEL_TO_LIONWHEEL_LOG_FILE, "=== Excel to Lionwheel Debug Log ===\n" . date('Y-m-d H:i:s') . " - Log file created\n" );
	    }
	    
	    if ( $debug_mode ) {
	        $timestamp = date( 'Y-m-d H:i:s' );
	        $log_entry = "[$timestamp] [$level] $message\n";
	        file_put_contents( EXCEL_TO_LIONWHEEL_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX );
	    } else {
	        // Always write at least basic info even if debug mode is off
	        $timestamp = date( 'Y-m-d H:i:s' );
	        $log_entry = "[$timestamp] [info] Function called but debug mode is disabled: $message\n";
	        file_put_contents( EXCEL_TO_LIONWHEEL_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX );
	    }
	}
}

// Initialize the plugin
Excel_To_Lionwheel_Plugin::get_instance();

// Add settings link to plugin action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=excel-to-lionwheel-settings' ) . '">' . __( 'Settings', 'excel-to-lionwheel' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
} );