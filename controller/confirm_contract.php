<?php
require_once __DIR__ . '/../config.php';

    $contract_id = intval($_POST['contract_id']); // Ép kiểu an toàn

    // Cập nhật trạng thái hợp đồng sang 'active' và lưu thời gian ký
    $update_sql = "UPDATE rental_contract 
                   SET status = 'active', signed_at = NOW() 
                   WHERE contract_id = ?";
    $update_stmt = $conn->prepare($update_sql);

    if ($update_stmt) {
        $update_stmt->bind_param("i", $contract_id);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Quay lại trang danh sách yêu cầu thuê
    header("Location: ../index.php?page_layout=tenant_requests");
    exit;

?>
