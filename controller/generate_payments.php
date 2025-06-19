<?php
require_once '../config.php';

// 1. Cập nhật trạng thái hợp đồng
$update_contract_status = "
    UPDATE rental_contract
    SET status = CASE
        WHEN CURDATE() > end_date THEN 'expired'
        WHEN CURDATE() BETWEEN start_date AND end_date AND status != 'terminated' THEN 'active'
        WHEN CURDATE() < start_date THEN 'pending'
        ELSE status
    END
";
$conn->query($update_contract_status);

// 2. Tạo hóa đơn cho hợp đồng đang active
$sql = "
    SELECT contract_id, payment_cycle, start_date, end_date, rent_amount
    FROM rental_contract
    WHERE status = 'active'
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $contract_id = $row["contract_id"];
        $cycle = $row["payment_cycle"];
        $start_date = $row["start_date"];
        $end_date = $row["end_date"];
        $amount = $row["rent_amount"];

        // Kiểm tra hóa đơn gần nhất
        $last = $conn->query("
            SELECT MAX(period_end) AS last_end FROM payment
            WHERE contract_id = $contract_id
        ")->fetch_assoc();

        $period_start = $last["last_end"] ? date('Y-m-d', strtotime($last["last_end"] . ' +1 day')) : $start_date;

        if ($cycle == 'monthly') $period_end = date('Y-m-d', strtotime($period_start . ' +1 month'));
        elseif ($cycle == 'quarterly') $period_end = date('Y-m-d', strtotime($period_start . ' +3 month'));
        elseif ($cycle == 'yearly') $period_end = date('Y-m-d', strtotime($period_start . ' +1 year'));

        // Nếu còn nằm trong thời gian hợp đồng
        if ($period_start <= $end_date) {
            $stmt = $conn->prepare("
                INSERT INTO payment (contract_id, payment_cycle, period_start, period_end, due_date, amount_due, payment_status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'unpaid', NOW(), NOW())
            ");
            $stmt->bind_param("issssd", $contract_id, $cycle, $period_start, $period_end, $period_end, $amount);
            $stmt->execute();
        }
    }
}

echo "✅ Đã xử lý cập nhật trạng thái hợp đồng và tạo hóa đơn tự động.";
$conn->close();
?>
