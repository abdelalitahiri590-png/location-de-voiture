<?php
include 'db.php';

// ميزة فلترة السيارات حسب المدينة المحددة
$selectedCity = isset($_GET['city']) ? mysqli_real_escape_string($conn, $_GET['city']) : '';

function getCarsByCategory($conn, $category, $city) {
    $sql = "SELECT * FROM cars WHERE category = ? AND available = 1";
    if ($city != '') {
        $sql .= " AND city = ?";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($city != '') {
        mysqli_stmt_bind_param($stmt, "ss", $category, $city);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $category);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $cars;
}

$normalCars = getCarsByCategory($conn, 'normal', $selectedCity);
$sportCars = getCarsByCategory($conn, 'sport', $selectedCity);
$luxuryCars = getCarsByCategory($conn, 'luxury', $selectedCity);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>abdou_car | كراء السيارات بالمغرب</title>
    <link rel="stylesheet" href="car.css">
</head>
<body>

<nav>
    <h2>🚗 abdou_car</h2>
    <ul>
        <li><a href="#normalCars">السيارات</a></li>
        <li><a href="dashboard.php" style="background:#d4af37; padding:5px 10px; border-radius:5px; color:black; font-weight:bold;">Admin</a></li>
    </ul>
</nav>

<header class="hero">
    <div class="hero-content">
        <h1>كراء السيارات الفاخرة و الرياضية والعادية بالمغرب</h1>
        <p>أفضل الأسعار - تسليم بالمطار - خدمة 24/24</p>
        <div class="booking-box">
            <select id="citySelect" onchange="filterCity()">
                <option value="">كل المدن</option>
                <option value="مراكش" <?= $selectedCity == 'مراكش' ? 'selected' : '' ?>>مراكش</option>
                <option value="الدار البيضاء" <?= $selectedCity == 'الدار البيضاء' ? 'selected' : '' ?>>الدار البيضاء</option>
                <option value="طنجة" <?= $selectedCity == 'طنجة' ? 'selected' : '' ?>>طنجة</option>
                <option value="أكادير" <?= $selectedCity == 'أكادير' ? 'selected' : '' ?>>أكادير</option>
                <option value="الرباط - سلا" <?= $selectedCity == 'الرباط - سلا' ? 'selected' : '' ?>>الرباط - سلا</option>
                <option value="الراشيدية" <?= $selectedCity == 'الراشيدية' ? 'selected' : '' ?>>الراشيدية</option>
            </select>
            <input type="date" id="startDate" onchange="calculateDays()">
            <input type="date" id="endDate" onchange="calculateDays()">
            <p id="daysResult" style="color: #d4af37; font-weight: bold; margin-top: 10px;"></p>
        </div>
    </div>
</header>

<div style="text-align:center; margin:40px 0;">
    <input type="text" id="searchInput" placeholder="ابحث عن اسم سيارة معينة..." onkeyup="searchCar()">
</div>

<section class="section-title"><h2>🚗 السيارات العادية</h2></section>
<section id="normalCars" class="cars">
    <?php foreach($normalCars as $car): ?>
        <div class="car" data-name="<?= htmlspecialchars($car['name']) ?>">
            <img src="<?= htmlspecialchars($car['image']) ?>">
            <h3><?= htmlspecialchars($car['name']) ?></h3>
            <p class="city-badge">📍 <?= htmlspecialchars($car['city']) ?></p>
            <p class="price"><?= $car['price'] ?> درهم / اليوم</p>
            <button class="reserve" onclick="openModal(<?= $car['id'] ?>, '<?= htmlspecialchars($car['name']) ?>', <?= $car['price'] ?>, '<?= htmlspecialchars($car['image']) ?>')">احجز الآن</button>
        </div>
    <?php endforeach; ?>
</section>

<section class="section-title"><h2>🏎️ السيارات الرياضية</h2></section>
<section id="sportCars" class="cars">
    <?php foreach($sportCars as $car): ?>
        <div class="car" data-name="<?= htmlspecialchars($car['name']) ?>">
            <img src="<?= htmlspecialchars($car['image']) ?>">
            <h3><?= htmlspecialchars($car['name']) ?></h3>
            <p class="city-badge">📍 <?= htmlspecialchars($car['city']) ?></p>
            <p class="price"><?= $car['price'] ?> درهم / اليوم</p>
            <button class="reserve" onclick="openModal(<?= $car['id'] ?>, '<?= htmlspecialchars($car['name']) ?>', <?= $car['price'] ?>, '<?= htmlspecialchars($car['image']) ?>')">احجز الآن</button>
        </div>
    <?php endforeach; ?>
</section>

<section class="section-title"><h2>👑 السيارات الفاخرة</h2></section>
<section id="luxuryCars" class="cars">
    <?php foreach($luxuryCars as $car): ?>
        <div class="car" data-name="<?= htmlspecialchars($car['name']) ?>">
            <img src="<?= htmlspecialchars($car['image']) ?>">
            <h3><?= htmlspecialchars($car['name']) ?></h3>
            <p class="city-badge">📍 <?= htmlspecialchars($car['city']) ?></p>
            <p class="price"><?= $car['price'] ?> درهم / اليوم</p>
            <button class="reserve" onclick="openModal(<?= $car['id'] ?>, '<?= htmlspecialchars($car['name']) ?>', <?= $car['price'] ?>, '<?= htmlspecialchars($car['image']) ?>')">احجز الآن</button>
        </div>
    <?php endforeach; ?>
</section>

<div id="carModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImg" width="100%">
        <h3 id="modalTitle"></h3>
        <p id="modalPrice"></p>
        <hr style="border:0.5px solid #333; margin:15px 0;">
      
        <input type="text" id="clientName" placeholder="أدخل اسمك الكامل">
        <input type="text" id="clientPhone" placeholder="06XXXXXXXX">
        <input type="text" id="clientCity" placeholder="مدينتك الحالية">
        <input type="number" id="rentalDays" min="1" value="1" readonly>
        
        <p id="totalPrice"></p>
        <button onclick="reserveOrder()" class="btn-confirm">تأكيد طلب الحجز</button>
    </div>
</div>

<script src="car.js"></script>
</body>
</html>