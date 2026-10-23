<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // استخدام Prepared Statement للأمان واستهداف الحساب مباشرة
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);
        
        // مقارنة مباشرة بدون استخدام دالة التشفير
        if ($admin && $password == $admin['password']) {
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_user'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "اسم المستخدم أو كلمة المرور غير صحيحة!";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "خطأ في إعداد استعلام السيرفر.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول | لوحة التحكم</title>
    <style>
        body { font-family: Tahoma, sans-serif; background: #0f0f0f; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; direction: rtl; }
        .login-box { background: #1a1a1a; padding: 40px; border-radius: 10px; border: 1px solid #d4af37; text-align: center; width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; background: black; color: white; border: 1px solid #333; border-radius: 5px; box-sizing: border-box; }
        button { background: #d4af37; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; width: 100%; border-radius: 5px; color: black; margin-top: 10px; }
        button:hover { background: #fff; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="color:#d4af37; margin-top: 0;">دخول المشرفين (التطوير)</h2>
        <?php if(!empty($error)): ?>
            <p style="color:#ff4d4d; font-size: 14px;"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="اسم المستخدم" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit">تسجيل الدخول</button>
        </form>
    </div>
</body>
</html>