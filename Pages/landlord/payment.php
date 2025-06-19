<?php
    include 'config.php';

    $landlord_id = $_SESSION['user_id'];

    $sql = "
        SELECT 
            p.payment_id,
            p.payment_cycle,
            p.period_start,
            p.period_end,
            p.due_date,
            p.amount_due,
            p.paid_at,
            p.payment_status,
            rc.contract_id,
            u.name AS tenant_name,
            r.room_name
        FROM qlktx.payment p
        JOIN qlktx.rental_contract rc ON p.contract_id = rc.contract_id
        JOIN qlktx.user u ON rc.tenant_id = u.user_id
        JOIN qlktx.room r ON rc.room_id = r.room_id
        WHERE rc.landlord_id = ?
        ORDER BY p.due_date DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $landlord_id);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<div class="container-fluid py-4">
    <h2 class="mb-4 text-center">Danh sách hóa đơn</h2>

    <?php while($row = $result->fetch_assoc()): ?>
        <?php
            $statusClass = match($row['payment_status']) {
                'paid' => 'success',
                'unpaid' => 'warning',
                'late' => 'danger',
                default => 'secondary'
            };
        ?>
        <div class="card mb-3 shadow-sm border-start border-4 border-<?= explode(' ', $statusClass)[0] ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Hóa đơn </h5>
                    <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($row['payment_status']) ?></span>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Người thuê:</strong> <?= htmlspecialchars($row['tenant_name']) ?></p>
                        <p class="mb-1"><strong>Phòng:</strong> <?= htmlspecialchars($row['room_name']) ?></p>
                        <p class="mb-1"><strong>Chu kỳ thanh toán:</strong> <?= ucfirst($row['payment_cycle']) ?></p>
                        <p class="mb-1"><strong>Thời gian:</strong> <?= date('d/m/Y', strtotime($row['period_start'])) ?> → <?= date('d/m/Y', strtotime($row['period_end'])) ?></p>
                    </div>

                    <div class="col-md-6">
                        <p class="mb-1"><strong>Hạn thanh toán:</strong> <?= date('d/m/Y', strtotime($row['due_date'])) ?></p>
                        <p class="mb-1"><strong>Số tiền cần trả:</strong> <?= number_format($row['amount_due'], 0, ',', '.') ?> VND</p>
                        <p class="mb-1"><strong>Ngày thanh toán:</strong> <?= $row['paid_at'] ? date('d/m/Y', strtotime($row['paid_at'])) : '-' ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
