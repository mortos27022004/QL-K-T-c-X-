<div class="row p-5 pt-0"  >



    <!-- FORM LỌC -->
    <form id="search-form" method="GET" class="mb-4 row g-3">
        <div class="form-group col-4 col-lg-2">
            <select id="province" name="province" class="form-control">
                <option value="">Chọn tỉnh</option>
            </select>
        </div>
        <div class="form-group col-4 col-lg-2">
            <select id="district" name="district" class="form-control">
                <option value="">Chọn quận/huyện</option>
            </select>
        </div>
        <div class="form-group col-4 col-lg-2">
            <select id="ward" name="ward" class="form-control">
                <option value="">Chọn phường/xã</option>
            </select>
        </div>
        <div class="col-md-6 col-lg-3">
            <input type="number" name="min_price" class="form-control" placeholder="Giá thấp nhất" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
        </div>
        <div class="col-md-6 col-lg-3">
            <input type="number" name="max_price" class="form-control" placeholder="Giá cao nhất" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
        </div>
    </form>
    <div class="row" id="search-results">

    </div>
    
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        $.get("location.php", { action: "getProvinces" }, function(data) {
            $("#province").append(data);
        });
        
        $("#province").change(function() {
            const provinceId = $(this).val();
            $.get("location.php", { action: "getDistricts", province_id: provinceId }, function(data) {
                $("#district").html('<option value="">Chọn quận/huyện</option>' + data);
            });
        });

        $("#district").change(function() {
            const districtId = $(this).val();
            $.get("location.php", { action: "getWards", district_id: districtId }, function(data) {
                $("#ward").html('<option value="">Chọn xã/phường</option>' + data);
            });
        });
   
    
        loadRooms();

        $('#search-form').on('change', 'select', function () {
            loadRooms();
        });

        $('#search-form input[name="min_price"], #search-form input[name="max_price"]').on('input', function () {
            loadRooms();
        });


        function loadRooms() {
            let formData = $('#search-form').serialize();
            $.get('controller/search_ajax.php', formData, function (data) {
                $('#search-results').html(data);
            });
        }
    });
</script>
