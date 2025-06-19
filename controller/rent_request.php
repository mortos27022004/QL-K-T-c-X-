<?php
session_start();
require_once '../config.php';

// Kiểm tra người dùng đã đăng nhập và là tenant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: ../login.php");
    exit;
}

// Lấy dữ liệu từ form
$tenant_id = $_SESSION['user_id'];
$room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
$message = trim($_POST['message'] ?? '');

// Kiểm tra dữ liệu đầu vào
if ($room_id <= 0) {
    echo "Yêu cầu không hợp lệ.";
    exit;
}

// Thêm yêu cầu vào bảng rental_request
$sql = "INSERT INTO rental_request (tenant_id, room_id, message, status, created_at, updated_at)
        VALUES (?, ?, ?, 'pending', NOW(), NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Lỗi truy vấn: " . $conn->error;
    exit;
}

$stmt->bind_param("iis", $tenant_id, $room_id, $message);

if ($stmt->execute()) {
    // Sau khi thành công, chuyển hướng về trang chi tiết phòng hoặc hiển thị thông báo
    header("Location: ../index.php?id=$room_id");
    exit;
} else {
    echo "Lỗi khi gửi yêu cầu: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
