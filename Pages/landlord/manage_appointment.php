<?php
    require_once 'config.php'; // Gọi kết nối từ file config.php

    // Giả lập ID chủ trọ đang đăng nhập
    $landlord_id =  $_SESSION['user_id'];

    // Truy vấn dữ liệu các yêu cầu xem phòng
    $sql = "SELECT 
            va.*, 
            r.room_name, 
            r.address, 
            u.name AS tenant_name, 
            ri.image_url
        FROM view_appointment va
        JOIN room r ON va.room_id = r.room_id
        JOIN user u ON va.tenant_id = u.user_id
        LEFT JOIN (
            SELECT room_id, MIN(img_id) AS first_img_id
            FROM room_img
            GROUP BY room_id
        ) first_img ON r.room_id = first_img.room_id
        LEFT JOIN room_img ri ON ri.img_id = first_img.first_img_id
        WHERE va.landlord_id = ?
        ORDER BY va.scheduled_time DESC";

    $stmt = $conn->prepare($sql);  // dùng $conn cho MySQLi
    $stmt->bind_param("i", $landlord_id); // "i" = integer
    $stmt->execute();

    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-5">
  <h2 class="mb-4">Danh sách yêu cầu xem phòng</h2>

  <div class="row row-cols-1 row-cols-md-2 g-4">
    <?php foreach ($appointments as $app): ?>
      <div class="col-12 col-lg-6 col-xl-4 ">
        <div class="card h-100 shadow-sm">
            <img src="images/room/<?= htmlspecialchars($app['image_url'] ?? 'https://via.placeholder.com/400x200') ?>" class="card-img-top" alt="Ảnh phòng">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($app['room_name']) ?></h5>
                <p class="card-text mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($app['address']) ?></p>
                <p class="card-text mb-1"><strong>Người yêu cầu:</strong> <?= htmlspecialchars($app['tenant_name']) ?></p>
                <p class="card-text mb-1"><strong>Thời gian hẹn:</strong> <?= htmlspecialchars($app['scheduled_time']) ?></p>
                <p class="card-text"><strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($app['notes'])) ?></p>

                <?php if ($app['status'] === 'pending'){ ?>
                    <div class="d-flex gap-2 mt-3">
                        <a href="controller/manage_appointment.php?action=confirm&appointment_id=<?= $app['appointment_id'] ?>" 
                        class="btn btn-success w-50 text-center">
                        Xác nhận
                        </a>
                        <a href="controller/manage_appointment.php?action=cancel&appointment_id=<?= $app['appointment_id'] ?>" 
                        class="btn btn-danger w-50 text-center">
                        Hủy
                        </a>
                    </div>
                <?php }else if($app['status'] === 'confirmed'){ ?>
                    <div class="d-flex gap-2 mt-3">
                        <div class="btn btn-outline-success w-50 text-center">Đã xác nhận</div>
                        <a href="controller/manage_appointment.php?action=cancel&appointment_id=<?= $app['appointment_id'] ?>" 
                            class="btn btn-danger w-50 text-center">
                            Hủy
                        </a>
                    </div>
                <?php }else if($app['status'] === 'cancelled'){ ?>
                    <div class="alert alert-danger mt-3 p-2 mb-0 text-center">Đã hủy</div>
                <?php } ?>
            </div>
        </div>

      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($appointments) === 0): ?>
    <p class="text-muted mt-3">Không có yêu cầu xem phòng nào.</p>
  <?php endif; ?>
</div>
