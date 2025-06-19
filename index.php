<?php
  session_start();
  if (isset($_SESSION['name']) && $_SESSION['role'] === 'landlord') {
      header("Location: host.php"); 
      exit();
  }
  $_GET['layout'] = 'home';
?>

<!DOCTYPE html>
<html lang="vi">
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
          <a href="index.php?page_layout=home" class="sidebar-link">
              <i class="icon fi fi-tr-house-chimney-blank"></i>
              <span class="sidebar-text">Trang chủ</span>
          </a>
        </li>

        <?php if(isset($_SESSION['name'])) { ?>
        <li class="sidebar-item">
          <a href="index.php?page_layout=tenant_requests" class="sidebar-link">
              <i class="icon fi fi-tr-suggestion"></i>
              <span class="sidebar-text">Yêu cầu thuê</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="index.php?page_layout=appointment" class="sidebar-link">
              <i class="icon fi fi-tr-overview"></i>
              <span class="sidebar-text">Xem phòng</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="index.php?page_layout=list_contract" class="sidebar-link">
              <i class="icon fi fi-tr-contract"></i>
              <span class="sidebar-text">Hợp đồng</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="index.php?page_layout=list_payment" class="sidebar-link">
              <i class="icon fi fi-tr-calculator-bill"></i>
              <span class="sidebar-text">Hóa đơn</span>
          </a>
        </li>
        <?php }?>
        
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
              case 'tenant_requests':  
                include "Pages/tenant/tenant_requests.php";
                break;
              case 'home':  
                include "Pages/tenant/list_room.php";
                break;
              case 'appointment':  
                include "Pages/tenant/list_appointment.php";
                break;
              case 'list_contract':  
                include "Pages/tenant/list_contract.php";
                break;
              case 'list_payment':  
                include "Pages/tenant/list_payment.php";
                break;
              case 'message':  
                include "Pages/message.php";
                break;
            }
          }else{
            if(isset($_GET['id'])){
              include "Pages/tenant/room_detail.php";
            }else{
              include "Pages/tenant/list_room.php";
            }
        }?>
      </div>
    </div>
    <!-- Modal Đăng Nhập -->
    <div class="modal fade" id="loginModal" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="loginModalLabel">Đăng nhập</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <form action="login.php" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" name = "username" id="username" placeholder="Nhập tên đăng nhập" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name = "password"  id="password" placeholder="Nhập mật khẩu" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            </form>
            <div class="text-center mt-3">
              <small>Chưa có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Đăng ký</a></small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Đăng ký -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="registerModalLabel">Đăng ký tài khoản</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <form action="register.php" method="POST">
              <div class="mb-3">
                <label class="form-label">Bạn là:</label>
                <select class="form-select" name="role" required>
                  <option value="tenant">Người thuê trọ</option>
                  <option value="landlord">Chủ trọ</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="fullName" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="fullName" name="name" required>
              </div>
              <div class="mb-3">
                <label for="registerUsername" class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" id="registerUsername" name="username" required>
              </div>
              <div class="mb-3">
                <label for="registerPassword" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="registerPassword" name="password" required>
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
              </div>
              <button type="submit" class="btn btn-success w-100">Đăng ký</button>
            </form>


            <div class="text-center mt-3">
              <small>Đã có tài khoản?
                <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Đăng nhập</a>
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="script/bootstrap.bundle.min.js"></script>
  <script src="script/script.js"></script>
</body>
</html>
