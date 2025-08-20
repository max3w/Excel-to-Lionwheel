<?php
/**
 * Test script for Excel Editor functionality
 */

// Include WordPress bootstrap
require_once('../../../wp-load.php');

// Load PHPExcel library
require_once(__DIR__ . '/lib/phpexcel/Classes/PHPExcel.php');

// Include plugin files
require_once('excel-to-lionwheel.php');
require_once('inc/class-excel-editor.php');

// Test the Excel Editor
$plugin = Excel_To_Lionwheel_Plugin::get_instance();
$editor = $plugin->editor;

echo "<h1>Testing Excel Editor</h1>";

// Check if PHPExcel is loaded
if (class_exists('PHPExcel')) {
    echo "<p style='color: green;'>✓ PHPExcel is loaded successfully</p>";
} else {
    echo "<p style='color: red;'>✗ PHPExcel failed to load</p>";
}

// Check if we can create a simple spreadsheet
try {
    $spreadsheet = new PHPExcel();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Test Value');
    
    echo "<p style='color: green;'>✓ Basic spreadsheet operations work</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error creating spreadsheet: " . $e->getMessage() . "</p>";
}

echo "<h2>Loaded Classes:</h2>";
echo "<pre>";
$loaded_classes = get_declared_classes();
$spreadsheet_classes = array_filter($loaded_classes, function($class) {
    return strpos($class, 'PHPExcel') === 0;
});
print_r($spreadsheet_classes);
echo "</pre>";echo "</pre>";