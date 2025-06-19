<?php
// Cấu hình kết nối CSDL
    $host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "QLKTX";

    // Tạo kết nối
    $conn = new mysqli($host, $db_user, $db_pass, $db_name,3307);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối CSDL thất bại: " . $conn->connect_error);
    }
?>
