<?php 
    session_start();
    include('../../admin/config/config.php');
    require '../../carbon/autoload.php';

    use Carbon\Carbon;
    if (isset($_SESSION['account_id'])) {
        $product_id = $_GET['product_id'];
        $account_id = $_SESSION['account_id'];

        // Kiểm tra xem người dùng đã mua sản phẩm này và đơn hàng đã hoàn thành chưa
        $sql_check_purchase = "SELECT o.order_id, o.order_code 
                             FROM orders o 
                             JOIN order_detail od ON o.order_code = od.order_code 
                             WHERE o.account_id = '$account_id' 
                             AND od.product_id = '$product_id'
                             AND o.order_status = 5";
        $query_check_purchase = mysqli_query($mysqli, $sql_check_purchase);

        if(mysqli_num_rows($query_check_purchase) == 0) {
            header('Location:../../index.php?page=product_detail&product_id='.$product_id.'&message=warning');
            exit();
        }

        $order_data = mysqli_fetch_array($query_check_purchase);
        $order_code = $order_data['order_code'];

        if (isset($_POST['evaluate_add'])) {
            $evaluate_rate = $_POST['rate'];
            $evaluate_content = $_POST['evaluate_content'];
            $evaluate_date = Carbon::now('Asia/Ho_Chi_Minh');

            $query_account = mysqli_query($mysqli, "SELECT * FROM account WHERE account_id = '$account_id' LIMIT 1");
            $account = mysqli_fetch_array($query_account);

            // Kiểm tra xem đã đánh giá chưa
            $check_existing = mysqli_query($mysqli, "SELECT evaluate_id FROM evaluate WHERE account_id = '$account_id' AND product_id = '$product_id' AND order_code = '$order_code'");
            
            if(mysqli_num_rows($check_existing) > 0) {
                // Cập nhật đánh giá cũ
                $evaluate = mysqli_fetch_array($check_existing);
                $sql_update = "UPDATE evaluate SET 
                    evaluate_rate = '$evaluate_rate',
                    evaluate_content = '$evaluate_content',
                    evaluate_date = '$evaluate_date'
                    WHERE evaluate_id = '".$evaluate['evaluate_id']."'";
                mysqli_query($mysqli, $sql_update);
            } else {
                // Thêm đánh giá mới
                $sql_insert = "INSERT INTO evaluate (account_id, product_id, order_code, account_name, evaluate_rate, evaluate_content, evaluate_date, evaluate_status) 
                              VALUES ('$account_id', '$product_id', '$order_code', '".$account['account_name']."', '$evaluate_rate', '$evaluate_content', '$evaluate_date', 1)";
                mysqli_query($mysqli, $sql_insert);
            }

            header('Location:../../index.php?page=product_detail&product_id='.$product_id.'&message=accept');
        }
    } else {
        header('Location:../../index.php?page=product_detail&product_id='.$product_id.'&message=warning');
    }
