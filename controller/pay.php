<?php
include '../config.php';

$payment_id = $_GET['payment_id'];
$sql = "SELECT * FROM payment WHERE payment_id = '$payment_id'";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) die("Không tìm thấy hóa đơn");

$row = $result->fetch_assoc();
$amount = $row['amount_due']; // số tiền
$orderId = time(); 
$orderInfo = "Thanh toán tiền thuê trọ - ID hóa đơn: $payment_id";
$returnUrl = "http://localhost/QLKTX/controller/momo_return.php";
$ipnUrl = "http://localhost/QLKTX/controller/ipn.php";

$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = "MOMO";
$accessKey = "F8BBA842ECF85";
$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";
$requestId = time() . "";
$requestType = "captureWallet";
$extraData = $payment_id;


$rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$returnUrl&requestId=$requestId&requestType=$requestType";
$signature = hash_hmac("sha256", $rawHash, $secretKey);

$data = array(
    'partnerCode' => $partnerCode,
    'accessKey' => $accessKey,
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $returnUrl,
    'ipnUrl' => $ipnUrl,
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature,
    'lang' => 'vi'
);


function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


$result = execPostRequest($endpoint, $data);
$jsonResult = json_decode($result, true);
header('Location: ' . $jsonResult['payUrl']);
