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
/* News Section Styles */
.news-section {
    padding: 60px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.news-container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
}

.news-header {
    text-align: center;
    margin-bottom: 50px;
}

.news-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    position: relative;
    display: inline-block;
}

.news-header h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 2px;
}

/* News Carousel */
.news-carousel-wrapper {
    position: relative;
    overflow: hidden;
    margin-bottom: 40px;
    padding: 0 70px; /* Khoảng cách cho nút to hơn */
}

.news-grid {
    display: flex;
    gap: 30px;
    transition: transform 0.5s ease;
    width: fit-content;
}

.news-item {
    flex: 0 0 calc(50% - 15px); /* Mỗi item chiếm 50% trừ gap */
    min-width: 450px; /* Đảm bảo có kích thước tối thiểu */
    max-width: 550px;
}

.news-item {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}

.news-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
}

.news-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.news-item h3 {
    font-size: 1.3rem;
    margin-bottom: 12px;
    line-height: 1.4;
}

.news-item h3 a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.news-item h3 a:hover {
    color: #3b82f6;
}

.news-date {
    font-size: 0.9rem;
    color: #7f8c8d;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.news-date::before {
    content: '📅';
    font-size: 0.85rem;
}

.news-description {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
    margin-bottom: 15px;
}

.news-read-more {
    display: inline-block;
    color: #3b82f6;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.news-read-more:hover {
    color: #8b5cf6;
    transform: translateX(5px);
}

.news-read-more::after {
    content: ' →';
    transition: transform 0.3s ease;
}

.news-read-more:hover::after {
    transform: translateX(3px);
}

.news-more {
    text-align: center;
    margin-top: 40px;
}

.news-more-btn {
    display: inline-block;
    padding: 15px 40px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(59, 130, 246, 0.3);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.news-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
}

/* News Navigation Buttons */
.news-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
    border: 2px solid rgba(59, 130, 246, 0.4);
    color: #3b82f6;
    width: 55px;
    height: 55px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 24px;
    font-weight: bold;
    transition: all 0.3s ease;
    backdrop-filter: blur(15px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.news-nav-btn:hover {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    transform: translateY(-50%) scale(1.15);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
    border-color: rgba(255, 255, 255, 0.5);
}

.news-nav-btn.disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.news-prev {
    left: 0;
}

.news-next {
    right: 0;
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
    .news-item {
        min-width: 350px;
        max-width: 450px;
    }
}

@media (max-width: 768px) {
    .news-section {
        padding: 40px 15px;
    }
    
    .news-header h2 {
        font-size: 2rem;
    }
    
    .news-carousel-wrapper {
        padding: 0 50px;
    }
    
    .news-item {
        min-width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
        padding: 20px;
    }
    
    .news-item h3 {
        font-size: 1.1rem;
    }
    
    .news-more-btn {
        padding: 12px 30px;
        font-size: 1rem;
    }
    
    .news-carousel-wrapper {
        padding: 0 55px;
    }
    
    .news-prev {
        left: 0;
    }
    
    .news-next {
        right: 0;
    }
    
    .news-nav-btn {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .news-header h2 {
        font-size: 1.6rem;
    }
    
    .news-item h3 {
        font-size: 1rem;
    }
    
    .news-description {
        font-size: 0.95rem;
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
            <h2>Bản tin VNMaterial</h2>
        </div>
        
        <?php if (!empty($newsList)): ?>
            <div class="news-carousel-wrapper">
                <!-- Navigation Buttons -->
                <button class="news-nav-btn news-prev" onclick="moveNewsCarousel(-1)">‹</button>
                <button class="news-nav-btn news-next" onclick="moveNewsCarousel(1)">›</button>
                
                <div class="news-grid" id="newsGrid">
                    <?php foreach ($newsList as $news): ?>
                    <div class="news-item">
                        <h3>
                            <a href="article-detail.php?slug=<?php echo urlencode($news['slug']); ?>">
                                <?php echo htmlspecialchars($news['title']); ?>
                            </a>
                        </h3>
                        <p class="news-date">
                            <?php echo date('d/m/Y', strtotime($news['published_date'])); ?>
                        </p>
                        <p class="news-description">
                            <?php echo htmlspecialchars($news['excerpt']); ?>
                        </p>
                        <a href="article-detail.php?slug=<?php echo urlencode($news['slug']); ?>" class="news-read-more">
                            Đọc thêm
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="news-more">
                <a href="news-modern.php" class="news-more-btn">Xem tất cả tin tức</a>
            </div>
        <?php else: ?>
            <div class="news-empty">
                <p>Chưa có tin tức nào được công bố.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// News Carousel JavaScript
let currentNewsPage = 0;
const itemsPerPage = 2; // Hiển thị 2 bài trên 1 hàng

function moveNewsCarousel(direction) {
    const newsGrid = document.getElementById('newsGrid');
    const newsItems = newsGrid.querySelectorAll('.news-item');
    const totalItems = newsItems.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    
    // Update current page
    currentNewsPage += direction;
    
    // Loop around
    if (currentNewsPage < 0) {
        currentNewsPage = totalPages - 1;
    } else if (currentNewsPage >= totalPages) {
        currentNewsPage = 0;
    }
    
    // Calculate transform based on item width + gap
    const firstItem = newsItems[0];
    const itemWidth = firstItem.offsetWidth;
    const gap = 30; // Gap between items
    const offset = currentNewsPage * (itemWidth * 2 + gap * 2); // 2 items + gaps
    
    newsGrid.style.transform = `translateX(-${offset}px)`;
    
    // Update button states
    updateNewsButtons(totalPages);
}

function updateNewsButtons(totalPages) {
    const prevBtn = document.querySelector('.news-prev');
    const nextBtn = document.querySelector('.news-next');
    
    // Luôn enable nút để có thể cuộn vòng
    prevBtn.classList.remove('disabled');
    nextBtn.classList.remove('disabled');
}

// Auto-scroll carousel
let newsAutoScroll = setInterval(() => {
    moveNewsCarousel(1);
}, 6000); // 6 giây tự động chuyển

// Pause on hover
document.querySelector('.news-carousel-wrapper')?.addEventListener('mouseenter', () => {
    clearInterval(newsAutoScroll);
});

// Resume on mouse leave
document.querySelector('.news-carousel-wrapper')?.addEventListener('mouseleave', () => {
    newsAutoScroll = setInterval(() => {
        moveNewsCarousel(1);
    }, 6000);
});

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    const newsGrid = document.getElementById('newsGrid');
    if (newsGrid) {
        const totalItems = newsGrid.querySelectorAll('.news-item').length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        updateNewsButtons(totalPages);
    }
});

// Recalculate on window resize
window.addEventListener('resize', () => {
    currentNewsPage = 0;
    const newsGrid = document.getElementById('newsGrid');
    if (newsGrid) {
        newsGrid.style.transform = 'translateX(0)';
    }
});
</script>
