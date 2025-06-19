<div class="container-fluid p-5">
    <h2 class="mb-4 text-center">Danh sách cuộc hẹn xem phòng</h2>
    
    <?php
        require_once 'config.php';


        $user_id = $_SESSION['user_id'];

        $sql = "
            SELECT
                va.appointment_id,
                u.name AS landlord_name,
                r.title,
                r.address,
                va.scheduled_time,
                va.notes,
                va.status,
                r.price
            FROM
                view_appointment va
            JOIN user u ON va.landlord_id = u.user_id
            JOIN room r ON va.room_id = r.room_id
            WHERE va.tenant_id = ?
            ORDER BY va.scheduled_time DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $badgeClass = match ($row['status']) {
                    'pending' => 'warning',
                    'confirmed' => 'success',
                    'cancelled' => 'danger',
                    'completed' => 'primary',
                    default => 'secondary',
                };
                ?>
                <div class="col-12 col-xl-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row justify-content-between flex-wrap">
                                <div class="col-9">
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($row['title']) ?></h5>
                                    <p class="mb-1"><strong>Chủ trọ:</strong> <?= htmlspecialchars($row['landlord_name']) ?></p>
                                    <p class="mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($row['address']) ?></p>
                                    <p class="mb-1"><strong>Giá thuê:</strong> <?= number_format($row['price'], 0, ',', '.') ?> đ</p>
                                </div>
                                <div class="text-end col-3">
                                    <p class="mb-1"><strong>Thời gian hẹn:</strong><br><?= date('H:i d/m/Y', strtotime($row['scheduled_time'])) ?></p>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>

                                    <?php if (in_array($row['status'], ['pending', 'confirmed'])): ?>
                                        <form method="post" action="controller/cancel_appointment.php" class="mt-2">
                                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                                    onclick="return confirm('Bạn có chắc chắn muốn hủy cuộc hẹn này không?')">
                                                Hủy
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($row['notes'])): ?>
                                <div class="mt-3">
                                    <strong>Ghi chú:</strong> <?= htmlspecialchars($row['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Không có cuộc hẹn nào được tìm thấy.</div>
    <?php endif; ?>
</div>
