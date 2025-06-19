<?php
include '../../config.php'; // Kết nối DB

// Giả sử đã đăng nhập và có landlord_id trong session
$landlord_id = $_SESSION['user_id'] ?? 1; // (1 là tạm để test)

// 1. Tổng doanh thu tháng này
$sql1 = "SELECT SUM(p.amount_due) AS total_revenue
        FROM payment p
        JOIN rental_contract c ON p.contract_id = c.contract_id
        WHERE c.landlord_id = ? 
          AND p.payment_status = 'paid'
          AND MONTH(p.paid_at) = MONTH(CURRENT_DATE())
          AND YEAR(p.paid_at) = YEAR(CURRENT_DATE())";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("i", $landlord_id);
$stmt1->execute();
$stmt1->bind_result($totalRevenue);
$stmt1->fetch();
$stmt1->close();

// 2. Hóa đơn chưa thanh toán
$sql2 = "SELECT COUNT(*) FROM payment p
         JOIN rental_contract c ON p.contract_id = c.contract_id
         WHERE c.landlord_id = ? AND p.payment_status = 'unpaid'";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $landlord_id);
$stmt2->execute();
$stmt2->bind_result($unpaidCount);
$stmt2->fetch();
$stmt2->close();

// 3. Người thuê đang thuê
$sql3 = "SELECT COUNT(DISTINCT tenant_id) FROM rental_contract 
         WHERE landlord_id = ? AND status = 'active'";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("i", $landlord_id);
$stmt3->execute();
$stmt3->bind_result($activeTenants);
$stmt3->fetch();
$stmt3->close();

// 4. Hợp đồng sắp hết hạn trong 30 ngày
$sql4 = "SELECT COUNT(*) FROM rental_contract 
         WHERE landlord_id = ? AND end_date <= (CURRENT_DATE + INTERVAL 30 DAY)";
$stmt4 = $conn->prepare($sql4);
$stmt4->bind_param("i", $landlord_id);
$stmt4->execute();
$stmt4->bind_result($expiringContracts);
$stmt4->fetch();
$stmt4->close();
?>

<div class="container py-5">
  <h2 class="mb-5 text-center fw-bold">📊 Tổng quan Chủ trọ</h2>
  <div class="row g-4">

    <!-- Tổng doanh thu -->
    <div class="col-md-6 col-lg-3">
      <div class="card dashboard-card text-white bg-success shadow-sm h-100">
        <div class="card-body text-center">
          <div class="dashboard-icon">💵</div>
          <h5 class="card-title">Doanh thu tháng</h5>
          <p class="fs-4 mb-0"><?= number_format($totalRevenue ?? 0, 0, ',', '.') ?> đ</p>
        </div>
      </div>
    </div>

    <!-- Hóa đơn chưa thanh toán -->
    <div class="col-md-6 col-lg-3">
      <div class="card dashboard-card text-white bg-danger shadow-sm h-100">
        <div class="card-body text-center">
          <div class="dashboard-icon">📄</div>
          <h5 class="card-title">Hóa đơn chưa thanh toán</h5>
          <p class="fs-4 mb-0"><?= $unpaidCount ?? 0 ?></p>
        </div>
      </div>
    </div>

    <!-- Người thuê đang thuê -->
    <div class="col-md-6 col-lg-3">
      <div class="card dashboard-card text-white bg-primary shadow-sm h-100">
        <div class="card-body text-center">
          <div class="dashboard-icon">👥</div>
          <h5 class="card-title">Người thuê đang thuê</h5>
          <p class="fs-4 mb-0"><?= $activeTenants ?? 0 ?></p>
        </div>
      </div>
    </div>

    <!-- Hợp đồng sắp hết hạn -->
    <div class="col-md-6 col-lg-3">
      <div class="card dashboard-card text-white bg-warning shadow-sm h-100">
        <div class="card-body text-center">
          <div class="dashboard-icon">⏳</div>
          <h5 class="card-title">Hợp đồng sắp hết hạn</h5>
          <p class="fs-4 mb-0"><?= $expiringContracts ?? 0 ?></p>
        </div>
      </div>
    </div>

  </div>
</div>