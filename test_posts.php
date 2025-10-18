<?php
// Test database connection và kiểm tra dữ liệu posts
echo "<h2>Test Database Connection</h2>";

try {
    require_once 'backend/inc/db.php';
    $pdo = getPDO();
    echo "<p>✅ Database connection successful!</p>";
    
    // Kiểm tra bảng posts
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM posts");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>📊 Total posts in database: " . $count['count'] . "</p>";
    
    // Lấy tất cả posts
    $stmt = $pdo->query("SELECT id, title, content, created_at, status FROM posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📝 All posts in database:</h3>";
    if (empty($posts)) {
        echo "<p>❌ No posts found in database!</p>";
    } else {
        foreach ($posts as $post) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
            echo "<strong>ID:</strong> " . $post['id'] . "<br>";
            echo "<strong>Title:</strong> " . htmlspecialchars($post['title']) . "<br>";
            echo "<strong>Content:</strong> " . htmlspecialchars(substr($post['content'], 0, 100)) . "...<br>";
            echo "<strong>Created:</strong> " . $post['created_at'] . "<br>";
            echo "<strong>Status:</strong> " . $post['status'] . "<br>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>