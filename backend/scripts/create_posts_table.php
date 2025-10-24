<?php
/**
 * Tạo bảng posts để lưu tin tức/bài viết
 */

require_once __DIR__ . '/../inc/db.php';

try {
    $pdo = getPDO();
    
    echo "<h2>🔧 Tạo Bảng Posts</h2>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;}</style>";
    
    echo "<p>Đang tạo bảng posts...</p>";
    
    // Tạo bảng posts
    $sql = "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        excerpt TEXT,
        content LONGTEXT,
        featured_image VARCHAR(500),
        author_id INT DEFAULT NULL,
        category VARCHAR(100),
        tags VARCHAR(255),
        status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        published_at DATETIME DEFAULT NULL,
        INDEX idx_status (status),
        INDEX idx_category (category),
        INDEX idx_created_at (created_at),
        INDEX idx_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<p class='success'>✓ Bảng posts đã được tạo thành công!</p>";
    
    // Kiểm tra cấu trúc bảng
    $columns = $pdo->query("SHOW COLUMNS FROM posts")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📋 Cấu trúc bảng posts:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;background:white;'>";
    echo "<tr style='background:#38bdf8;color:white;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($col['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3 class='success'>✅ Hoàn tất!</h3>";
    echo "<p>Bây giờ bạn có thể thêm bài viết.</p>";
    
    echo "<p style='text-align:center;margin-top:30px;'>";
    echo "<a href='add_more_posts.php' style='display:inline-block;padding:15px 30px;background:#38bdf8;color:white;text-decoration:none;border-radius:10px;font-weight:bold;font-size:1.1em;'>📝 Thêm 10 Bài Viết Ngay</a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit(1);
}
?>

