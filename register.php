<?php
// Gọi file cấu hình
require_once 'config.php';

// Nhận dữ liệu từ form
$name = trim($_POST['name']);
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$phone = trim($_POST['phone']);
$role = trim($_POST['role']);


// Mã hóa mật khẩu
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Kiểm tra tên đăng nhập đã tồn tại chưa
$sql_check = "SELECT * FROM user WHERE username = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    echo "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
} else {
    // Thêm vào CSDL
    $sql = "INSERT INTO user (name, username, password, phone, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $username, $hashed_password, $phone, $role);

    if ($stmt->execute()) {
        echo "<script>
            alert('Đăng ký thành công!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
