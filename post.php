<?php 
include 'inc/header-new.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id > 0) {
    try {
        require_once 'backend/inc/news_manager.php';
        
        // Get all news from NewsManager
        $newsManager = new NewsManager();
        $allNews = $newsManager->getNews();
        
        // Find the specific post by ID
        $post = null;
        foreach ($allNews as $news) {
            if ($news['id'] == $post_id) {
                $post = $news;
                break;
            }
        }
        
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
    max-width: 900px;
    margin: 0 auto;
    padding: 0;
    background: transparent;
    box-shadow: none;
    border-radius: 0;
}

.post-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 40px 60px;
    margin-bottom: 0;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
}

.post-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9));
    backdrop-filter: blur(10px);
}

.post-content-wrapper {
    position: relative;
    z-index: 2;
}

.post-title {
    font-size: 3rem;
    color: white;
    margin-bottom: 30px;
    line-height: 1.2;
    font-weight: 700;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.post-meta {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    font-size: 1.1rem;
}

.post-date, .post-category {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.2);
    padding: 10px 20px;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.post-date::before {
    content: '📅';
    font-size: 1.3rem;
}

.post-category {
    color: rgba(255, 255, 255, 0.95);
    font-weight: 600;
}

.post-category::before {
    content: '🏷️';
    font-size: 1.2rem;
}

.post-body {
    background: white;
    padding: 60px 40px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    margin-top: -30px;
    position: relative;
    z-index: 10;
}

.post-content {
    color: #2c3e50;
    font-size: 1.2rem;
    line-height: 1.8;
    white-space: pre-line;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 40px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    padding: 12px 25px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-radius: 25px;
    border: 2px solid rgba(102, 126, 234, 0.2);
}

.back-link:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.back-link::before {
    content: '←';
    font-size: 1.2rem;
    font-weight: bold;
    transition: transform 0.3s ease;
}

.back-link:hover::before {
    transform: translateX(-3px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .post-hero {
        padding: 60px 20px 40px;
        border-radius: 15px;
    }
    
    .post-title {
        font-size: 2.2rem;
    }
    
    .post-meta {
        gap: 15px;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .post-body {
        padding: 40px 25px;
        margin-top: -20px;
        border-radius: 15px;
    }
    
    .post-content {
        font-size: 1.1rem;
    }
    
    .back-link {
        margin-bottom: 30px;
        font-size: 1rem;
        padding: 10px 20px;
    }
}

@media (max-width: 480px) {
    .post-hero {
        padding: 40px 15px 30px;
    }
    
    .post-title {
        font-size: 1.8rem;
    }
    
    .post-body {
        padding: 30px 20px;
    }
}
</style>

<div class="post-detail">
    <div class="post-hero">
        <div class="post-content-wrapper">
            <a href="index.php" class="back-link">Quay lại trang chủ</a>
            
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-meta">
                <div class="post-date">
                    <?php echo date('d/m/Y H:i', strtotime($post['published_date'])); ?>
                </div>
                <div class="post-category">
                    <?php echo htmlspecialchars($post['category']); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="post-body">
        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>