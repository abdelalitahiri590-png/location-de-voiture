<?php
session_start();
// التحقق من صلاحية الجلسة وحماية الصفحة من المتطفلين
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// جلب الحجوزات مع ربط الجداول لمعرفة بيانات السيارة الحالية بدقة
$sql = "SELECT r.*, c.name AS car_name, c.image AS car_image FROM reservations r 
        INNER JOIN cars c ON r.car_id = c.id ORDER BY r.id DESC";
$result = mysqli_query($conn, $sql);

$reservations = [];
$totalRevenue = 0;
$revenueByCar = [];
$lastCar = "---";

while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row;
    $totalRevenue += $row['total_price'];
    
    if (!isset($revenueByCar[$row['car_name']])) {
        $revenueByCar[$row['car_name']] = 0;
    }
    $revenueByCar[$row['car_name']] += $row['total_price'];
}

if (count($reservations) > 0) {
    $lastCar = $reservations[0]['car_name'];
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | لوحة التحكم</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        body { font-family: Tahoma, sans-serif; background: #0f0f0f; color:white; direction:rtl; margin:0;}
        nav { padding: 20px; background: black; display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #d4af37;}
        nav h2 { color:#d4af37; margin:0; }
        .dashboard { padding:40px; }
        .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
        .stat { background:#1a1a1a; padding:20px; border-radius:10px; text-align:center; border:1px solid #222; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px; margin-top:40px; }
        .card { background:#1a1a1a; border-radius:10px; overflow:hidden; border:1px solid #222; }
        .card img { width:100%; height:180px; object-fit:cover; }
        .card-content { padding:15px; }
        .total { color:#00ff88; font-weight:bold; }
        .btn-delete { background:#ff4d4d; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer; width:100%; margin-top:10px; }
        #map { height:400px; margin-top:40px; border-radius:12px; border: 1px solid #d4af37; }
        canvas { max-width: 100%; background: #1a1a1a; padding: 20px; border-radius: 10px; margin-top: 40px; }
    </style>
</head>
<body>

<nav>
    <h2>لوحة تحكم النظام الفنية الفاخرة</h2>
    <div>
        <a href="car.php" style="color:#d4af37; text-decoration:none; margin-left: 20px;">الموقع الرئيسي</a>
        <a href="logout.php" style="color:red; text-decoration:none; font-weight: bold;">تسجيل الخروج</a>
    </div>
</nav>

<div class="dashboard">
    <div class="stats">
        <div class="stat"><h3>عدد الحجوزات الكلية</h3><p><?= count($reservations) ?></p></div>
        <div class="stat"><h3>إجمالي الإيرادات</h3><p style="color:#00ff88; font-weight:bold;"><?= $totalRevenue ?> درهم</p></div>
        <div class="stat"><h3>آخر سيارة مطلوبة</h3><p style="color:#d4af37;"><?= htmlspecialchars($lastCar) ?></p></div>
    </div>

    <div class="grid">
        <?php if(empty($reservations)): ?>
            <p>لا توجد طلبات حجوزات مسجلة حالياً.</p>
        <?php else: ?>
            <?php foreach($reservations as $res): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($res['car_image']) ?>">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($res['car_name']) ?></h3>
                        <p>👤 العميل: <?= htmlspecialchars($res['client_name']) ?></p>
                        <p>📞 الهاتف: <?= htmlspecialchars($res['client_phone']) ?></p>
                        <p>📍 مكان الإقامة: <?= htmlspecialchars($res['client_city']) ?></p>
                        <p>📅 من: <?= $res['start_date'] ?> إلى: <?= $res['end_date'] ?></p>
                        <p>⏱️ المدة: <?= $res['days'] ?> أيام</p>
                        <p class="total">💰 المبلغ: <?= $res['total_price'] ?> درهم</p>
                        <button class="btn-delete" onclick="if(confirm('هل أنت متأكد من حذف هذا الحجز نهائياً؟')) window.location.href='delete_reservation.php?id=<?= $res['id'] ?>'">إلغاء وحذف الطلب</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <canvas id="chart"></canvas>
    <h2 style="margin-top:40px; color:#d4af37;">📍 الخريطة الجغرافية التفاعلية للعملاء الحالبين (GPS)</h2>
    <div id="map"></div>
</div>

<script>
const revenueData = <?= json_encode($revenueByCar) ?>;
new Chart(document.getElementById("chart"), {
    type: 'bar',
    data: {
        labels: Object.keys(revenueData),
        datasets: [{ label: 'الأرباح المحققة بالدرهم المغربي', data: Object.values(revenueData), backgroundColor: '#d4af37' }]
    }
});

var map = L.map('map').setView([31.7917, -7.0926], 6); // الإحداثيات المركزية للمغرب الافتراضية
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

const markersData = <?= json_encode($reservations) ?>;
markersData.forEach(res => {
    if(res.lat && res.lng) {
        L.marker([parseFloat(res.lat), parseFloat(res.lng)])
        .addTo(map)
        .bindPopup(`<b>${res.car_name}</b><br>👤 العميل: ${res.client_name}<br>📞 هاتف: ${res.client_phone}<br>💰 الكلفة: ${res.total_price} DH`);
    }
});
</script>
</body>
</html>