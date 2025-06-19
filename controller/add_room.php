<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $room_name = $_POST['room_name'];
    $title = $_POST['title'];
    $ward = $_POST['ward']; // ward_id
    $address = $_POST['address']; // địa chỉ chi tiết (số nhà, đường)
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Truy vấn để lấy tên ward, district, province
    $stmt_location = $conn->prepare("
        SELECT w.name AS ward_name, d.name AS district_name, p.name AS province_name
        FROM wards w
        JOIN district d ON w.district_id = d.district_id
        JOIN province p ON d.province_id = p.province_id
        WHERE w.wards_id = ?
    ");
    $stmt_location->bind_param("i", $ward);
    $stmt_location->execute();
    $result_location = $stmt_location->get_result();
    $location_data = $result_location->fetch_assoc();
    $stmt_location->close();

    // Địa chỉ đầy đủ
    $full_address = $address . ', ' . $location_data['ward_name'] . ', ' . $location_data['district_name'] . ', ' . $location_data['province_name'];

    // Xử lý upload ảnh
    $image_filenames = [];
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = "../images/room/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['images']['name'] as $key => $nameImg) {
            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $new_filename = uniqid() . "_" . basename($nameImg);
            $targetPath = $uploadDir . $new_filename;

            if (move_uploaded_file($tmp_name, $targetPath)) {
                $image_filenames[] = $new_filename;
            }
        }
    }
    $status = 'available';
    session_start();
    // Lưu thông tin phòng vào bảng room với địa chỉ đầy đủ
    $stmt = $conn->prepare("INSERT INTO room (landlord_id, room_name, title, address, ward_id, price, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiiss", $_SESSION['user_id'], $room_name, $title, $full_address, $ward, $price, $description, $status);

    if ($stmt->execute()) {
        $room_id = $stmt->insert_id;

        // Lưu các ảnh vào bảng room_img
        foreach ($image_filenames as $filename) {
            $stmt_img = $conn->prepare("INSERT INTO room_img (room_id, image_url) VALUES (?, ?)");
            $stmt_img->bind_param("is", $room_id, $filename);
            $stmt_img->execute();
        }

        echo "<script>
            alert('Thêm phòng thành công!');
            window.location.href = '../host.php?page_layout=manage_room';
        </script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
