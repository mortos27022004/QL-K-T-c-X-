<?php
    require_once 'config.php';

    // Lấy danh sách các phòng từ database
    $sql = "SELECT room.*, wards.name AS ward_name, district.name AS district_name, province.name AS province_name
            FROM room 
            JOIN wards ON room.ward_id = wards.wards_id
            JOIN district ON wards.district_id = district.district_id
            JOIN province ON district.province_id = province.province_id
            ORDER BY room.room_id DESC";

    $result = $conn->query($sql);
?>

<div class="container-fluid p-5 overflow-auto">
    <h2 class="mb-3">Quản lý phòng</h2>
    <a href="host.php?page_layout=add_room" class="btn btn-primary ">
        <i class="fa fa-plus me-1"></i> Thêm phòng
    </a>
    <h2 class="text-center mb-4">Danh sách phòng đã thêm</h2>
    <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <?php
                $room_id = $row['room_id'];
                $status = $row['status'];
                $border_class = ($status === 'occupied') ? 'border-danger' : 'border-primary';

                // Lấy ảnh đầu tiên từ bảng room_img
                $img_sql = "SELECT image_url FROM room_img WHERE room_id = $room_id LIMIT 1";
                $img_result = $conn->query($img_sql);
                $img_row = $img_result->fetch_assoc();
                $img_url = $img_row ? $img_row['image_url'] : "../default.jpg";
            ?>
            <div class="col-12 col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 shadow border-3 <?= $border_class ?>">
                    <img src="images/room/<?= htmlspecialchars($img_url) ?>" class="card-img-top" alt="Ảnh phòng">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['room_name']) ?></h5>
                        <p class="card-text"><strong>Tiêu đề:</strong> <?= htmlspecialchars($row['title']) ?></p>
                        <p class="card-text"><strong>Địa chỉ:</strong> <?= htmlspecialchars($row['address']) ?> </p>
                        <p class="card-text"><strong>Giá:</strong> <?= number_format($row['price'], 0, ',', '.') ?> VNĐ</p>
                        <p class="card-text"><?= nl2br(htmlspecialchars(substr($row['description'], 0, 100))) ?>...</p>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="host.php?page_layout=edit_room&action=edit&id=<?= $room_id ?>" class="btn btn-sm btn-primary w-50">Sửa</a>
                            <a href="controller/delete_room.php?id=<?= $room_id ?>" class="btn btn-sm btn-danger w-50">Xóa</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
