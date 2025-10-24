<?php
require_once 'backend/inc/db.php';

echo "<h2>🔍 Kiểm Tra Bài Viết</h2>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} table{background:white;border-collapse:collapse;width:100%;margin:20px 0;} th,td{padding:12px;text-align:left;border:1px solid #ddd;} th{background:#38bdf8;color:white;} .success{color:green;} .error{color:red;}</style>";

try {
    $pdo = getPDO();
    
    // Kiểm tra bảng có tồn tại không
    echo "<h3>1. Kiểm tra bảng posts</h3>";
    try {
        $tables = $pdo->query("SHOW TABLES LIKE 'posts'")->fetchAll();
        if (count($tables) > 0) {
            echo "<p class='success'>✓ Bảng posts tồn tại</p>";
        } else {
            echo "<p class='error'>✗ Bảng posts KHÔNG tồn tại!</p>";
            echo "<p><a href='backend/scripts/create_posts_table.php'>→ Tạo bảng posts</a></p>";
            exit;
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Lỗi: " . $e->getMessage() . "</p>";
        exit;
    }
    
    // Đếm số bài viết
    echo "<h3>2. Số lượng bài viết</h3>";
    $total = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $published = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
    $draft = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();
    
    echo "<table>";
    echo "<tr><th>Trạng thái</th><th>Số lượng</th></tr>";
    echo "<tr><td><strong>Tổng cộng</strong></td><td><strong style='font-size:1.5em;color:#38bdf8;'>$total</strong></td></tr>";
    echo "<tr><td>Published</td><td style='color:green;font-weight:bold;'>$published</td></tr>";
    echo "<tr><td>Draft</td><td style='color:orange;'>$draft</td></tr>";
    echo "</table>";
    
    // Hiển thị danh sách bài viết
    echo "<h3>3. Danh sách tất cả bài viết</h3>";
    $posts = $pdo->query("SELECT id, title, category, status, created_at FROM posts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($posts) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tiêu đề</th><th>Danh mục</th><th>Trạng thái</th><th>Ngày tạo</th></tr>";
        foreach ($posts as $post) {
            $statusColor = $post['status'] == 'published' ? 'green' : 'orange';
            echo "<tr>";
            echo "<td>{$post['id']}</td>";
            echo "<td>" . htmlspecialchars($post['title']) . "</td>";
            echo "<td>" . htmlspecialchars($post['category']) . "</td>";
            echo "<td style='color:$statusColor;font-weight:bold;'>{$post['status']}</td>";
            echo "<td>{$post['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='padding:40px;background:white;text-align:center;border-radius:10px;'>❌ Không có bài viết nào trong database!</p>";
        echo "<p style='text-align:center;'><a href='backend/scripts/add_more_posts.php' style='display:inline-block;padding:15px 30px;background:#38bdf8;color:white;text-decoration:none;border-radius:10px;font-weight:bold;'>📝 Thêm 10 Bài Viết</a></p>";
    }
    
    // Kiểm tra news.php đang query như thế nào
    echo "<h3>4. Kiểm tra file news.php</h3>";
    if (file_exists('news.php')) {
        $newsContent = file_get_contents('news.php');
        if (strpos($newsContent, 'LIMIT') !== false) {
            preg_match('/LIMIT\s+(\d+)/', $newsContent, $matches);
            if (isset($matches[1])) {
                echo "<p>⚠️ File news.php có LIMIT = <strong style='color:red;font-size:1.2em;'>{$matches[1]}</strong></p>";
                echo "<p>→ Đây là lý do chỉ hiển thị {$matches[1]} bài!</p>";
            }
        } else {
            echo "<p>✓ Không có LIMIT trong query</p>";
        }
    }
    
    echo "<hr>";
    echo "<p style='text-align:center;'>";
    echo "<a href='news.php' style='display:inline-block;padding:12px 24px;background:#059669;color:white;text-decoration:none;border-radius:8px;margin:10px;'>📰 Xem Trang Tin Tức</a>";
    echo "<a href='backend/scripts/add_more_posts.php' style='display:inline-block;padding:12px 24px;background:#38bdf8;color:white;text-decoration:none;border-radius:8px;margin:10px;'>➕ Thêm Bài Viết</a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Lỗi: " . $e->getMessage() . "</p>";
}
?>

