<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu phòng
    $room_id = (int)$_POST['room_id'];
    $room_name = $conn->real_escape_string($_POST['room_name']);
    $title = $conn->real_escape_string($_POST['title']);
    $province = (int)$_POST['province'];
    $district = (int)$_POST['district'];
    $ward = (int)$_POST['ward'];
    $address = $conn->real_escape_string($_POST['address']);
    $price = (int)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);

    // Cập nhật thông tin phòng
    $update_sql = "
        UPDATE room SET 
            room_name = '$room_name',
            title = '$title',
            ward_id = $ward,
            address = '$address',
            price = $price,
            description = '$description'
        WHERE room_id = $room_id
    ";
    $conn->query($update_sql);

    if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $imgUrl) {
            $escapedUrl = $conn->real_escape_string($imgUrl);
            $conn->query("DELETE FROM room_img WHERE room_id = $room_id AND image_url = '$escapedUrl'");

            $filePath = "../images/room/" . $escapedUrl;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    // Thêm ảnh mới nếu có
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadDir = "../images/room/";

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['images']['name'][$key]);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($tmp_name, $targetFile)) {
                $escapedFileName = $conn->real_escape_string($fileName);
                $conn->query("INSERT INTO room_img (room_id, image_url) VALUES ($room_id, '$escapedFileName')");
            }
        }
    }

    // Chuyển hướng về danh sách phòng
    header("Location: ../host.php?page_layout=manage_room");
    exit();
}
?>
