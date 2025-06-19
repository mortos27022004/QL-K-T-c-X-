
<div class="container">
    <div class="row">
        <h1 class="text-center mb-3">Thêm Phòng</h1>
        <form action="controller/add_room.php" method="POST" enctype="multipart/form-data">
            <div class="row mb-1">
                <div class = "form-group col-4">
                    <label for="room_name" class="form-label">Tên Phòng:</label>
                    <input type="text" class="form-control" id="room_name" name="room_name" required>
                </div>
        
                <div class = "form-group col-8">
                    <label for="title" class="form-label">Tiêu đề:</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Nguyễn Đình Tuấn" required>
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

                <div class = "form-group col-8">
                    <label for="address" class="form-label">Địa chỉ:</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Nguyễn Đình Tuấn" required>
                </div>

                <div class = "form-group col-4">
                    <label for="price" class="form-label">Giá thuê:</label>
                    <input type="text" class="form-control" id="price" name="price" placeholder="Nguyễn Đình Tuấn" required>
                </div>

                <div class="form-group col-12">
                    <label for="description" class="form-label">Mô tả:</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Nguyễn Đình Tuấn" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="images">Ảnh phòng:</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Xác nhận</button>
                </div>
                
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

 <!-- Script chọn địa điểm -->
<script>
    $(document).ready(function() {
        // Load tỉnh
        $.get("location.php", { action: "getProvinces" }, function(data) {
            $("#province").append(data);
        });

        // Khi chọn tỉnh → load quận
        $("#province").change(function() {
            const provinceId = $(this).val();
            $.get("location.php", { action: "getDistricts", province_id: provinceId }, function(data) {
                $("#district").html('<option value="">Chọn quận/huyện</option>' + data);
                $("#ward").html('<option value="">Chọn xã/phường</option>');
            });
        });

        // Khi chọn quận → load xã
        $("#district").change(function() {
            const districtId = $(this).val();
            $.get("location.php", { action: "getWards", district_id: districtId }, function(data) {
                $("#ward").html('<option value="">Chọn xã/phường</option>' + data);
            });
        });
    });
</script>