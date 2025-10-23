<?php
/**
 * News Section Component
 * Hiển thị 5 tin tức mới nhất từ NewsManager
 */

// Lấy dữ liệu tin tức từ NewsManager
try {
    require_once __DIR__ . '/../backend/inc/news_manager.php';
    
    $newsManager = new NewsManager();
    $allNews = $newsManager->getNews();
    
    // Lấy 5 tin tức mới nhất
    $newsList = array_slice($allNews, 0, 5);
    
    // Debug: Hiển thị thông tin (xóa sau khi fix)
    // echo "<!-- DEBUG: Found " . count($newsList) . " posts -->";
    
} catch (Exception $e) {
    $newsList = [];
    error_log("Error fetching news: " . $e->getMessage());
    // Debug
    echo "<!-- ERROR: " . htmlspecialchars($e->getMessage()) . " -->";
}
?>

<style>
/* Modern News Section Styles */
.news-section {
    padding: 80px 20px;
    background: #ffffff;
    position: relative;
}

.news-container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
}

.news-header {
    text-align: center;
    margin-bottom: 60px;
    position: relative;
}

.news-header-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(56, 189, 248, 0.2);
}

.news-header-icon i {
    font-size: 36px;
    color: #38bdf8;
}

.news-header h2 {
    font-size: 2.8rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 16px;
    position: relative;
    display: inline-block;
}

.news-header p {
    font-size: 1.2rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

/* Modern News Grid */
.news-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-bottom: 50px;
}

.news-item {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(56, 189, 248, 0.1);
    transition: all 0.3s ease;
    border: 2px solid #f0f9ff;
    display: flex;
    flex-direction: column;
}

.news-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(56, 189, 248, 0.25);
    border-color: #bae6fd;
}

.news-image {
    width: 100%;
    height: 220px;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    position: relative;
    overflow: hidden;
}

.news-image::after {
    content: '📰';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 4rem;
    opacity: 0.3;
}

.news-category {
    position: absolute;
    top: 16px;
    left: 16px;
    background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
}

.news-content {
    padding: 28px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.news-item h3 {
    font-size: 1.3rem;
    margin-bottom: 12px;
    line-height: 1.4;
}

.news-item h3 a {
    color: #1e293b;
    text-decoration: none;
    transition: color 0.3s ease;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-item:hover h3 a {
    color: #38bdf8;
}

.news-date {
    font-size: 0.9rem;
    color: #94a3b8;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.news-date i {
    color: #38bdf8;
    font-size: 0.85rem;
}

.news-description {
    font-size: 1rem;
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 20px;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-read-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #38bdf8;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    padding: 12px 24px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 12px;
    align-self: flex-start;
}

.news-read-more:hover {
    background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
    color: white;
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3);
}

.news-read-more i {
    transition: transform 0.3s ease;
}

.news-read-more:hover i {
    transform: translateX(5px);
}

.news-more {
    text-align: center;
    margin-top: 40px;
}

.news-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 18px 48px;
    background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
    color: white;
    text-decoration: none;
    border-radius: 60px;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(56, 189, 248, 0.3);
    position: relative;
    overflow: hidden;
}

.news-more-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.news-more-btn:hover::before {
    left: 100%;
}

.news-more-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(56, 189, 248, 0.4);
    background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
}

.news-more-btn i {
    transition: transform 0.3s ease;
}

.news-more-btn:hover i {
    transform: translateX(5px);
}

.news-empty {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
    font-size: 1.1rem;
}

.news-empty::before {
    content: '📰';
    display: block;
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .news-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
}

@media (max-width: 768px) {
    .news-section {
        padding: 60px 15px;
    }
    
    .news-header h2 {
        font-size: 2.2rem;
    }
    
    .news-header p {
        font-size: 1rem;
    }
    
    .news-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .news-content {
        padding: 24px;
    }
    
    .news-image {
        height: 200px;
    }
    
    .news-item h3 {
        font-size: 1.15rem;
    }
    
    .news-more-btn {
        padding: 15px 36px;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .news-header h2 {
        font-size: 1.8rem;
    }
    
    .news-header-icon {
        width: 60px;
        height: 60px;
    }
    
    .news-header-icon i {
        font-size: 28px;
    }
    
    .news-item h3 {
        font-size: 1.05rem;
    }
    
    .news-description {
        font-size: 0.95rem;
    }
    
    .news-content {
        padding: 20px;
    }
}

/* News Modal Styles */
.news-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.news-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
}

.news-modal-content {
    position: relative;
    background: white;
    border-radius: 15px;
    max-width: 800px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

.news-modal-header {
    padding: 30px 30px 20px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: sticky;
    top: 0;
    background: white;
    border-radius: 15px 15px 0 0;
}

.news-modal-header h2 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.8rem;
    line-height: 1.3;
    flex: 1;
    margin-right: 20px;
}

.news-modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    color: #94a3b8;
    cursor: pointer;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.news-modal-close:hover {
    background: #f1f5f9;
    color: #64748b;
    transform: scale(1.1);
}

.news-modal-body {
    padding: 20px 30px 30px;
}

.news-modal-date {
    color: #64748b;
    font-size: 1rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.news-modal-date::before {
    content: '📅';
    font-size: 1.1rem;
}

.news-modal-text {
    color: #374151;
    font-size: 1.1rem;
    line-height: 1.8;
    white-space: pre-line;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        transform: translateY(50px);
        opacity: 0;
    }
    to { 
        transform: translateY(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .news-modal-content {
        width: 95%;
        max-height: 90vh;
    }
    
    .news-modal-header {
        padding: 20px 20px 15px;
    }
    
    .news-modal-header h2 {
        font-size: 1.5rem;
        margin-right: 15px;
    }
    
    .news-modal-body {
        padding: 15px 20px 25px;
    }
    
    .news-modal-text {
        font-size: 1rem;
    }
}
</style>

<section class="news-section">
    <div class="news-container">
        <div class="news-header">
            <div class="news-header-icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <h2>Bản tin VNMaterial</h2>
            <p>Cập nhật tin tức mới nhất về vật liệu xây dựng, công nghệ và xu hướng thị trường</p>
        </div>
        
        <?php if (!empty($newsList)): ?>
            <div class="news-grid">
                <?php foreach ($newsList as $news): ?>
                <div class="news-item">
                    <div class="news-image">
                        <?php if (!empty($news['category'])): ?>
                            <div class="news-category"><?php echo htmlspecialchars($news['category']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="news-content">
                        <h3>
                            <a href="article-detail.php?slug=<?php echo urlencode($news['slug']); ?>">
                                <?php echo htmlspecialchars($news['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="news-date">
                            <i class="far fa-calendar-alt"></i>
                            <?php echo date('d/m/Y', strtotime($news['published_date'])); ?>
                        </p>
                        
                        <p class="news-description">
                            <?php echo htmlspecialchars($news['excerpt']); ?>
                        </p>
                        
                        <a href="article-detail.php?slug=<?php echo urlencode($news['slug']); ?>" class="news-read-more">
                            <span>Đọc tiếp</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="news-more">
                <a href="news-modern.php" class="news-more-btn">
                    <span>Xem tất cả tin tức</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="news-empty">
                <p>Chưa có tin tức nào được công bố.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

