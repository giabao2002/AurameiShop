<?php
include('../config/config.php');
require '../../carbon/autoload.php';
require("../../vendor/autoload.php");

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

// Lấy thời gian từ URL (nếu có), mặc định là '365ngay'
$thoigian = isset($_GET['thoigian']) ? $_GET['thoigian'] : '365ngay';

// Tạo đối tượng Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Đặt tiêu đề cho các cột
$sheet->setCellValue('A1', 'Ngày');
$sheet->setCellValue('B1', 'Số đơn');
$sheet->setCellValue('C1', 'Số sản phẩm');
$sheet->setCellValue('D1', 'Doanh thu');

// Xác định ngày bắt đầu dựa trên thời gian được chọn
if ($thoigian == '7ngay') {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(7)->toDateString();
} elseif ($thoigian == '28ngay') {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(28)->toDateString();
} elseif ($thoigian == '90ngay') {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(90)->toDateString();
} elseif ($thoigian == '365ngay') {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(365)->toDateString();
} else {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->firstOfMonth()->toDateString();
}

$now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

// Truy vấn cơ sở dữ liệu để lấy thông tin thống kê
$sql = "SELECT * FROM metrics WHERE metric_date BETWEEN '$subdays' AND '$now' ORDER BY metric_date ASC";
$sql_query = mysqli_query($mysqli, $sql);

// Thiết lập chỉ số dòng trong Excel bắt đầu từ dòng 2
$rowIndex = 2;

// Nếu có dữ liệu trong cơ sở dữ liệu
if (mysqli_num_rows($sql_query) > 0) {
    while ($row = mysqli_fetch_array($sql_query)) {
        $sheet->setCellValue('A' . $rowIndex, $row['metric_date']);
        $sheet->setCellValue('B' . $rowIndex, $row['metric_order']);
        $sheet->setCellValue('C' . $rowIndex, $row['metric_quantity']);
        $sheet->setCellValue('D' . $rowIndex, $row['metric_sales']);
        $rowIndex++;
    }
} else {
    $sheet->setCellValue('A' . $rowIndex, 'No records found...');
}

// Tạo tên file Excel
$fileName = 'thongke_' . date('d_m_Y') . '.xlsx';

// Tạo đối tượng writer và lưu file
$writer = new Xlsx($spreadsheet);

// Thiết lập headers cho việc tải file xuống
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

// Xuất file ra
$writer->save('php://output');
