<?php
    include 'config.php';

    $tenant_id = $_SESSION['user_id'];

    $sql = "
        SELECT 
            rc.contract_id,
            r.title AS room_title,
            l.name AS landlord_name,
            rc.start_date,
            rc.end_date,
            rc.rent_amount,
            rc.deposit_amount,
            rc.payment_cycle,
            rc.status,
            rc.signed_at,
            rc.notes
        FROM qlktx.rental_contract rc
        JOIN qlktx.room r ON rc.room_id = r.room_id
        JOIN qlktx.user l ON rc.landlord_id = l.user_id
        WHERE rc.tenant_id = ?
        ORDER BY rc.signed_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<div class="container-fluid p-5">
    <h2 class="mb-4 text-center">Hợp đồng của bạn</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $badgeClass = match ($row['status']) {
                        'active' => 'success',
                        'terminated' => 'danger',
                        'expired' => 'secondary',
                        'pending' => 'warning',
                        default => 'dark',
                    };
                ?>
                <div class="col">
                    <div class="card border-start border-4 border-<?= $badgeClass ?> shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['room_title']) ?></h5>
                            <p class="mb-1"><strong>Chủ nhà:</strong> <?= htmlspecialchars($row['landlord_name']) ?></p>
                            <p class="mb-1"><strong>Thời hạn:</strong> <?= date('d/m/Y', strtotime($row['start_date'])) ?> → <?= date('d/m/Y', strtotime($row['end_date'])) ?></p>
                            <p class="mb-1"><strong>Giá thuê:</strong> <?= number_format($row['rent_amount'], 0, ',', '.') ?> đ / <?= $row['payment_cycle'] ?></p>
                            <p class="mb-1"><strong>Tiền cọc:</strong> <?= number_format($row['deposit_amount'], 0, ',', '.') ?> đ</p>
                            <p class="mb-1"><strong>Trạng thái:</strong> <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($row['status']) ?></span></p>
                            <p class="mb-1"><strong>Ngày ký:</strong> <?= date('H:i d/m/Y', strtotime($row['signed_at'])) ?></p>

                            <?php if (!empty($row['notes'])): ?>
                                <div class="mt-2">
                                    <strong>Ghi chú:</strong><br>
                                    <?= nl2br(htmlspecialchars($row['notes'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Bạn chưa có hợp đồng thuê nào.</div>
    <?php endif; ?>
</div>
