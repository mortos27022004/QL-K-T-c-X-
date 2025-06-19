<?php
include '../config.php'; // nếu cần dùng DB

$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";

// Lấy dữ liệu từ URL (MoMo redirect về bằng phương thức GET)
$partnerCode = $_GET['partnerCode'] ?? '';
$orderId = $_GET['orderId'] ?? '';
$requestId = $_GET['requestId'] ?? '';
$amount = $_GET['amount'] ?? '';
$orderInfo = $_GET['orderInfo'] ?? '';
$orderType = $_GET['orderType'] ?? '';
$resultCode = $_GET['resultCode'] ?? '';
$message = $_GET['message'] ?? '';
$payType = $_GET['payType'] ?? '';
$responseTime = $_GET['responseTime'] ?? '';
$extraData = $_GET['extraData'] ?? ''; // payment_id đã được truyền từ trước
$signature = $_GET['signature'] ?? '';

if ($resultCode == "0" && $extraData !== '') {
    $payment_id = intval($extraData); // đảm bảo là số nguyên
    $stmt = $conn->prepare("UPDATE payment SET payment_status = 'paid', paid_at = NOW(), updated_at = NOW() WHERE payment_id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ../index.php?page_layout=list_payment");
 
}
?>
