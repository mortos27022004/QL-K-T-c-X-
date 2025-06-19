<?php
require_once '../config.php';
session_start();
$landlord_id =  $_SESSION['user_id'];

// Xử lý xác nhận/hủy nếu có action
if (isset($_GET['action']) && isset($_GET['appointment_id'])) {
    $appointment_id = intval($_GET['appointment_id']);
    $action = $_GET['action'];

    if ($action === 'confirm') {
        $stmt = $conn->prepare("UPDATE view_appointment SET status = 'confirmed' WHERE appointment_id = ? AND landlord_id = ?");
        $stmt->bind_param("ii", $appointment_id, $landlord_id);
        $stmt->execute();
    }

    if ($action === 'cancel') {
        $stmt = $conn->prepare("UPDATE view_appointment SET status = 'cancelled' WHERE appointment_id = ? AND landlord_id = ?");
        $stmt->bind_param("ii", $appointment_id, $landlord_id);
        $stmt->execute();
    }

    // ✅ Chuyển hướng lại chính trang này để tránh lặp hành động khi refresh
    header("Location: ../host.php?page_layout=manage_appointment");
    exit;
}
