<?php
$conn = mysqli_connect("localhost", "root", "", "car_rental");

if (!$conn) {
    die("خطأ في الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>