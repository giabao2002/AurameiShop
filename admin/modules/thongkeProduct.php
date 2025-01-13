<?php
require '../../carbon/autoload.php';
include('../config/config.php');

use Carbon\Carbon;

// Lấy ngày đầu tháng và ngày hiện tại
$start_date = Carbon::now('Asia/Ho_Chi_Minh')->firstOfMonth()->toDateString();
$end_date = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

// Truy vấn lấy top 10 sản phẩm bán chạy
$sql = "SELECT 
            p.product_name, 
            SUM(od.product_quantity) AS total_sold_quantity
        FROM 
            product p
        LEFT JOIN 
            order_detail od ON p.product_id = od.product_id
        LEFT JOIN 
            orders o ON od.order_code = o.order_code
        GROUP BY 
            p.product_name
        ORDER BY 
            total_sold_quantity DESC
        LIMIT 10";

$sql_query = mysqli_query($mysqli, $sql);

// Chuẩn bị dữ liệu cho biểu đồ
$chart_data = [];
if (mysqli_num_rows($sql_query) > 0) {
    while ($val = mysqli_fetch_array($sql_query)) {
        $chart_data[] = [
            'name' => $val['product_name'],
            'quantity' => intval($val['total_sold_quantity'])
        ];
    }
}

// Nếu không có dữ liệu, trả về mảng rỗng
if (empty($chart_data)) {
    $chart_data[] = ['name' => 'Không có dữ liệu', 'quantity' => 0];
}

echo json_encode($chart_data);
