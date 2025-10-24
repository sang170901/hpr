<?php
require_once 'backend/inc/db.php';

echo "<h2>🔍 Kiểm Tra Slugs</h2>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} table{background:white;border-collapse:collapse;width:100%;} th,td{padding:12px;text-align:left;border:1px solid #ddd;} th{background:#38bdf8;color:white;} .duplicate{background:#fee;}</style>";

try {
    $pdo = getPDO();
    
    // Lấy tất cả bài viết với slug
    $posts = $pdo->query("SELECT id, title, slug, status FROM posts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📊 Danh sách Slugs</h3>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Title</th><th>Slug</th><th>Status</th><th>Link Test</th></tr>";
    
    $slugCounts = [];
    
    foreach ($posts as $post) {
        $isDuplicate = false;
        if (!empty($post['slug'])) {
            if (isset($slugCounts[$post['slug']])) {
                $isDuplicate = true;
            }
            $slugCounts[$post['slug']] = ($slugCounts[$post['slug']] ?? 0) + 1;
        }
        
        $rowClass = $isDuplicate ? ' class="duplicate"' : '';
        
        echo "<tr$rowClass>";
        echo "<td>{$post['id']}</td>";
        echo "<td>" . htmlspecialchars($post['title']) . "</td>";
        echo "<td><code>" . htmlspecialchars($post['slug']) . "</code></td>";
        echo "<td>{$post['status']}</td>";
        echo "<td><a href='article-detail.php?slug=" . urlencode($post['slug']) . "' target='_blank'>Test →</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Kiểm tra duplicate slugs
    echo "<h3>⚠️ Slugs trùng lặp</h3>";
    $hasDuplicates = false;
    foreach ($slugCounts as $slug => $count) {
        if ($count > 1) {
            echo "<p style='color:red;'><strong>$slug</strong> - Xuất hiện $count lần</p>";
            $hasDuplicates = true;
        }
    }
    
    if (!$hasDuplicates) {
        echo "<p style='color:green;'>✓ Không có slug trùng lặp</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

