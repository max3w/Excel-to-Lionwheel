<?php
/**
 * Frontend class for Excel to Lionwheel plugin
 */
class Excel_To_Lionwheel_Frontend {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->init_hooks();
    }

    private function init_hooks() {
        add_shortcode( 'excel_order_form', array( $this, 'excel_order_form_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // Form processing (frontend and for logged-in admins)
        add_action( 'admin_post_nopriv_excel_order_submit', array( $this, 'handle_frontend_form_submission' ) );
        add_action( 'admin_post_excel_order_submit', array( $this, 'handle_frontend_form_submission' ) );
    }

    public function excel_order_form_shortcode() {
        ob_start();
        include EXCEL_TO_LIONWHEEL_DIR . 'inc/frontend/views/excel-order-form.php';
        return ob_get_clean();
    }

    public function enqueue_styles() {
        $css_file = EXCEL_TO_LIONWHEEL_DIR . 'assets/css/frontend.css';
        $version = file_exists($css_file) ? filemtime($css_file) : $this->version;
        
        wp_enqueue_style(
            $this->plugin_name . '-frontend',
            EXCEL_TO_LIONWHEEL_URL . 'assets/css/frontend.css',
            array(),
            $version
        );
    }

    public function enqueue_scripts() {
        $js_file = EXCEL_TO_LIONWHEEL_DIR . 'assets/js/frontend.js';
        $version = file_exists($js_file) ? filemtime($js_file) : $this->version;
        
        wp_enqueue_script(
            $this->plugin_name . '-frontend',
            EXCEL_TO_LIONWHEEL_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            $version,
            true
        );
    }

    /**
     * Form processing
     */
    public function handle_frontend_form_submission() {
        // Logging form processing start
        Excel_To_Lionwheel_Plugin::log( 'Excel order form processing started', 'info' );

        // nonce
        if ( ! isset($_POST['_wpnonce']) || ! wp_verify_nonce( $_POST['_wpnonce'], 'excel_order_nonce' ) ) {
            Excel_To_Lionwheel_Plugin::log( 'Security error: invalid nonce', 'error' );
            wp_safe_redirect( add_query_arg( 'excel_order_error', 'security', wp_get_referer() ) );
            exit;
        }
        Excel_To_Lionwheel_Plugin::log( 'Nonce verified successfully', 'info' );

        // validation
        if ( empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['email']) ) {
            Excel_To_Lionwheel_Plugin::log( 'Validation error: required fields missing', 'error' );
            wp_safe_redirect( add_query_arg( 'excel_order_error', 'required_fields', wp_get_referer() ) );
            exit;
        }
        Excel_To_Lionwheel_Plugin::log( 'Field validation passed successfully', 'info' );

        // sanitization
        $name  = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);
        Excel_To_Lionwheel_Plugin::log( "Data sanitized: name=$name, phone=$phone, email=$email", 'info' );

        // file
        $file_url = $this->handle_file_upload( $phone );
        if ( ! $file_url ) {
            Excel_To_Lionwheel_Plugin::log( 'File upload error', 'error' );
            wp_safe_redirect( add_query_arg( 'excel_order_error', 'upload_error', wp_get_referer() ) );
            exit;
        }
        Excel_To_Lionwheel_Plugin::log( "File uploaded successfully: $file_url", 'info' );

        // save to database
        global $wpdb;
        $table = $wpdb->prefix . 'excel_orders';
        $result = $wpdb->insert($table, [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'file_path' => $file_url,
            'created_at' => current_time('mysql')
        ]);

        if ( $result === false ) {
            Excel_To_Lionwheel_Plugin::log( 'Database save error: ' . $wpdb->last_error, 'error' );
            wp_safe_redirect( add_query_arg( 'excel_order_error', 'database', wp_get_referer() ) );
            exit;
        }

        Excel_To_Lionwheel_Plugin::log( "Data successfully saved to database. Record ID: " . $wpdb->insert_id, 'info' );

        // redirect back to form page with message
        Excel_To_Lionwheel_Plugin::log( 'Form processing completed successfully. Redirecting user', 'info' );
        wp_safe_redirect( add_query_arg( 'excel_order_success', '1', wp_get_referer() ) );
        exit;
    }

    /**
     * Excel file upload
     */
    private function handle_file_upload( $phone ) {
        Excel_To_Lionwheel_Plugin::log( 'Starting file upload processing for phone: ' . $phone, 'info' );
        
        if ( empty( $_FILES['excel_file']['name'] ) ) {
            Excel_To_Lionwheel_Plugin::log( 'Error: file was not uploaded', 'error' );
            return '';
        }

        Excel_To_Lionwheel_Plugin::log( 'File received: ' . $_FILES['excel_file']['name'] . ', size: ' . $_FILES['excel_file']['size'] . ' bytes', 'info' );

        // Size check (10MB)
        $max_size = 10 * 1024 * 1024;
        if ( $_FILES['excel_file']['size'] > $max_size ) {
            Excel_To_Lionwheel_Plugin::log( 'Error: file too large (' . $_FILES['excel_file']['size'] . ' bytes)', 'error' );
            return '';
        }
        Excel_To_Lionwheel_Plugin::log( 'File size check passed successfully', 'info' );

        // Supported types
        $allowed_exts = ['xls', 'xlsx'];
        $ext = strtolower(pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION));
        if ( ! in_array( $ext, $allowed_exts ) ) {
            Excel_To_Lionwheel_Plugin::log( 'Error: invalid file type (' . $ext . ')', 'error' );
            return '';
        }
        Excel_To_Lionwheel_Plugin::log( 'File type check passed successfully: ' . $ext, 'info' );

        // Create plugin folder uploads/<phone>/
        $upload_dir = EXCEL_TO_LIONWHEEL_DIR . 'uploads/' . sanitize_file_name($phone) . '/';
        if ( ! file_exists( $upload_dir ) ) {
            Excel_To_Lionwheel_Plugin::log( 'Creating upload directory: ' . $upload_dir, 'info' );
            wp_mkdir_p( $upload_dir );
            Excel_To_Lionwheel_Plugin::log( 'Directory created successfully', 'info' );
        } else {
            Excel_To_Lionwheel_Plugin::log( 'Directory already exists: ' . $upload_dir, 'info' );
        }

        // New filename: timestamp_originalname.xlsx
        $timestamp = time();
        $filename = $timestamp . '_' . sanitize_file_name($_FILES['excel_file']['name']);
        $destination = $upload_dir . $filename;

        Excel_To_Lionwheel_Plugin::log( 'Moving file to: ' . $destination, 'info' );

        if ( ! move_uploaded_file( $_FILES['excel_file']['tmp_name'], $destination ) ) {
            Excel_To_Lionwheel_Plugin::log( 'Error: failed to move file to ' . $destination, 'error' );
            return '';
        }

        Excel_To_Lionwheel_Plugin::log( 'File successfully moved to: ' . $destination, 'info' );

        // Return URL
        $file_url = EXCEL_TO_LIONWHEEL_URL . 'uploads/' . sanitize_file_name($phone) . '/' . $filename;
        Excel_To_Lionwheel_Plugin::log( 'File URL: ' . $file_url, 'info' );
        
        return $file_url;
    }

}
