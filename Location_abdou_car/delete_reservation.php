<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // استخدام Prepared Statements لمنع التلاعب البرمجي بمعرف الحذف المعطى برابط الـ URL
    $sql = "DELETE FROM reservations WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: dashboard.php");
exit;
?>