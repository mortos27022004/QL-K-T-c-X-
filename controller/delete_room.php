<?php
require_once '../config.php';

if (isset($_GET['id'])) {
    $room_id = (int)$_GET['id'];

    // Xóa ảnh trong thư mục và CSDL
    $img_sql = "SELECT image_url FROM room_img WHERE room_id = $room_id";
    $img_result = $conn->query($img_sql);

    while ($img = $img_result->fetch_assoc()) {
        $filePath = "../images/room/" . $img['image_url'];
        if (file_exists($filePath)) {
            unlink($filePath); // Xóa file vật lý
        }
    }

    // Xóa ảnh khỏi bảng room_img
    $conn->query("DELETE FROM room_img WHERE room_id = $room_id");

    // Xóa phòng khỏi bảng room
    $conn->query("DELETE FROM room WHERE room_id = $room_id");

    // Chuyển hướng về danh sách phòng
    header("Location: ../host.php?page_layout=manage_room");
    exit();
} else {
    echo "Thiếu ID phòng cần xóa.";
}
?>
