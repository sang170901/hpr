<?php
/**
 * Script to create posts table for news/blog system
 */

require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();
    
    // Create posts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        excerpt TEXT,
        author TEXT,
        status TEXT DEFAULT 'draft',
        display_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "✅ Bảng 'posts' đã được tạo thành công!<br><br>";
    
    // Insert sample posts
    $samplePosts = [
        [
            'title' => 'Vật Liệu Xây Dựng Chất Lượng Cao',
            'content' => 'Chúng tôi cung cấp đầy đủ các loại vật liệu xây dựng từ cơ bản đến cao cấp. Với hơn 10 năm kinh nghiệm trong ngành, chúng tôi cam kết mang đến những sản phẩm chất lượng nhất cho mọi công trình.',
            'excerpt' => 'Cung cấp vật liệu xây dựng chất lượng cao với giá cả cạnh tranh',
            'author' => 'Admin',
            'status' => 'published',
            'display_order' => 1
        ],
        [
            'title' => 'Công Nghệ Xây Dựng Hiện Đại',
            'content' => 'Ứng dụng công nghệ tiên tiến vào ngành xây dựng giúp tối ưu hóa thời gian và chi phí. Chúng tôi luôn cập nhật những công nghệ mới nhất để phục vụ khách hàng tốt nhất.',
            'excerpt' => 'Áp dụng công nghệ hiện đại trong xây dựng',
            'author' => 'Admin',
            'status' => 'published',
            'display_order' => 2
        ],
        [
            'title' => 'Giải Pháp Xây Dựng Bền Vững',
            'content' => 'Xây dựng xanh và bền vững là xu hướng của tương lai. Chúng tôi cung cấp các giải pháp xây dựng thân thiện với môi trường, tiết kiệm năng lượng và bền vững theo thời gian.',
            'excerpt' => 'Xây dựng xanh và bền vững cho tương lai',
            'author' => 'Admin',
            'status' => 'published',
            'display_order' => 3
        ],
        [
            'title' => 'Tư Vấn Thiết Kế Miễn Phí',
            'content' => 'Đội ngũ kiến trúc sư và kỹ sư có kinh nghiệm của chúng tôi sẵn sàng tư vấn thiết kế miễn phí cho mọi công trình. Liên hệ ngay để nhận tư vấn chuyên nghiệp.',
            'excerpt' => 'Nhận tư vấn thiết kế miễn phí từ chuyên gia',
            'author' => 'Admin',
            'status' => 'published',
            'display_order' => 4
        ],
        [
            'title' => 'Chương Trình Khuyến Mãi Tháng 10',
            'content' => 'Nhân dịp kỷ niệm 10 năm thành lập, chúng tôi có chương trình khuyến mãi đặc biệt với nhiều ưu đãi hấp dẫn. Giảm giá lên đến 30% cho nhiều sản phẩm.',
            'excerpt' => 'Khuyến mãi đặc biệt giảm giá lên đến 30%',
            'author' => 'Admin',
            'status' => 'published',
            'display_order' => 5
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, excerpt, author, status, display_order) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($samplePosts as $post) {
        $stmt->execute([
            $post['title'],
            $post['content'],
            $post['excerpt'],
            $post['author'],
            $post['status'],
            $post['display_order']
        ]);
    }
    
    echo "✅ Đã thêm 5 bài viết mẫu thành công!<br><br>";
    
    // Display all posts
    $posts = $pdo->query("SELECT * FROM posts ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Danh sách bài viết:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Tiêu đề</th><th>Trạng thái</th><th>Thứ tự</th><th>Ngày tạo</th></tr>";
    
    foreach ($posts as $post) {
        echo "<tr>";
        echo "<td>" . $post['id'] . "</td>";
        echo "<td>" . htmlspecialchars($post['title']) . "</td>";
        echo "<td>" . $post['status'] . "</td>";
        echo "<td>" . $post['display_order'] . "</td>";
        echo "<td>" . $post['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><p><a href='index.php'>← Quay về trang chủ</a> | <a href='backend/index.php'>Vào trang quản trị</a></p>";
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
?>
