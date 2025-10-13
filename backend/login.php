<?php
require __DIR__ . '/inc/db.php';
$config = require __DIR__ . '/config.php';
session_start();
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Sai email hoặc mật khẩu';
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đăng nhập quản trị VNMaterial</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
  <div class="login-fullpage">
    <div class="card login-box">
        <h3 style="margin-top:0">Đăng nhập quản trị</h3>
        <?php if (!empty($error)) echo "<div class='flash error'>" . htmlspecialchars($error) . "</div>"; ?>
        <form method="post">
            <label>Email
                <input type="email" name="email" placeholder="Nhập email" required>
            </label>
            <label>Mật khẩu
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            </label>
            <div style="margin-top:12px">
                <button class="primary" type="submit">Đăng nhập</button>
            </div>
        </form>
        <p class="muted" style="margin-top:12px;font-size:13px">Email: admin@vnmt.com — Mật khẩu: admin123</p>
    </div>
  </div>
</body>
</html>