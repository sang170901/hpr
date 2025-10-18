<?php 
include 'inc/header-new.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id > 0) {
    try {
        require_once 'backend/inc/db.php';
        $pdo = getPDO();
        
        // Get post details
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND status = 'published'");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            echo "<h1>Bài viết không tồn tại</h1>";
            exit;
        }
    } catch (Exception $e) {
        echo "<h1>Lỗi: " . htmlspecialchars($e->getMessage()) . "</h1>";
        exit;
    }
} else {
    echo "<h1>ID bài viết không hợp lệ</h1>";
    exit;
}
?>

<style>
.post-detail {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.post-title {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 20px;
    line-height: 1.2;
}

.post-meta {
    color: #7f8c8d;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #ecf0f1;
}

.post-date {
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.post-date::before {
    content: '📅';
    font-size: 1.2rem;
}

.post-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #34495e;
}

.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.back-link:hover {
    color: #2980b9;
}

.back-link::before {
    content: '← ';
    margin-right: 5px;
}
</style>

<div class="post-detail">
    <a href="index.php" class="back-link">Quay lại trang chủ</a>
    
    <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
    
    <div class="post-meta">
        <div class="post-date">
            <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
        </div>
    </div>
    
    <div class="post-content">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>