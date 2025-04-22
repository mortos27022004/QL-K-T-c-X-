<?php
session_start(); // Bắt đầu phiên làm việc
session_unset();  // Hủy tất cả các biến session
session_destroy(); // Hủy session

header("Location: index.php"); // Chuyển hướng về trang đăng nhập
exit();
?>