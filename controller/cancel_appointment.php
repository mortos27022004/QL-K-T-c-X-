<?php
include '../config.php'; // file chứa kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);

    $stmt = $conn->prepare("UPDATE view_appointment SET status = 'cancelled' WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointment_id);

    $stmt->execute();
    header("Location: ../index.php?page_layout=appointment");

    $stmt->close();
    $conn->close();
}
?>
