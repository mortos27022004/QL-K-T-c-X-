<?php
// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";      // Tên người dùng MySQL
$password = "";          // Mật khẩu nếu có, hoặc để trống nếu không có
$dbname = "QLKTX"; // Tên cơ sở dữ liệu

try {
    // Tạo kết nối với PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Thiết lập chế độ lỗi PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
}
?>