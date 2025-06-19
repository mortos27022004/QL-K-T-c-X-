<?php 
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        die("Vui lòng đăng nhập trước khi tạo hợp đồng.");
    }

    // Lấy dữ liệu từ form
    $request_id     = intval($_POST['request_id']);
    $start_date     = $_POST['start_date'];
    $end_date       = $_POST['end_date'];
    $rent_amount    = floatval($_POST['rent_amount']);
    $deposit_amount = floatval($_POST['deposit_amount']);
    $payment_cycle  = $_POST['payment_cycle'];

    $landlord_id = intval($_SESSION['user_id']);

    // Lấy thông tin yêu cầu thuê
    $stmt = $conn->prepare("SELECT tenant_id, room_id FROM rental_request WHERE request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Yêu cầu thuê không tồn tại.");
    }

    $row = $result->fetch_assoc();
    $tenant_id = $row['tenant_id'];
    $room_id   = $row['room_id'];

    // Tạo hợp đồng thuê
    $stmt = $conn->prepare("INSERT INTO rental_contract (
        request_id, tenant_id, landlord_id, room_id, start_date, end_date, rent_amount, deposit_amount, payment_cycle
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("iiiissdds", $request_id, $tenant_id, $landlord_id, $room_id, $start_date, $end_date, $rent_amount, $deposit_amount, $payment_cycle);

    if ($stmt->execute()) {
        // ✅ Cập nhật trạng thái yêu cầu thuê
        $update = $conn->prepare("UPDATE rental_request SET status = 'approved' WHERE request_id = ?");
        $update->bind_param("i", $request_id);
        $update->execute();

        // ✅ Cập nhật trạng thái phòng → 'occupied'
        $updateRoom = $conn->prepare("UPDATE room SET status = 'occupied' WHERE room_id = ?");
        $updateRoom->bind_param("i", $room_id);
        $updateRoom->execute();

        // ✅ Tạo hóa đơn thanh toán định kỳ
        require_once 'generate_payments.php';

        // ✅ Chuyển hướng sau khi thành công
        header("Location: ../host.php?page_layout=manage_rent_request");
        exit;
    } else {
        echo "❌ Lỗi khi tạo hợp đồng: " . $stmt->error;
    }
} else {
    echo "❌ Phương thức không hợp lệ.";
}
?>
