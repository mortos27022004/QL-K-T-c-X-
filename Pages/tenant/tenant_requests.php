<?php
require_once __DIR__ . '/../../config.php';

// Kiểm tra xem người dùng đã đăng nhập và có phải là tenant không
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: ../login.php");
    exit;
}

$tenant_id = $_SESSION['user_id'];

// Lấy danh sách yêu cầu thuê phòng của người dùng
$sql = "SELECT 
            rr.*, 
            r.room_name, 
            r.address, 
            r.price,
            rc.contract_id,
            rc.start_date, 
            rc.end_date, 
            rc.rent_amount, 
            rc.deposit_amount,
            rc.status AS contract_status, 
            rc.payment_cycle
        FROM rental_request rr
        JOIN room r ON rr.room_id = r.room_id
        LEFT JOIN rental_contract rc ON rr.request_id = rc.request_id
        WHERE rr.tenant_id = ?
        ORDER BY rr.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="p-5 container-fluid">
    <h3 class="mb-3">Yêu cầu thuê phòng của tôi</h3>
    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4 p-5">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['room_name']) ?></h5>
                            <p class="card-text"><strong>Địa chỉ:</strong> <?= htmlspecialchars($row['address']) ?></p>
                            <p class="card-text"><strong>Lời nhắn:</strong> <?= nl2br(htmlspecialchars($row['message'])) ?></p>
                            <p class="card-text mb-1">
                                <strong>Trạng thái:</strong>
                                <span class="badge bg-<?= 
                                    $row['status'] === 'pending' ? 'warning' :
                                    ($row['status'] === 'approved' ? 'success' : 
                                    ($row['status'] === 'rejected' ? 'danger' : 'secondary')) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </p>
                            <p class="text-muted small">Gửi lúc: <?= $row['created_at'] ?></p>
                            <?php if (!empty($row['contract_id'])): ?>
                                <div>
                                    <?php if ($row['contract_status'] === 'pending'): ?>
                                        <button class="text-white btn btn-sm btn-warning w-100" data-bs-toggle="modal" data-bs-target="#viewContractModal<?= $row['contract_id'] ?>">Xác nhận hợp đồng</button>
                                    <?php elseif ($row['contract_status'] === 'active'): ?>
                                        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#viewContractModal<?= $row['contract_id'] ?>">Xem hợp đồng</button>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($row['contract_id'])): ?>
                                    <div class="modal fade" id="viewContractModal<?= $row['contract_id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title">Thông tin hợp đồng</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                            <p><strong>Ngày bắt đầu:</strong> <?= htmlspecialchars($row['start_date']) ?></p>
                                            <p><strong>Ngày kết thúc:</strong> <?= htmlspecialchars($row['end_date']) ?></p>
                                            <p><strong>Tiền thuê:</strong> <?= number_format($row['rent_amount'], 0, ',', '.') ?> VNĐ</p>
                                            <p><strong>Tiền cọc:</strong> <?= number_format($row['deposit_amount'], 0, ',', '.') ?> VNĐ</p>
                                            <p><strong>Chu kỳ thanh toán:</strong> 
                                                <?= 
                                                $row['payment_cycle'] === 'monthly' ? 'Hàng tháng' :
                                                ($row['payment_cycle'] === 'quarterly' ? 'Hàng quý' : 'Hàng năm') 
                                                ?>
                                            </p>
                                            <p><strong>Trạng thái hợp đồng:</strong> 
                                                <span class="badge bg-<?= 
                                                    $row['contract_status'] === 'pending' ? 'warning' :
                                                    ($row['contract_status'] === 'active' ? 'success' :
                                                    ($row['contract_status'] === 'terminated' ? 'danger' : 'secondary')) ?>">
                                                    <?= ucfirst($row['contract_status']) ?>
                                                </span>
                                            </p>
                                            </div>
                                            <div class="modal-footer">
                                                <?php if ($row['contract_status'] === 'pending'): ?>
                                                    <form action="controller/confirm_contract.php" method="post" class="d-inline">
                                                        <input type="hidden" name="contract_id" value="<?= $row['contract_id'] ?>">
                                                        <button type="submit" class="btn btn-primary">Xác nhận hợp đồng</button>
                                                    </form>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div>
                                    <button class="btn btn-sm btn-secondary w-100" disabled>Chờ xác nhận</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Bạn chưa gửi yêu cầu thuê phòng nào.</div>
    <?php endif; ?>

</div>
