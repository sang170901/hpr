<?php
require_once 'backend/inc/db.php';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra Font Tiếng Việt</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section { margin: 30px 0; }
        h2 { color: #2c3e50; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>🔍 Kiểm tra Font Chữ Tiếng Việt trong Database</h1>
    
    <?php
    try {
        $pdo = getPDO();
        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo '<div class="status success">✅ Kết nối database thành công với UTF-8</div>';
        
        // Test Vietnamese characters
        echo '<div class="section">';
        echo '<h2>🔤 Test Font Chữ Tiếng Việt:</h2>';
        echo '<p style="font-size: 18px;">Các ký tự đặc biệt: à á ả ã ạ ă ắ ằ ẳ ẵ ặ â ấ ầ ẩ ẫ ậ</p>';
        echo '<p style="font-size: 18px;">Chữ hoa: À Á Ả Ã Ạ Ă Ắ Ằ Ẳ Ẵ Ặ Â Ấ Ầ Ẩ Ẫ Ậ</p>';
        echo '<p style="font-size: 18px;">Ví dụ: Công ty, Thiết bị, Công nghệ, Cảnh quan, Việt Nam</p>';
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="status error">❌ Lỗi kết nối: ' . $e->getMessage() . '</div>';
    }
    ?>
    
    <div class="section">
        <h2>👥 Danh sách Nhà cung cấp:</h2>
        <table>
            <thead>
                <tr><th>ID</th><th>Tên</th><th>Email</th><th>Địa chỉ</th></tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, name, email, address FROM suppliers ORDER BY id");
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['address']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>📦 Danh sách Sản phẩm:</h2>
        <table>
            <thead>
                <tr><th>ID</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Giá</th></tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, name, category, price FROM products ORDER BY category, id");
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['category']}</td>";
                    echo "<td>" . number_format($row['price']) . " VNĐ</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>📊 Thống kê theo danh mục:</h2>
        <table>
            <thead>
                <tr><th>Danh mục</th><th>Số lượng</th></tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM products GROUP BY category ORDER BY category");
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>{$row['category']}</td>";
                    echo "<td>{$row['count']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>