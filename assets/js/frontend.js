// Frontend JavaScript for Excel to Lionwheel plugin
jQuery(document).ready(function($) {
    // Form validation
    $('.excel-order-form form').on('submit', function(e) {
        var name = $('#name').val().trim();
        var phone = $('#phone').val().trim();
        var email = $('#email').val().trim();
        var fileInput = $('#excel_file');
        
        // Basic validation
        if (!name) {
            alert('Please enter your name.');
            e.preventDefault();
            return false;
        }
        
        if (!phone) {
            alert('Please enter your phone number.');
            e.preventDefault();
            return false;
        }
        
        if (!email) {
            alert('Please enter your email address.');
            e.preventDefault();
            return false;
        }
        
        // Email validation
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            e.preventDefault();
            return false;
        }
        
        // File validation if file is selected
        if (fileInput[0].files.length > 0) {
            var fileName = fileInput.val();
            var ext = fileName.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['xlsx', 'xls']) === -1) {
                alert('Please upload only Excel files (.xlsx or .xls).');
                e.preventDefault();
                return false;
            }
            
            // File size check (max 10MB)
            var fileSize = fileInput[0].files[0].size;
            var maxSize = 10 * 1024 * 1024; // 10MB
            if (fileSize > maxSize) {
                alert('File is too large. Maximum size is 10MB.');
                e.preventDefault();
                return false;
            }
        }
        
        // Show loading indicator
        var submitBtn = $(this).find('input[type="submit"]');
        submitBtn.prop('disabled', true).val('Processing...');
    });
});