<?php
include 'config.php';

$tenant_id = $_SESSION['user_id'];

$sql = "
    SELECT p.*
    FROM payment p
    JOIN rental_contract rc ON p.contract_id = rc.contract_id
    WHERE rc.tenant_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-fluid p-5">
    <h2 class="mb-4">Danh sách hóa đơn của bạn</h2>
    <div class="row g-4">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-12 col-xl-6">
                <div class="card border-4 border-<?php echo ($row['payment_status'] == 'paid') ? 'success' : (($row['payment_status'] == 'late') ? 'danger' : 'warning'); ?>">
                    <div class="row g-0">
                        <!-- Cột trái: Nội dung chính -->
                        <div class="col-md-8 p-3">
                            <h5 class="card-title">Hóa đơn</h5>
                            <p class="card-text"><strong>Tiền nhà từ:</strong> <?php echo $row['period_start']; ?> - <?php echo $row['period_end']; ?></p>
                            <p class="card-text"><strong>Hạn thanh toán:</strong> <?php echo $row['due_date']; ?></p>
                            <p class="card-text"><strong>Số tiền:</strong> <?php echo number_format($row['amount_due']); ?> đ</p>
                        </div>

                        <!-- Cột phải: Trạng thái và thanh toán -->
                        <div class="col-md-4 d-flex flex-column justify-content-center align-items-md-end p-3">
                            <p class="mb-2"><strong>Trạng thái:</strong> 
                                <span class="badge bg-<?php 
                                    echo ($row['payment_status'] == 'paid') ? 'success' : 
                                        (($row['payment_status'] == 'late') ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($row['payment_status']); ?>
                                </span>
                            </p>

                           

                            <?php if ($row['payment_status'] == 'unpaid' || $row['payment_status'] == 'late'): ?>
                                <a href="controller/pay.php?payment_id=<?php echo $row['payment_id']; ?>" class="btn btn-primary mt-2">
                                    Thanh toán ngay
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-end">
                        <?php if($row['paid_at']){ 
                            echo "Đã thanh toán lúc: " . $row['paid_at']; 
                        }else{
                            echo "Tạo lúc: " . $row['created_at']; 
                        } ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>


