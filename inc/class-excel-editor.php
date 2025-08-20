<?php
namespace ExcelToLionwheel\Inc;

use Exception;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

class ExcelEditor {
    /**
     * @var string Path to the PHPExcel library
     */
    private $phpexcel_path;

    public function __construct() {
        // Set path to PHPExcel library
        $this->phpexcel_path = EXCEL_TO_LIONWHEEL_DIR . 'lib/phpexcel/Classes/PHPExcel.php';

        // Load PHPExcel library if not already loaded
        if (!class_exists('PHPExcel')) {
            if (file_exists($this->phpexcel_path)) {
                require_once($this->phpexcel_path);
            } else {
                throw new Exception('PHPExcel library not found at: ' . $this->phpexcel_path);
            }
        }
    }

    /**
     * Get data from an Excel file for display in the admin area
     */
    public function getExcelDataForAdmin($file_path, $limit = 200) {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $spreadsheet = $objReader->load($file_path);
            $sheet = $spreadsheet->getActiveSheet();
            
            $data = array();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            
            // Limit 200 rows
            $displayRows = min($highestRow, $limit);
            
            for ($row = 1; $row <= $displayRows; $row++) {
                $rowData = array();
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }
            
            return array(
                'data' => $data,
                'total_rows' => $highestRow,
                'total_columns' => $highestColumnIndex,
                'displayed_rows' => $displayRows
            );
            
        } catch (Exception $e) {
            return array(
                'error' => 'File reading error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Edit a cell in an Excel file
     */
    public function editExcelCell($file_path, $cell_address, $new_value) {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $spreadsheet = $objReader->load($file_path);
            $sheet = $spreadsheet->getActiveSheet();
            
            $sheet->setCellValue($cell_address, $new_value);
            
            // Save changes
            $objWriter = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
            $objWriter->save($file_path);
            
            return array(
                'success' => true,
                'message' => 'Cell updated successfully'
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'error' => 'Error edit: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get info about file
     */
    public function getFileInfo($file_path) {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $spreadsheet = $objReader->load($file_path);
            $sheet = $spreadsheet->getActiveSheet();
            
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            
            return array(
                'file_name' => basename($file_path),
                'total_rows' => $sheet->getHighestRow(),
                'total_columns' => $highestColumnIndex,
                'last_modified' => filemtime($file_path)
            );
            
        } catch (Exception $e) {
            return array(
                'error' => 'Error receiving information: ' . $e->getMessage()
            );
        }
    }

    /**
     * Display Excel file content in admin
     */
    public function display_excel_file($file_path, $return = false) {
        $data = $this->getExcelDataForAdmin($file_path);
        
        if (isset($data['error'])) {
            if ($return) {
                return '<div class="error"><p>' . esc_html($data['error']) . '</p></div>';
            } else {
                echo '<div class="error"><p>' . esc_html($data['error']) . '</p></div>';
                return;
            }
        }
        
        ob_start();
        ?>
        <div class="wrap">
            <h1>Excel File Viewer: <?php echo esc_html(basename($file_path)); ?></h1>
            
            <style>
                .excel-viewer { font-family: Calibri, Arial, sans-serif; font-size: 13px; color: #111; }
                .excel-meta { margin: 8px 0 14px; }
                .excel-meta p { margin: 2px 0; color: #444; }
                .excel-grid-wrap { border: 1px solid #d0d7de; border-radius: 4px; background: #fff; }
                .excel-grid-scroll { max-height: 65vh; overflow: auto; }
                table.excel-grid { width: 100%; border-collapse: collapse; table-layout: auto; }
                /* Do not truncate: keep content on one line and allow horizontal scrolling */
                table.excel-grid th, table.excel-grid td { border: 1px solid #d0d7de; padding: 6px 8px; vertical-align: top; background: #fff; white-space: nowrap; overflow: visible; text-overflow: clip; }
                table.excel-grid th { position: sticky; top: 0; background: #f3f6fb; font-weight: 600; z-index: 2; color: #223; }
                table.excel-grid tr:nth-child(even) td { background: #fafafa; }
                table.excel-grid td.num { text-align: right; font-variant-numeric: tabular-nums; }
                .excel-caption { padding: 8px 10px; background: #f6f8fa; border-bottom: 1px solid #e5e7eb; color: #333; font-weight: 600; }
            </style>

            <div class="excel-viewer">
                <div class="excel-meta">
                    <p><strong>Total Rows:</strong> <?php echo (int) $data['total_rows']; ?></p>
                    <p><strong>Total Columns:</strong> <?php echo (int) $data['total_columns']; ?></p>
                    <p><strong>Displaying:</strong> <?php echo (int) $data['displayed_rows']; ?> rows</p>
                </div>

                <div class="excel-grid-wrap">
                    <div class="excel-caption">Preview</div>
                    <div class="excel-grid-scroll">
                        <table class="excel-grid">
                            <thead>
                                <tr>
                                    <?php for ($col = 0; $col < $data['total_columns']; $col++): ?>
                                        <th><?php echo PHPExcel_Cell::stringFromColumnIndex($col); ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['data'] as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $cell): ?>
                                            <?php $isNum = is_numeric($cell); ?>
                                            <td class="<?php echo $isNum ? 'num' : ''; ?>">
                                                <?php echo ($cell === '' || $cell === null) ? '&nbsp;' : esc_html($cell); ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
        $output = ob_get_clean();
        
        if ($return) {
            return $output;
        } else {
            echo $output;
        }

        return $output;
    }
}