<?php
session_start();
// Kiểm tra nếu người dùng chưa đăng nhập hoặc không phải là admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'landlord') {
    header("Location: ../index.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập hoặc không phải admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css"> 
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="shortcut icon" href="https://play-lh.googleusercontent.com/cyWiXDTIzS06REm5RZMo_tyItoTlYZzyjeISMHgKZnqnem9WjKOnOHaiXMoMKuMAfJc=w240-h480-rw">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-thin-rounded/css/uicons-thin-rounded.css'>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" rel="stylesheet" />
    <title>HomeHub</title>
</head>
<body>

    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex justify-content-between p-4">
                <div class="sidebar-logo">
                    <a href="#" class="my-auto">HomeHub</a>
                </div>
                <button class="toggle-btn border-0" type="button">
                    <span id="icon" class="material-symbols-sharp">chevron_right</span>
                </button>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="host.php?page_layout=home" class="sidebar-link">
                        <i class="icon fi fi-tr-house-chimney-blank"></i>
                        <span class="sidebar-text">Trang chủ</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="host.php?page_layout=manage_room" class="sidebar-link">
                        <i class="icon fi fi-tr-house-key"></i>
                        <span class="sidebar-text">Quản lý phòng</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="host.php?page_layout=manage_rent_request" class="sidebar-link">
                        <i class="icon fi fi-tr-suggestion"></i>
                        <span class="sidebar-text">Yêu cầu thuê</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="host.php?page_layout=manage_appointment" class="sidebar-link">
                        <i class="icon fi fi-tr-overview"></i>
                        <span class="sidebar-text">Xem phòng</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="host.php?page_layout=manage_contract" class="sidebar-link">
                        <i class="icon fi fi-tr-contract"></i>
                        <span class="sidebar-text">Hợp đồng</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="host.php?page_layout=payment" class="sidebar-link">
                        <i class="icon fi fi-tr-calculator-bill"></i>
                        <span class="sidebar-text">Hóa đơn</span>
                    </a>
                </li>
   
            </ul>
            <div class="mt-auto mx-auto mb-5 pb-5">
            <?php if(isset($_SESSION['name'])) { ?>
                <button type="button" class="btn_logout btn btn-outline-danger d-flex align-items-center" onclick="window.location.href='logout.php'">
                    <i class="fi fi-tr-sign-out-alt"></i>  
                    <span class="sidebar-text">Đăng xuất</span>
                </button>
            <?php }?>
        </div>
        </aside>
        <div class="main">
            <div class="navbar navbar-expand px-4 py-3 justify-content-end">
                <?php if(isset($_SESSION['name'])) { ?>
                    <div id="userArea" class="d-flex align-items-center">
                        <span class="me-3"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        <img src="../images/avatar.png" alt="Avatar" class="rounded-circle me-4" width="40" height="40">
                    </div>
                <?php } else { ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Đăng nhập / Đăng ký
                    </button>
                <?php } ?>
            </div>


            <div class="container-fluid" style="height: 90vh; overflow-y: auto;">
                <?php
                    if(isset($_GET['page_layout'])){
                        switch($_GET['page_layout']){
                            case 'home':
                                include "Pages/landlord/home.php";
                                break;
                            case 'manage_room':
                                include "Pages/landlord/manage_room.php";
                                break;
                            case 'add_room':
                                include "Pages/landlord/add_room.php";
                                break;
                            case 'manage_rent_request':
                                include "Pages/landlord/manage_rent_request.php";
                                break;
                            case 'manage_appointment':
                                include "Pages/landlord/manage_appointment.php";
                                break;
                            case 'manage_contract':
                                include "Pages/landlord/manage_contract.php";
                                break;
                            case 'payment':
                                include "Pages/landlord/payment.php";
                                break;
                            case 'edit_room':
                                include "Pages/landlord/edit_room.php";
                                break;
                        }
                    }else{
                        include "Pages/landlord/home.php";
                    }
                ?>
            </div>
            
        </div>
        
        
    </div>


    <script src="script/bootstrap.bundle.min.js"></script>
    <script src="script/script.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>

