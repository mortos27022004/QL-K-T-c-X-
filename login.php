<?php
session_start(); // Bắt đầu session
require_once 'config.php'; // Gọi file cấu hình CSDL

// Nhận dữ liệu từ form
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Kiểm tra xem username có tồn tại không
$sql = "SELECT * FROM user WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Nếu tồn tại username
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Kiểm tra mật khẩu
    if (password_verify($password, $user['password'])) {
        // Lưu thông tin người dùng vào session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        if($_SESSION['role'] == 'landlord'){
            echo "<script>
                alert(' HOST Đăng nhập thành công!');
                window.location.href = 'host.php';
            </script>";
        }else{
            echo "<script>
                alert('TENANT Đăng nhập thành công!');
                window.location.href = 'index.php';
            </script>";
        }

        exit;
    } else {
        // Mật khẩu sai
        echo "<script>
            alert('Sai mật khẩu!');
            window.history.back();
        </script>";
        exit;
    }
} else {
    // Không tìm thấy tài khoản
    echo "<script>
        alert('Tên đăng nhập không tồn tại!');
        window.history.back();
    </script>";
    exit;
}

$stmt->close();
$conn->close();
?>
