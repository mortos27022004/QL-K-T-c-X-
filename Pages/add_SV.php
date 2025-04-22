<?php
session_start();
// Kiểm tra nếu người dùng chưa đăng nhập hoặc không phải là admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập hoặc không phải admin
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    
    <link rel="stylesheet" href="../style/bootstrap.min.css">
    <link rel="shortcut icon" href="../images/unnamed.webp">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Kí túc xá</title>
</head>
<body>


    <div class="d-flex">
        <aside id="sidebar" class="">
            <div class="sidebar-logo">
                <a href="admin.php" class="inner-logo">
                    <img src="../images/unnamed.webp" alt="">
                    <span>QL Kí Túc Xá</span>   
                </a>
            </div>
            <ul class="sidebar-nav">
                
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link active">
                        <div class="icon-wrap">
                            <i class="material-symbols-sharp">home</i>
                        </div>
                        <span>Trang chủ</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <div class="icon-wrap">
                            <i class="material-symbols-sharp">
                                person
                                </i>
                        </div>
                        <span>Sinh Viên</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <div class="icon-wrap">
                            <i class="material-symbols-sharp">
                                meeting_room
                                </i>
                        </div>
                        <span>Phòng Kí Túc</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <div class="icon-wrap">
                            <i class="material-symbols-sharp">
                                receipt_long
                                </i>
                        </div>
                        <span>Hóa Đơn</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <div class="icon-wrap">
                            <i class="material-symbols-sharp">
                                contract_edit
                            </i>
                        </div>
                        <span>Hợp đồng</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <div class="icon-wrap">
                            <i class="material-symbols-sharp">
                                support_agent
                                </i>
                        </div>
                        <span>Yêu Cầu Hỗ Trợ</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <div class="icon-wrap">
                            <i class="material-symbols-sharp">
                                campaign
                                </i>
                        </div>
                        <span>Thông Báo</span>
                    </a>
                </li>
            
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php" class="sidebar-link">
                    <div class="icon-wrap">
                        <i class="material-symbols-sharp">
                            logout
                            </i>
                    </div>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        
        <div class="main">
            <nav class="navbar sticky-top">
                <a href="" class="top-title">
                    <h1>
                        Trang chủ
                    </h1>
                </a>
                <button class="toggler-btn" type="button">
                    <i class="material-symbols-sharp">
                        menu
                        </i>
                </button>
            </nav>
            <main>
                <div class="container-fluid">
                   <div class="container pt-5">
                        <div class="row">
                            <h1 class="text-center mb-3">Thêm Sinh Viên</h1>
                            <form action="admin.php">
                                <div class="row mb-1">
                                    <div class = "col-8">
                                        <div class="row align-items-center">
                                            <label for="ten_sv" class="form-label col-3">Họ và tên:</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control" id="ten_sv" name="ten_sv" placeholder="Nguyễn Đình Tuấn">
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "col-4">
                                        <div class="row align-items-center">
                                            <label for="gioitinh" class="form-label col-5">Giới tính:</label>
                                            <div class="col-7">
                                            <select class="form-select" id="gioitinh" name="gioitinh" aria-label="Giới tính">
                                                <option value="Nam" selected>Nam</option>
                                                <option value="Nữ">Nữ</option>
                                            </select>
                                            </div>    
                                        </div> 
                                    </div>
                                
                                    <div class = "col-6">
                                        <div class="row align-items-center">
                                            <label for="sdt" class="form-label col-4">Số điện thoại:</label>
                                            <div class="col-8">
                                                <input type="text" class="form-control" id="sdt" name="sdt" placeholder="0866418597">
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "col-6">
                                        <div class="row align-items-center">
                                            <label for="id" class="form-label col-4">Ngày sinh:</label>
                                            <div class="col-8">
                                                <input type="date" class="form-control" id="id" name="ngaysinh" placeholder="27/02/2004">
                                            </div>    
                                        </div> 
                                    </div>
                                    
                                
                                    <div class = "col-6">
                                        <div class="row align-items-center">
                                            <label for="ma_sv" class="form-label col-4">Mã Sinh Viên:</label>
                                            <div class="col-8">
                                                <input type="text" class="form-control" id="ma_sv" name="ma_sv" placeholder="B22DCCN759">
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "col-6">
                                        <div class="row align-items-center">
                                            <label for="cccd" class="form-label col-2">CCCD:</label>
                                            <div class="col-10">
                                                <input type="text" class="form-control" id="cccd" name="cccd" placeholder="02346454567">
                                            </div>    
                                        </div> 
                                    </div>

                                    <div class = "col-6">
                                        <div class="row align-items-center">
                                            <label for="lop" class="form-label col-4">Lớp:</label>
                                            <div class="col-8">
                                                <input type="text" class="form-control" id="lop" name="ma" placeholder="D22CQCN-03">
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "col-6">
                                        <div class="row align-items-center">
                                            <label for="mail" class="form-label col-2">Mail:</label>
                                            <div class="col-10">
                                                <input type="mail" class="form-control" id="mail" name="mail" placeholder="superman@gmail.com">
                                            </div>    
                                        </div> 
                                    </div>

                                    <div class = "col-12">
                                        <div class="row align-items-center">
                                            <label for="diachi" class="form-label col-2">Địa chỉ:</label>
                                            <div class="col-10">
                                                <input type="text" class="form-control" id="diachi" name="diachi" placeholder="Địa chỉ">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                                    </div>
                                    
                                </div>
                                
                                
                            </form>
                        </div>
                        
                   </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../script/bootstrap.bundle.min.js"></script>
    <script src="../script/script.js"></script>
</body>
</html>