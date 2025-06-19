<?php
// Nhúng file cấu hình kết nối
require_once 'config.php';

// Kiểm tra và xử lý các hành động từ AJAX
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'getProvinces':
            $sql = "SELECT * FROM province";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['province_id'] . "'>" . $row['name'] . "</option>";
            }
            break;

        case 'getDistricts':
            if (isset($_GET['province_id'])) {
                $province_id = intval($_GET['province_id']);
                $sql = "SELECT * FROM district WHERE province_id = $province_id";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['district_id'] . "'>" . $row['name'] . "</option>";
                }
            }
            break;

        case 'getWards':
            if (isset($_GET['district_id'])) {
                $district_id = intval($_GET['district_id']);
                $sql = "SELECT * FROM wards WHERE district_id = $district_id";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['wards_id'] . "'>" . $row['name'] . "</option>";
                }
            }
            break;
    }
}

// Đóng kết nối sau khi xử lý xong
$conn->close();
?>
