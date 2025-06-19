<?php
require_once 'config.php';

$isEdit = isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']);
$roomData = null;
$province_id = $district_id = $ward_id = '';

if ($isEdit) {
    $room_id = (int)$_GET['id'];
    $sql = "SELECT * FROM room WHERE room_id = $room_id";
    $result = $conn->query($sql);
    $roomData = $result->fetch_assoc();

    $ward_id = $roomData['ward_id'];
    $ward_sql = "SELECT wards_id, district_id FROM wards WHERE wards_id = $ward_id";
    $ward = $conn->query($ward_sql)->fetch_assoc();
    $district_id = $ward['district_id'];

    $district_sql = "SELECT province_id FROM district WHERE district_id = $district_id";
    $district = $conn->query($district_sql)->fetch_assoc();
    $province_id = $district['province_id'];
}
$currentImages = [];
if ($isEdit) {
    $room_id = (int)$_GET['id'];
    $img_sql = "SELECT image_url FROM room_img WHERE room_id = $room_id";
    $img_result = $conn->query($img_sql);
    while ($row = $img_result->fetch_assoc()) {
        $currentImages[] = $row['image_url'];
    }
}
?>

<div class="container">
    <div class="row">
        <h1 class="text-center mb-3"><?= $isEdit ? 'Sửa Phòng' : 'Thêm Phòng' ?></h1>
        <form action="controller/<?= $isEdit ? 'edit_room.php' : 'add_room.php' ?>" method="POST" enctype="multipart/form-data">
            <?php if ($isEdit): ?>
                <input type="hidden" name="room_id" value="<?= $roomData['room_id'] ?>">
            <?php endif; ?>

            <div class="row mb-1">
                <div class="form-group col-4">
                    <label for="room_name" class="form-label">Tên Phòng:</label>
                    <input type="text" class="form-control" id="room_name" name="room_name"
                           value="<?= $isEdit ? htmlspecialchars($roomData['room_name']) : '' ?>" required>
                </div>

                <div class="form-group col-8">
                    <label for="title" class="form-label">Tiêu đề:</label>
                    <input type="text" class="form-control" id="title" name="title"
                           value="<?= $isEdit ? htmlspecialchars($roomData['title']) : '' ?>" required>
                </div>

                <div class="form-group col-4">
                    <label for="province" class="form-label">Tỉnh/Thành phố</label>
                    <select id="province" name="province" class="form-control" required>
                        <option value="">Chọn một tỉnh</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="district" class="form-label">Quận/Huyện</label>
                    <select id="district" name="district" class="form-control" required>
                        <option value="">Chọn một quận/huyện</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="ward" class="form-label">Phường/Xã</label>
                    <select id="ward" name="ward" class="form-control" required>
                        <option value="">Chọn một xã</option>
                    </select>
                </div>

                <div class="form-group col-8">
                    <label for="address" class="form-label">Địa chỉ:</label>
                    <input type="text" class="form-control" id="address" name="address"
                           value="<?= $isEdit ? htmlspecialchars($roomData['address']) : '' ?>" required>
                </div>

                <div class="form-group col-4">
                    <label for="price" class="form-label">Giá thuê:</label>
                    <input type="text" class="form-control" id="price" name="price"
                           value="<?= $isEdit ? htmlspecialchars($roomData['price']) : '' ?>" required>
                </div>

                <div class="form-group col-12">
                    <label for="description" class="form-label">Mô tả:</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= $isEdit ? htmlspecialchars($roomData['description']) : '' ?></textarea>
                </div>

                <div class="form-group col-12">
                    <label for="images">Ảnh phòng:</label>
                    <input type="file" class="form-control mb-2" id="images" name="images[]" multiple accept="image/*">

                    <?php if ($isEdit && !empty($currentImages)): ?>
                        <label>Ảnh hiện tại:</label>
                        <div class="row">
                            <?php foreach ($currentImages as $index => $img): ?>
                                <div class="col-4 mb-2 position-relative">
                                    <a href="images/room/<?= htmlspecialchars($img) ?>" target="_blank">
                                        <img src="images/room/<?= htmlspecialchars($img) ?>" class="img-fluid rounded border" alt="Ảnh phòng">
                                    </a>
                                    <div class="position-absolute top-0 end-0">
                                        <input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($img) ?>" class="form-check-input mt-2 me-2">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Tick ảnh muốn xóa</small>
                    <?php endif; ?>
                </div>


                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Cập nhật' : 'Xác nhận' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Script chọn địa điểm -->
<script>
    $(document).ready(function() {
        let provinceId = <?= $province_id ?>;
        let districtId = <?= $district_id ?>;
        let wardId = <?= $ward_id ?>;

        // Load tỉnh
        $.get("location.php", { action: "getProvinces" }, function(data) {
            $("#province").append(data);
            $("#province").val(provinceId).trigger('change');
        });

        // Khi chọn tỉnh → load quận
        $("#province").on('change', function() {
            const selectedProvinceId = $(this).val();
            $("#district").html('<option value="">Đang tải...</option>');
            $("#ward").html('<option value="">Chọn xã/phường</option>');

            $.get("location.php", { action: "getDistricts", province_id: selectedProvinceId }, function(data) {
                $("#district").html('<option value="">Chọn quận/huyện</option>' + data);

                // Nếu đang sửa và người dùng chưa thay đổi → gán districtId cũ
                if (selectedProvinceId == provinceId) {
                    $("#district").val(districtId).trigger('change');
                }
            });
        });

        // Khi chọn quận → load xã
        $("#district").on('change', function() {
            const selectedDistrictId = $(this).val();
            $("#ward").html('<option value="">Đang tải...</option>');

            $.get("location.php", { action: "getWards", district_id: selectedDistrictId }, function(data) {
                $("#ward").html('<option value="">Chọn xã/phường</option>' + data);

                // Nếu đang sửa và người dùng chưa thay đổi → gán wardId cũ
                if (selectedDistrictId == districtId) {
                    $("#ward").val(wardId);
                }
            });
        });
    });
</script>
