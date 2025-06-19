<?php
require_once '../config.php';
session_start();



$tenant_id = $_SESSION['user_id'];
$room_id = intval($_POST['room_id']);
$scheduled_time = $_POST['scheduled_time'];
$notes = $_POST['notes'] ?? '';

$sql = "INSERT INTO view_appointment (tenant_id, room_id, landlord_id, scheduled_time, status, notes, created_at)
        SELECT ?, ?, room.landlord_id, ?, 'pending', ?, NOW()
        FROM room WHERE room_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $tenant_id, $room_id, $scheduled_time, $notes, $room_id);

$stmt->execute();
header("Location: ../index.php?id=" . $room_id);
?>