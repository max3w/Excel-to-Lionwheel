<?php

if ( isset( $_GET['excel_order_success'] ) ) {
    echo '<div class="excel-order-success" style="background:#d4edda;color:#155724;padding:15px;margin:20px 0;border:1px solid #c3e6cb;border-radius:4px;">
        ✅ Your order has been successfully sent! We will contact you shortly.
    </div>';
}

if ( isset( $_GET['excel_order_error'] ) ) {
    $error_type = sanitize_text_field( $_GET['excel_order_error'] );
    $errors = [
        'invalid_file'   => 'Invalid file type. Please upload only .xlsx or .xls',
        'file_too_large' => 'File size exceeds 10MB.',
        'upload_error'   => 'File upload error.',
        'database'       => 'Database error. Please try again later.',
        'security'       => 'Security error. Please refresh the page and try again.',
        'required_fields'=> 'Please fill all required fields.',
    ];

    if ( isset( $errors[$error_type] ) ) {
        echo '<div class="excel-order-error" style="background:#f8d7da;color:#721c24;padding:15px;margin:20px 0;border:1px solid #f5c6cb;border-radius:4px;">
            ❌ Error: ' . esc_html( $errors[$error_type] ) . '
        </div>';
    }
}
?>

<div class="excel-order-form">
    <h2>Order from Excel</h2>
    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <?php wp_nonce_field('excel_order_nonce', '_wpnonce'); ?>
        <input type="hidden" name="action" value="excel_order_submit">

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" name="phone" id="phone" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="excel_file">Excel order file:</label>
            <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" required>
            <small>.xlsx and .xls files are supported (max. 10MB)</small>
        </div>

        <div class="form-group">
            <input type="submit" value="Send order">
        </div>
    </form>
</div>
