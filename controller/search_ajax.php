<?php
require_once '../config.php';

$sql = "SELECT room.*, wards.name AS ward_name, district.name AS district_name, province.name AS province_name
        FROM room 
        JOIN wards ON room.ward_id = wards.wards_id
        JOIN district ON wards.district_id = district.district_id
        JOIN province ON district.province_id = province.province_id
        WHERE 1";

if (!empty($_GET['min_price'])) {
    $min_price = (int)$_GET['min_price'];
    $sql .= " AND room.price >= $min_price";
}
if (!empty($_GET['max_price'])) {
    $max_price = (int)$_GET['max_price'];
    $sql .= " AND room.price <= $max_price";
}
if (!empty($_GET['ward'])) {
    $ward_id = (int)$_GET['ward'];
    $sql .= " AND room.ward_id = $ward_id";
} else if (!empty($_GET['district'])) {
    $district_id = (int)$_GET['district'];
    $sql .= " AND wards.district_id = $district_id";
} else if (!empty($_GET['province'])) {
    $province_id = (int)$_GET['province'];
    $sql .= " AND province.province_id = $province_id";
}

$sql .= " ORDER BY room.room_id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0):
    while($row = $result->fetch_assoc()):
        $room_id = $row['room_id'];
        $img_sql = "SELECT image_url FROM room_img WHERE room_id = $room_id LIMIT 1";
        $img_result = $conn->query($img_sql);
        $img_row = $img_result->fetch_assoc();
        $img_url = $img_row ? $img_row['image_url'] : "images/default.jpg";
?>
    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="card room-card shadow-sm">
            <img src="images/room/<?= htmlspecialchars($img_url) ?>" class="card-img-top" alt="Ảnh phòng">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                <p class="card-text">
                    <strong>Tên phòng:</strong> <?= htmlspecialchars($row['room_name']) ?><br>
                    <strong>Giá thuê:</strong> <?= number_format($row['price'], 0, ',', '.') ?> VNĐ<br>
                    <strong>Địa chỉ:</strong> <?= htmlspecialchars($row['address']) ?>
                </p>
                <p class="card-text">
                    <?= nl2br(htmlspecialchars(substr($row['description'], 0, 100))) ?>...
                </p>
                <a href="index.php?id=<?= htmlspecialchars($room_id) ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
            </div>
        </div>
    </div>
<?php
    endwhile;
else:
?>
    <div class="col-12">
        <div class="alert alert-info">Không tìm thấy kết quả phù hợp.</div>
    </div>
<?php endif; ?>
