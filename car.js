let selectedCarId = 0;
let selectedCarName = "";
let selectedPrice = 0;
let selectedImg = "";
let totalCalculatedDays = 1;

// ميزة فلترة المدن بشكل فوري وديناميكي
function filterCity() {
    const city = document.getElementById("citySelect").value;
    window.location.href = "car.php?city=" + encodeURIComponent(city);
}

function calculateDays() {
    const startInput = document.getElementById("startDate").value;
    const endInput = document.getElementById("endDate").value;
    const resultElement = document.getElementById("daysResult");
    
    if (startInput && endInput) {
        const start = new Date(startInput);
        const end = new Date(endInput);
        
        if (end >= start) {
            const diffTime = Math.abs(end - start);
            totalCalculatedDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // حساب اليوم الأول أيضاً
            resultElement.innerText = "مدة الحجز المحسوبة: " + totalCalculatedDays + " أيام";
            document.getElementById("rentalDays").value = totalCalculatedDays;
            updateTotalPrice();
        } else {
            resultElement.innerText = "⚠️ خطأ: تاريخ النهاية يجب أن يكون بعد تاريخ البداية";
            totalCalculatedDays = 1;
        }
    }
}

function openModal(id, name, price, img) {
    selectedCarId = id;
    selectedCarName = name;
    selectedPrice = price;
    selectedImg = img;

    document.getElementById("modalTitle").innerText = name;
    document.getElementById("modalPrice").innerText = price + " درهم / اليوم";
    document.getElementById("modalImg").src = img;
    document.getElementById("rentalDays").value = totalCalculatedDays;
    document.getElementById("carModal").style.display = "flex";

    updateTotalPrice();
}

function closeModal() {
    document.getElementById("carModal").style.display = "none";
}

function updateTotalPrice() {
    const days = document.getElementById("rentalDays").value || 1;
    const total = selectedPrice * days;
    document.getElementById("totalPrice").innerText = "الإجمالي المالي: " + total + " درهم";
}

function searchCar() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let carCards = document.getElementsByClassName('car');
    
    for (let i = 0; i < carCards.length; i++) {
        let name = carCards[i].getAttribute('data-name').toLowerCase();
        if (name.includes(input)) {
            carCards[i].style.display = "";
        } else {
            carCards[i].style.display = "none";
        }
    }
}

function reserveOrder() {
    const name = document.getElementById("clientName").value.trim();
    const phone = document.getElementById("clientPhone").value.trim();
    const city = document.getElementById("clientCity").value.trim();
    const startDate = document.getElementById("startDate").value;
    const endDate = document.getElementById("endDate").value;

    if (!startDate || !endDate) {
        alert("المرجو تحديد تاريخ بداية ونهاية الكراء من الصندوق العلوي أولاً!");
        return;
    }
    if (!name || !phone) {
        alert("المرجو تعبئة البيانات الأساسية (الاسم الكامل ورقم الهاتف)");
        return;
    }

    if (!navigator.geolocation) {
        sendDataToServer(name, phone, city, startDate, endDate, null, null);
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            sendDataToServer(name, phone, city, startDate, endDate, position.coords.latitude, position.coords.longitude);
        },
        (error) => {
            sendDataToServer(name, phone, city, startDate, endDate, null, null);
        }
    );
}

function sendDataToServer(name, phone, city, start, end, lat, lng) {
    const reservationData = {
        carId: selectedCarId,
        clientName: name,
        clientPhone: phone,
        clientCity: city,
        startDate: start,
        endDate: end,
        days: totalCalculatedDays,
        totalPrice: selectedPrice * totalCalculatedDays,
        lat: lat,
        lng: lng
    };

    fetch('process_reservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(reservationData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
           alert(data.message);
           closeModal();
        } else {
           alert("خطأ: " + data.message);
        }
    })
    .catch(error => {
        alert("فشل الاتصال بالسيرفر ❌");
    });
}