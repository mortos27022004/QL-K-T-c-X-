<?php
    require_once 'config.php';
    $landlord_id = $_SESSION['user_id'];

    $sql = "SELECT rr.request_id as request_id, rr.message, rr.status, rr.created_at, u.name AS tenant_name, r.room_name AS room_name
            FROM rental_request rr
            JOIN user u ON rr.tenant_id = u.user_id
            JOIN room r ON rr.room_id = r.room_id
            WHERE r.landlord_id = ?
            ORDER BY rr.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $landlord_id);
    $stmt->execute();
    $result = $stmt->get_result();
?>
<div class="container-fluid p-5">
    <h2 class="mb-4">Danh sách yêu cầu thuê phòng</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()):?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                      <div class="card-body d-flex flex-column">
                          <h5 class="card-title"><?= htmlspecialchars($row['tenant_name']) ?></h5>
                          <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($row['room_name']) ?></h6>
                          <p class="card-text mt-2" style="white-space: pre-wrap"><?= htmlspecialchars($row['message']) ?></p>
                          <p class="mb-1 mt-auto">
                              <strong>Trạng thái:</strong>
                              <span class="badge bg-<?= 
                                  $row['status'] === 'pending' ? 'warning' :
                                  ($row['status'] === 'approved' ? 'success' : 
                                  ($row['status'] === 'rejected' ? 'danger' : 'secondary')) ?>">
                                  <?= ucfirst($row['status']) ?>
                              </span>
                          </p>
                          <p class="text-muted small mb-3">Gửi lúc: <?= $row['created_at'] ?></p>

                          <?php if ($row['status'] === 'pending'): ?>
                              <div class="d-flex gap-2 mt-auto">
                                  <button class="btn btn-sm btn-success w-50" data-bs-toggle="modal" data-bs-target="#contractModal_<?= $row['request_id'] ?>">Chấp nhận</button>
                                  <button class="btn btn-sm btn-danger w-50">Từ chối</button>
                              </div>
                          <?php else: ?>
                              <div class="alert alert-light text-center p-2 mt-auto">
                                  <em>Đã xử lý</em>
                              </div>
                          <?php endif; ?>
                      </div>
                    </div>
                </div>
                <div class="modal fade" id="contractModal_<?= $row['request_id'] ?>" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form id="contractForm_<?= $row['request_id'] ?>" action="controller/create_contract.php" method="POST">
                      <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">

                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="contractModalLabel">Tạo hợp đồng thuê nhà</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label for="startDate" class="form-label">Ngày bắt đầu</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" required>
                          </div>
                          <div class="mb-3">
                            <label for="endDate" class="form-label">Ngày kết thúc</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" required>
                          </div>
                          <div class="mb-3">
                            <label for="rentAmount" class="form-label">Tiền thuê (VNĐ)</label>
                            <input type="number" class="form-control" id="rentAmount" name="rent_amount" required>
                          </div>
                          <div class="mb-3">
                            <label for="depositAmount" class="form-label">Tiền cọc (VNĐ)</label>
                            <input type="number" class="form-control" id="depositAmount" name="deposit_amount" required>
                          </div>
                          <div class="mb-3">
                            <label for="paymentCycle" class="form-label">Chu kỳ thanh toán</label>
                            <select class="form-select" id="paymentCycle" name="payment_cycle" required>
                              <option value="monthly">Hàng tháng</option>
                              <option value="quarterly">Hàng quý</option>
                              <option value="yearly">Hàng năm</option>
                            </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                          <button type="submit" class="btn btn-primary">Gửi</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Không có yêu cầu nào.</div>
    <?php endif; ?>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Đã tạo hợp đồng thành công!</div>
<?php endif; ?>