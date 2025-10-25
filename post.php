<?php
header('Content-Type: text/html; charset=UTF-8');
require_once 'backend/inc/db.php';

$slug = $_GET['slug'] ?? '';
$pdo = getPDO();

// Lấy bài viết
$stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = ? AND status = "published"');
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: news-modern.php');
    exit;
}

// Tăng lượt xem
$pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = ?')->execute([$post['id']]);

// Xử lý submit comment
$commentSubmitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $content = trim($_POST['content'] ?? '');
    $userId = $isLoggedIn ? $_SESSION['user_id'] : null;
    
    // Nếu đã đăng nhập, lấy thông tin từ session
    if ($isLoggedIn) {
        $name = $_SESSION['full_name'];
        $email = $_SESSION['email'];
        $website = '';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $website = trim($_POST['website'] ?? '');
    }
    
    if (!empty($name) && !empty($email) && !empty($content) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, author_name, author_email, content, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $post['id'],
            $userId,
            $name,
            $email,
            $content,
            'pending' // Mặc định chờ duyệt
        ]);
        $commentSubmitted = true;
    }
}

// Lấy comments đã duyệt (join với users để lấy avatar)
$stmt = $pdo->prepare('
    SELECT c.*, u.avatar, u.username 
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.post_id = ? AND c.status = "approved" 
    ORDER BY c.created_at DESC
');
$stmt->execute([$post['id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'inc/header-new.php';
?>

<style>
.post-detail {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.post-header {
    margin-bottom: 40px;
}

.post-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 16px;
    line-height: 1.2;
}

.post-meta {
    display: flex;
    gap: 24px;
    color: #64748b;
    font-size: 14px;
    margin-bottom: 24px;
}

.post-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.post-featured-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 32px;
}

.post-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #334155;
    margin-bottom: 48px;
}

.post-content h1, .post-content h2, .post-content h3 {
    margin-top: 32px;
    margin-bottom: 16px;
    color: #1e293b;
}

.post-content ul, .post-content ol {
    margin: 16px 0;
    padding-left: 32px;
}

.post-content li {
    margin: 8px 0;
}

.post-content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 24px 0;
}

/* Comments Section */
.comments-section {
    margin-top: 64px;
    padding-top: 40px;
    border-top: 2px solid #e2e8f0;
}

.comments-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 24px;
}

.comment-form {
    background: #f8fafc;
    padding: 32px;
    border-radius: 12px;
    margin-bottom: 40px;
}

.comment-form-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1e293b;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #475569;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.submit-btn {
    background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
    color: white;
    padding: 12px 32px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(56, 189, 248, 0.3);
}

.success-message {
    background: #dcfce7;
    border: 2px solid #86efac;
    color: #059669;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.comments-list {
    margin-top: 32px;
}

.comment-item {
    background: white;
    padding: 24px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid #e2e8f0;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.comment-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
}

.comment-author {
    font-weight: 700;
    color: #1e293b;
}

.comment-date {
    color: #94a3b8;
    font-size: 13px;
}

.comment-content {
    color: #475569;
    line-height: 1.6;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #38bdf8;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 24px;
    transition: all 0.3s;
}

.back-link:hover {
    color: #0ea5e9;
    transform: translateX(-4px);
}
</style>

<div class="post-detail">
    <a href="news-modern.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
    
    <article>
        <div class="post-header">
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-meta">
                <div class="post-meta-item">
                    <i class="fas fa-calendar"></i>
                    <?php echo date('d/m/Y', strtotime($post['published_at'])); ?>
                </div>
                <div class="post-meta-item">
                    <i class="fas fa-eye"></i>
                    <?php echo number_format($post['views']); ?> lượt xem
                </div>
                <?php if($post['category']): ?>
                <div class="post-meta-item">
                    <i class="fas fa-folder"></i>
                    <?php echo htmlspecialchars($post['category']); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if($post['featured_image']): ?>
            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                 alt="<?php echo htmlspecialchars($post['title']); ?>"
                 class="post-featured-image">
            <?php endif; ?>
        </div>
        
        <div class="post-content">
            <?php echo $post['content']; ?>
        </div>
    </article>
    
    <!-- Comments Section -->
    <div class="comments-section">
        <h2 class="comments-title">
            <i class="fas fa-comments"></i> Bình luận (<?php echo count($comments); ?>)
        </h2>
        
        <!-- Comment Form -->
        <div class="comment-form">
            <h3 class="comment-form-title">Để lại bình luận</h3>
            
            <?php if($commentSubmitted): ?>
            <div class="success-message">
                <strong>✓ Cảm ơn bạn đã bình luận!</strong><br>
                Bình luận của bạn đang chờ duyệt và sẽ được hiển thị sau khi admin xác nhận.
            </div>
            <?php endif; ?>
            
            <?php if ($isLoggedIn): ?>
                <!-- Logged in user -->
                <div style="background: #e0f2fe; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                    <img src="<?php echo htmlspecialchars($userData['avatar']); ?>" 
                         alt="<?php echo htmlspecialchars($userData['full_name']); ?>"
                         style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div style="font-weight: 600; color: #0369a1;">
                            Đăng bình luận với tên <?php echo htmlspecialchars($userData['full_name']); ?>
                        </div>
                        <div style="font-size: 13px; color: #64748b;">
                            @<?php echo htmlspecialchars($userData['username']); ?>
                        </div>
                    </div>
                </div>
                
                <form method="post" action="">
                    <div class="form-group">
                        <label>Nội dung <span style="color:red">*</span></label>
                        <textarea name="content" required placeholder="Viết bình luận của bạn..."></textarea>
                    </div>
                    
                    <button type="submit" name="submit_comment" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Gửi bình luận
                    </button>
                </form>
            <?php else: ?>
                <!-- Not logged in -->
                <div style="background: #fef3c7; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>💡 Tip:</strong> 
                    <a href="/vnmt/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" style="color: #0284c7; font-weight: 600;">Đăng nhập</a> 
                    để bình luận nhanh hơn, không cần nhập thông tin mỗi lần!
                </div>
                
                <form method="post" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Họ tên <span style="color:red">*</span></label>
                            <input type="text" name="name" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="form-group">
                            <label>Email <span style="color:red">*</span></label>
                            <input type="email" name="email" required placeholder="email@example.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nội dung <span style="color:red">*</span></label>
                        <textarea name="content" required placeholder="Viết bình luận của bạn..."></textarea>
                    </div>
                    
                    <button type="submit" name="submit_comment" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Gửi bình luận
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Comments List -->
        <?php if(count($comments) > 0): ?>
        <div class="comments-list">
            <?php foreach($comments as $comment): ?>
            <div class="comment-item">
                <div class="comment-header">
                    <?php if ($comment['avatar']): ?>
                        <img src="<?php echo htmlspecialchars($comment['avatar']); ?>" 
                             alt="<?php echo htmlspecialchars($comment['author_name']); ?>"
                             class="comment-avatar"
                             style="object-fit: cover;">
                    <?php else: ?>
                        <div class="comment-avatar">
                            <?php echo strtoupper(mb_substr($comment['author_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="comment-author">
                            <?php echo htmlspecialchars($comment['author_name']); ?>
                            <?php if ($comment['username']): ?>
                                <span style="font-weight: 400; color: #64748b; font-size: 13px;">
                                    @<?php echo htmlspecialchars($comment['username']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></div>
                    </div>
                </div>
                <div class="comment-content">
                    <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="text-align: center; color: #94a3b8; padding: 40px;">
            Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
        </p>
        <?php endif; ?>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>
