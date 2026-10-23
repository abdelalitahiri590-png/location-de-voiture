<?php
include 'db.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "لم يتم استقبال أي بيانات"]);
    exit;
}

// استخراج المتغيرات وتنظيفها
$carId       = (int)$data['carId'];
$clientName  = trim($data['clientName']);
$clientPhone = trim($data['clientPhone']);
$clientCity  = trim($data['clientCity']);
$startDate   = $data['startDate'];
$endDate     = $data['endDate'];
$days        = (int)$data['days'];
$totalPrice  = (int)$data['totalPrice'];
$lat         = !empty($data['lat']) ? (float)$data['lat'] : null;
$lng         = !empty($data['lng']) ? (float)$data['lng'] : null;

if (empty($clientName) || empty($clientPhone) || empty($startDate) || empty($endDate)) {
    echo json_encode(["status" => "error", "message" => "تأكد من إدخال جميع الحقول الإلزامية"]);
    exit;
}

// معالجة وحفظ البيانات عبر Prepared Statement لمنع ثغرات الحقن
$sql = "INSERT INTO reservations (car_id, client_name, client_phone, client_city, start_date, end_date, days, total_price, lat, lng) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "isssssiidd", $carId, $clientName, $clientPhone, $clientCity, $startDate, $endDate, $days, $totalPrice, $lat, $lng);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "تم تسجيل طلب حجزك بنجاح وسيتواصل معك الدعم الفني ✅"]);
    } else {
        echo json_encode(["status" => "error", "message" => "فشل حفظ الحجز في قاعدة البيانات"]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["status" => "error", "message" => "فشل إعداد الاستعلام البرمجي بالسيرفر"]);
}
?>