<?php
/**
 * Settings class for Excel to Lionwheel plugin
 */
class Excel_To_Lionwheel_Settings {

	/**
	 * Constructor
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->init_hooks();
	}
	
	/**
	 * Initialize settings hooks
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_post_clear_excel_logs', array( $this, 'handle_clear_logs' ) );
	}
	
	/**
	 * Add settings menu
	 */
	public function add_settings_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Excel Lion Settings', 'excel-to-lionwheel' ),
			__( 'Excel Lion Settings', 'excel-to-lionwheel' ),
			'manage_woocommerce',
			'excel-to-lionwheel-settings',
			array( $this, 'settings_page_callback' )
		);
	}
	
	/**
	 * Settings page callback
	 */
	public function settings_page_callback() {
		// Ensure log file exists when loading settings page
		if ( ! file_exists( EXCEL_TO_LIONWHEEL_DIR . 'debug.log' ) ) {
			$log_dir = dirname( EXCEL_TO_LIONWHEEL_DIR . 'debug.log' );
			if ( ! file_exists( $log_dir ) ) {
				wp_mkdir_p( $log_dir );
			}
			file_put_contents( EXCEL_TO_LIONWHEEL_DIR . 'debug.log', "=== Excel to Lionwheel Debug Log ===\n" . date('Y-m-d H:i:s') . " - Log file created from settings page\n" );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<form method="post" action="options.php">
				<?php
				settings_fields( 'excel_to_lionwheel_settings' );
				do_settings_sections( 'excel_to_lionwheel_settings' );
				submit_button();
				?>
			</form>

			<hr>
			
			<h2><?php _e( 'Log Management', 'excel-to-lionwheel' ); ?></h2>
			<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
				<input type="hidden" name="action" value="clear_excel_logs">
				<?php wp_nonce_field( 'clear_excel_logs_nonce' ); ?>
				<p>
					<button type="submit" class="button button-secondary">
						<?php _e( 'Clear Logs', 'excel-to-lionwheel' ); ?>
					</button>
				</p>
			</form>

			<?php if ( file_exists( EXCEL_TO_LIONWHEEL_DIR . 'debug.log' ) ) : ?>
				<p>
					<strong><?php _e( 'Log file size:', 'excel-to-lionwheel' ); ?></strong>
					<?php echo size_format( filesize( EXCEL_TO_LIONWHEEL_DIR . 'debug.log' ) ); ?>
				</p>
				<h3><?php _e( 'Log content:', 'excel-to-lionwheel' ); ?></h3>
				<textarea style="width: 100%; height: 200px; font-family: monospace; font-size: 12px;" readonly>
                <?php echo esc_textarea( file_get_contents( EXCEL_TO_LIONWHEEL_DIR . 'debug.log' ) ); ?>
				</textarea>
			<?php else : ?>
				<p><?php _e( 'No log file found.', 'excel-to-lionwheel' ); ?></p>
				<p><strong><?php _e( 'Expected log file path:', 'excel-to-lionwheel' ); ?></strong> <?php echo esc_html( EXCEL_TO_LIONWHEEL_LOG_FILE ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
	
	/**
	 * Initialize settings
	 */
	public function settings_init() {
		register_setting( 'excel_to_lionwheel_settings', 'excel_to_lionwheel_options' );
		
		add_settings_section(
			'excel_to_lionwheel_section',
			__( 'Lionwheel API Settings', 'excel-to-lionwheel' ),
			array( $this, 'settings_section_callback' ),
			'excel_to_lionwheel_settings'
		);
		
		add_settings_field(
			'api_key',
			__( 'API Key', 'excel-to-lionwheel' ),
			array( $this, 'api_key_field_callback' ),
			'excel_to_lionwheel_settings',
			'excel_to_lionwheel_section'
		);
		
		add_settings_field(
			'api_url',
			__( 'API URL', 'excel-to-lionwheel' ),
			array( $this, 'api_url_field_callback' ),
			'excel_to_lionwheel_settings',
			'excel_to_lionwheel_section'
		);
		
		add_settings_field(
			'api_secret',
			__( 'API Secret', 'excel-to-lionwheel' ),
			array( $this, 'api_secret_field_callback' ),
			'excel_to_lionwheel_settings',
			'excel_to_lionwheel_section'
		);
		
		add_settings_field(
			'default_warehouse',
			__( 'Default Warehouse', 'excel-to-lionwheel' ),
			array( $this, 'default_warehouse_field_callback' ),
			'excel_to_lionwheel_settings',
			'excel_to_lionwheel_section'
		);

		add_settings_field(
			'debug_mode',
			__( 'Debug Mode', 'excel-to-lionwheel' ),
			array( $this, 'debug_mode_field_callback' ),
			'excel_to_lionwheel_settings',
			'excel_to_lionwheel_section'
		);
	}
	
	/**
	 * Settings section callback
	 */
	public function settings_section_callback() {
		echo '<p>' . __( 'Configure your Lionwheel API connection settings.', 'excel-to-lionwheel' ) . '</p>';
	}
	
	/**
	 * API key field callback
	 */
	public function api_key_field_callback() {
		$options = get_option( 'excel_to_lionwheel_options' );
		$value = isset( $options['api_key'] ) ? $options['api_key'] : '';
		echo '<input type="text" name="excel_to_lionwheel_options[api_key]" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">' . __( 'Your Lionwheel API key', 'excel-to-lionwheel' ) . '</p>';
	}
	
	/**
	 * API URL field callback
	 */
	public function api_url_field_callback() {
		$options = get_option( 'excel_to_lionwheel_options' );
		$value = isset( $options['api_url'] ) ? $options['api_url'] : 'https://api.lionwheel.com';
		echo '<input type="url" name="excel_to_lionwheel_options[api_url]" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">' . __( 'Lionwheel API endpoint URL', 'excel-to-lionwheel' ) . '</p>';
	}
	
	/**
	 * API secret field callback
	 */
	public function api_secret_field_callback() {
		$options = get_option( 'excel_to_lionwheel_options' );
		$value = isset( $options['api_secret'] ) ? $options['api_secret'] : '';
		echo '<input type="password" name="excel_to_lionwheel_options[api_secret]" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">' . __( 'Your Lionwheel API secret key', 'excel-to-lionwheel' ) . '</p>';
	}
	
	/**
	 * Default warehouse field callback
	 */
	public function default_warehouse_field_callback() {
		$options = get_option( 'excel_to_lionwheel_options' );
		$value = isset( $options['default_warehouse'] ) ? $options['default_warehouse'] : '';
		echo '<input type="text" name="excel_to_lionwheel_options[default_warehouse]" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">' . __( 'Default warehouse code for orders', 'excel-to-lionwheel' ) . '</p>';
	}

	/**
		* Debug mode field callback
		*/
	public function debug_mode_field_callback() {
		$options = get_option( 'excel_to_lionwheel_options' );
		$value = isset( $options['debug_mode'] ) ? $options['debug_mode'] : 0;
		echo '<input type="checkbox" name="excel_to_lionwheel_options[debug_mode]" value="1" ' . checked( $value, 1, false ) . '>';
		echo '<label for="excel_to_lionwheel_options[debug_mode]">' . __( 'Enable debug logging', 'excel-to-lionwheel' ) . '</label>';
		echo '<p class="description">' . __( 'When enabled, detailed logs will be written to the plugin log file', 'excel-to-lionwheel' ) . '</p>';
	}
	
	/**
	 * Get API settings
	 */
	public static function get_api_settings() {
		$options = get_option( 'excel_to_lionwheel_options' );
		return array(
			'api_key' => isset( $options['api_key'] ) ? $options['api_key'] : '',
			'api_url' => isset( $options['api_url'] ) ? $options['api_url'] : 'https://api.lionwheel.com',
			'api_secret' => isset( $options['api_secret'] ) ? $options['api_secret'] : '',
			'default_warehouse' => isset( $options['default_warehouse'] ) ? $options['default_warehouse'] : '',
			'debug_mode' => isset( $options['debug_mode'] ) ? $options['debug_mode'] : 0
		);
	}

	/**
	 * Check if debug mode is enabled
	 */
	public static function is_debug_enabled() {
		$options = get_option( 'excel_to_lionwheel_options' );
		return isset( $options['debug_mode'] ) && $options['debug_mode'] == 1;
	}

	/**
	 * Handle clear logs action
	 */
	public function handle_clear_logs() {
		$log_file = EXCEL_TO_LIONWHEEL_DIR . 'debug.log';
		if ( file_put_contents( $log_file, "=== Excel to Lionwheel Debug Log ===\n" . date('Y-m-d H:i:s') . " - Logs cleared\n" ) !== false ) {
			add_settings_error( 'excel_to_lionwheel_messages', 'excel_to_lionwheel_message', __( 'Logs cleared successfully.', 'excel-to-lionwheel' ), 'updated' );
		} else {
			add_settings_error( 'excel_to_lionwheel_messages', 'excel_to_lionwheel_message', __( 'Failed to clear logs.', 'excel-to-lionwheel' ), 'error' );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=excel-to-lionwheel-settings' ) );
	}
}