<?php
session_start();
include('../../admin/config/config.php');
if (isset($_GET['order_code']) && isset($_GET['order_cancel']) && $_GET['order_cancel'] == 1) {
    $order_code = $_GET['order_code'];
    $sql_get_order = "SELECT * FROM orders WHERE order_code = $order_code LIMIT 1";
    $query_get_order = mysqli_query($mysqli, $sql_get_order);

    $sql_get_order_detail = "SELECT * FROM order_detail WHERE order_code = $order_code";
    $query_order_detail = mysqli_query($mysqli, $sql_get_order_detail);

    $order = mysqli_fetch_array($query_get_order);
    if ($order['order_status'] == 0) {
        while ($item = mysqli_fetch_array($query_order_detail)) {
            $product_id = $item['product_id'];
            $query_get_product = mysqli_query($mysqli, "SELECT * FROM product WHERE product_id = $product_id");
            $product = mysqli_fetch_array($query_get_product);
            $quantity = $product['product_quantity'] + $item['product_quantity'];
            $quantity_sales = $product['quantity_sales'] - $item['product_quantity'];

            mysqli_query($mysqli, "UPDATE product SET product_quantity = $quantity, quantity_sales = $quantity_sales WHERE product_id = $product_id");
        }
        $sql_cancel_order = "UPDATE orders SET order_status = -1 WHERE order_code = $order_code";
        mysqli_query($mysqli, $sql_cancel_order);

        header('Location:../../index.php?page=my_account&tab=account_order');
    }
} elseif (isset($_GET['order_code']) && isset($_GET['order_confirm']) && $_GET['order_confirm'] == 1) {
    $order_code = $_GET['order_code'];
    $sql_update_order = "UPDATE orders SET order_status = 5 WHERE order_code = $order_code LIMIT 1";
    $query_update_order = mysqli_query($mysqli, $sql_update_order);
    header('Location:../../index.php?page=my_account&tab=account_order');
} elseif (isset($_POST['update_order']) && isset($_POST['order_code'])) {
    $order_code = $_POST['order_code'];
    $delivery_name = $_POST['delivery_name'];
    $delivery_phone = $_POST['delivery_phone'];
    $delivery_address = $_POST['delivery_address'];
    $delivery_note = $_POST['delivery_note'];

    // Kiểm tra trạng thái đơn hàng trước khi cập nhật
    $check_order = mysqli_query($mysqli, "SELECT order_status FROM orders WHERE order_code = '$order_code' LIMIT 1");
    $order = mysqli_fetch_array($check_order);
    
    if ($order && $order['order_status'] == 0) {
        // Cập nhật thông tin giao hàng trong bảng delivery
        $sql_update_delivery = "UPDATE delivery SET 
            delivery_name = '$delivery_name',
            delivery_phone = '$delivery_phone',
            delivery_address = '$delivery_address',
            delivery_note = '$delivery_note'
        WHERE delivery_id = (SELECT delivery_id FROM orders WHERE order_code = '$order_code')";
        
        if (mysqli_query($mysqli, $sql_update_delivery)) {
            header('Location:../../index.php?page=order_detail&order_code='.$order_code.'&message=success');
        } else {
            header('Location:../../index.php?page=order_detail&order_code='.$order_code.'&message=error');
        }
    } else {
        header('Location:../../index.php?page=order_detail&order_code='.$order_code.'&message=invalid_status');
    }
}
