<?php
/**
 * Admin class for Excel to Lionwheel plugin
 */
class Excel_To_Lionwheel_Admin {
	
	private $plugin_name;
	private $version;
	
	/**
	 * Constructor
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->init_hooks();
	}
	
	/**
	 * Initialize admin hooks
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// Create database table on plugin activation
		register_activation_hook( EXCEL_TO_LIONWHEEL_DIR . 'excel-to-lionwheel.php', array( $this, 'create_database_table' ) );
	}
	
	/**
	 * Add admin menu under WooCommerce
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'woocommerce',
			'Excel Lionwheel',
			'Excel Lionwheel',
			'manage_woocommerce',
			'excel-orders',
			array( $this, 'display_excel_orders_page' )
		);
	}
	
	/**
	 * Display excel orders admin page
	 */
	public function display_excel_orders_page() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'excel_orders';
		
		// Handle search
		$search = '';
		if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
			$search = sanitize_text_field( $_GET['s'] );
			$orders = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM $table_name WHERE phone LIKE %s OR email LIKE %s ORDER BY created_at DESC",
				'%' . $search . '%',
				'%' . $search . '%'
			) );
		} else {
			$orders = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
		}
		?>
		<div class="wrap">
			<h1>Excel Orders Database</h1>
			
			<!-- Search form -->
			<div class="xls-search">
				<form method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
					<input type="hidden" name="page" value="excel-orders">
					<p>
						<label for="search-input">Search by phone or email:</label>
						<input type="text" id="search-input" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Enter phone or email">
						<input type="submit" class="button" value="Search">
						<?php if ( ! empty( $search ) ) : ?>
							<a href="<?php echo admin_url( 'admin.php?page=excel-orders' ); ?>" class="button">Clear Search</a>
						<?php endif; ?>
					</p>
				</form>
			</div>
			
			<!-- Orders table -->
			<div class="xls-orders">
				<h2>All Orders</h2>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Phone</th>
							<th>Email</th>
							<th>File</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $orders ) ) : ?>
							<?php foreach ( $orders as $order ) : ?>
							<tr>
								<td><?php echo $order->id; ?></td>
								<td><?php echo esc_html( $order->name ); ?></td>
								<td><?php echo esc_html( $order->phone ); ?></td>
								<td><?php echo esc_html( $order->email ); ?></td>
								<td>
									<?php if ( $order->file_path ) : ?>
										<a href="<?php echo esc_url( $order->file_path ); ?>" target="_blank">Download File</a>
									<?php else : ?>
										No file
									<?php endif; ?>
								</td>
								<td><?php echo $order->created_at; ?></td>
							</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="6" style="text-align: center;">
									<?php if ( ! empty( $search ) ) : ?>
										No orders found matching your search criteria.
									<?php else : ?>
										No orders found in the database.
									<?php endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
	
	
	/**
		* Create database table for orders
		*/
	public function create_database_table() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'excel_orders';
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(100) NOT NULL,
			phone varchar(20) NOT NULL,
			email varchar(100) NOT NULL,
			file_path varchar(255) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	/**
	 * Enqueue admin styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name . '-admin',
			EXCEL_TO_LIONWHEEL_URL . 'assets/css/admin.css',
			array(),
			$this->version
		);
	}
	
	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name . '-admin',
			EXCEL_TO_LIONWHEEL_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);
    }
}