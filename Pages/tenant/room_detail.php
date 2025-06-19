<?php
  require_once 'config.php';

  if (!isset($_GET['id'])) {
      echo "Không tìm thấy phòng.";
      exit;
  }

  $room_id = intval($_GET['id']);

  // Lấy thông tin phòng kèm landlord
  $sql = "SELECT room.*, user.name AS landlord_name, user.phone AS landlord_phone
          FROM room
          JOIN user ON room.landlord_id = user.user_id
          WHERE room.room_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $room_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
      echo "Phòng không tồn tại.";
      exit;
  }

  $room = $result->fetch_assoc();

  // Lấy ảnh đầu tiên từ bảng room_img
  $sql_img = "SELECT image_url FROM room_img WHERE room_id = ? ORDER BY uploaded_at ASC LIMIT 1";
  $stmt_img = $conn->prepare($sql_img);
  $stmt_img->bind_param("i", $room_id);
  $stmt_img->execute();
  $result_img = $stmt_img->get_result();

  $image_url = 'images/Ironbridge+-+Website+Icon+-+Landlord+Representation.jpg'; // ảnh mặc định
  if ($row_img = $result_img->fetch_assoc()) {
      $image_url = 'images/room/' . htmlspecialchars($row_img['image_url']);
  }
?>

<div class="container-fluid p-5">
    <div class="card mb-3">
        <div class="row g-0">
            <div class="col-lg-5">
                <img src="<?= $image_url ?>" 
                    alt="Ảnh phòng" 
                    class="img-fluid rounded-start w-100 h-100 object-fit-cover" 
                    >
            </div>
            <div class="col-lg-7">
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($room['title']) ?></h3>
                    <p class="card-text"><strong>Tên phòng:</strong> <?= htmlspecialchars($room['room_name']) ?></p>
                    <p class="card-text"><strong>Giá thuê:</strong> <?= number_format($room['price'], 0, ',', '.') ?> VNĐ</p>
                    <p class="card-text"><strong>Địa chỉ:</strong> <?= htmlspecialchars($room['address']) ?></p>
                    <p class="card-text"><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($room['description'])) ?></p>
                    <p class="card-text"><strong>Trạng thái:</strong> 
                        <span class="badge bg-<?= $room['status'] === 'available' ? 'success' : ($room['status'] === 'occupied' ? 'danger' : 'warning') ?>">
                            <?= ucfirst($room['status']) ?>
                        </span>
                    </p>
                    <hr>
                    <p class="card-text"><strong>Người cho thuê:</strong> <?= htmlspecialchars($room['landlord_name']) ?> - <?= htmlspecialchars($room['landlord_phone']) ?></p>
                    <p class="card-text"><small class="text-muted">Đăng lúc: <?= $room['created_at'] ?></small></p>
                    <div class="d-flex gap-3 mt-3">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#rentRequestModal">
                            Yêu cầu thuê phòng
                        </button>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#viewRequestModal">
                            Yêu cầu xem phòng
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <iframe
      width="100%"
      height="350"
      frameborder="0"
      style="border:0"
      src="https://www.google.com/maps?q=<?php echo $room['address']; ?>&output=embed"
      allowfullscreen>
    </iframe>

</div>

<div class="modal fade" id="rentRequestModal" tabindex="-1" aria-labelledby="rentRequestModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="controller/rent_request.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rentRequestModalLabel">Gửi yêu cầu thuê phòng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
          <div class="mb-3">
            <label for="message" class="form-label">Lời nhắn:</label>
            <textarea name="message" id="message" class="form-control" rows="4" placeholder="Nhập lời nhắn..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="controller/add_view_request.php" method="POST">
      <input type="hidden" name="room_id" value="<?= $room_id ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Yêu cầu xem phòng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="scheduled_time" class="form-label">Thời gian mong muốn</label>
            <input type="datetime-local" class="form-control" name="scheduled_time" required>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">Ghi chú (tuỳ chọn)</label>
            <textarea class="form-control" name="notes" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
        </div>
      </div>
    </form>
  </div>
</div>