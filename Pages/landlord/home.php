<?php
include 'config.php';
    $landlord_id = $_SESSION['user_id']; 
    $sql = "SELECT SUM(p.amount_due) AS tong_doanh_thu 
            FROM payment p
            JOIN rental_contract c ON p.contract_id = c.contract_id
            WHERE p.payment_status = 'paid' AND c.landlord_id = $landlord_id";
    $doanh_thu = $conn->query($sql)->fetch_assoc()['tong_doanh_thu'] ?? 0;
    // Số yêu cầu thuê chưa xử lý
    $sql = "SELECT COUNT(*) AS so_yeu_cau 
            FROM rental_request rq
            JOIN room r ON rq.room_id = r.room_id
            WHERE rq.status = 'pending' AND r.landlord_id = $landlord_id";
    $yeu_cau_chua_xu_ly = $conn->query($sql)->fetch_assoc()['so_yeu_cau'] ?? 0;

    $sql = "SELECT COUNT(*) AS so_yeu_cau 
            FROM rental_request rq
            JOIN room r ON rq.room_id = r.room_id
            WHERE r.landlord_id = $landlord_id";
    $yeu_cau = $conn->query($sql)->fetch_assoc()['so_yeu_cau'] ?? 0;


    $sql = "SELECT COUNT(*) AS so_phong 
        FROM room 
        WHERE status = 'available' AND landlord_id = $landlord_id";
    $so_phong_trong = $conn->query($sql)->fetch_assoc()['so_phong'] ?? 0;

    $sql = "SELECT COUNT(*) AS so_phong 
        FROM room 
        WHERE landlord_id = $landlord_id";
    $so_phong = $conn->query($sql)->fetch_assoc()['so_phong'] ?? 0;


    // Danh sách hóa đơn chưa thanh toán
    $sql = "SELECT p.payment_id, u.name, r.room_name, p.amount_due 
            FROM payment p
            JOIN rental_contract c ON p.contract_id = c.contract_id
            JOIN user u ON c.tenant_id = u.user_id
            JOIN room r ON c.room_id = r.room_id
            WHERE p.payment_status = 'unpaid' AND c.landlord_id = $landlord_id";
    $hoa_don = $conn->query($sql);


    // Lịch hẹn xem phòng
    $sql = "SELECT a.appointment_id, r.room_name, a.scheduled_time 
        FROM view_appointment a
        JOIN room r ON a.room_id = r.room_id
        WHERE a.status = 'confirmed' AND a.landlord_id = $landlord_id
        ORDER BY a.appointment_id";
    $lich_hen = $conn->query($sql);

?>




<div class="container-fluid p-5">
    <div class="mb-3">
        <h3 class="fw-bold fs-4 mb-3">
            Trang chủ
        </h3>
        <div class="row gy-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow border-start border-start-5 border-start-success">
                    <div class="card-body py-4">
                        <h6 class="mb-2 fw-bold">
                            Doanh thu
                        </h6>
                        <p class="fw-bold mb-3 text-success fs-3">
                            <?php echo number_format($doanh_thu); ?> VND
                        </p>
                        <div class="mb-2">
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow">
                    <div class="card-body py-4">
                        <h6 class="mb-2 fw-bold">
                            Số yêu cầu thuê chưa xử lý
                        </h6>
                        <p class="fw-bold mb-2 text-danger">
                            <?php echo $yeu_cau_chua_xu_ly; ?> Yêu cầu
                        </p>
                        <div class="mb-0">
                            <span class="fw-bold">
                                Trên tổng số
                                <span class="bagde text-success me-2">
                                    <?php echo $yeu_cau; ?>
                                </span>
                                Yêu cầu
                            </span>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow">
                    <div class="card-body py-4">
                        <h6 class="mb-2 fw-bold">
                            Số phòng/nhà trống
                        </h6>
                        <p class="fw-bold mb-2 text-primary">
                            <?php echo $so_phong_trong; ?> Phòng
                        </p>
                        <div class="mb-0">
                            <span class="fw-bold">
                                Trên tổng số
                                <span class="bagde text-success me-2">
                                    <?php echo $so_phong; ?>
                                </span>
                                Phòng
                            </span>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-7">
                <h3 class="fw-bold fs-4 my-3">Hóa Đơn chưa thanh toán</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Người thuê</th>
                        <th scope="col">Phòng</th>
                        <th scope="col">Số tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        while($row = $hoa_don->fetch_assoc()) {
                            echo "<tr>
                                    <th scope='row'>{$i}</th>
                                    <td>{$row['name']}</td>
                                    <td>{$row['room_name']}</td>
                                    <td>".number_format($row['amount_due'])." VND</td>
                                </tr>";
                            $i++;
                        }
                        ?>
                    </tbody>

                </table>
                
            </div>
            <div class="col-12 col-md-5">
                <h3 class="fw-bold fs-4 my-3">Lịch hẹn xem phòng</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Phòng</th>
                        <th scope="col">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $i = 1;
                        while($row = $lich_hen->fetch_assoc()) {
                            echo "<tr>
                                    <th scope='row'>{$i}</th>
                                    <td>{$row['room_name']}</td>
                                    <td>{$row['scheduled_time']}</td>
                                </tr>";
                            $i++;
                        }
                        ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>