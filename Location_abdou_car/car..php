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
        <li><a href="#contact">تواصل معنا</a></li>
        <li><a href="dashboard.html" style="background:#d4af37; padding:5px 10px; border-radius:5px; color:black;">Admin</a></li>
    </ul>
</nav>
 <button id="toggle">🌙</button>
<header class="hero">
    <div class="hero-content">
        <h1>كراء السيارات الفاخرة و الرياضية والعادية بالمغرب</h1>
        <p>أفضل الأسعار - تسليم بالمطار - خدمة 24/24</p>
        <div class="booking-box">
            <select id="city">
                <option>مراكش</option><option>الدار البيضاء</option><option>طنجة</option>
                <option>أكادير</option><option>الرباط - سلا</option><option>الراشيدية</option>
            </select>
            <input type="date" id="startDate">
            <input type="date" id="endDate">
            <button onclick="calculateDays()">احسب المدة</button>
            <p id="daysResult"></p>
        </div>
    </div>
</header>

<div style="text-align:center; margin:40px 0;">
    <input type="text" id="searchInput" placeholder="ابحث عن سيارة..." onkeyup="searchCar()" 
           style="padding:15px; width:300px; border-radius:30px; border:1px solid #d4af37; outline:none; background:#1a1a1a; color:white;">
</div>

<section class="section-title"><h2>🚗 السيارات العادية</h2></section>
<section id="normalCars" class="cars"></section>

<section class="section-title"><h2>🚗السيارات الرياضية</h2></section>
<section id="sportCars" class="cars"></section>

<section class="section-title"><h2>👑 السيارات الفاخرة</h2></section>
<section id="luxuryCars" class="cars"></section>

<div id="carModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()" style="cursor:pointer; float:left; font-size:28px;">&times;</span>
        <img id="modalImg" width="80%" style="border-radius:10px;">
        <h3 id="modalTitle"></h3>
        <p id="modalPrice"></p>
        <hr style="border:0.5px solid #333; margin:15px 0;">
      
        <input type="text" id="clientName" placeholder="أدخل اسمك الكامل">
        <input type="text" id="clientPhone" placeholder="06XXXXXXXX">
        <input type="text" id="clientCity" placeholder="مدينتك">
        <input type="number" id="rentalDays" min="1" value="1">
        
        <p id="totalPrice" style="color:#d4af37; font-weight:bold; font-size:20px;"></p>
        <button onclick="reserveOrder()" style="width:100%; padding:15px; background:#d4af37; border:none; border-radius:10px; font-weight:bold; cursor:pointer;">تأكيد الحجز</button>
    </div>
</div>

<script src="car.js"></script>
</body>
</html>