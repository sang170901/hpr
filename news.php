<?php include 'inc/header-new.php'; ?>
<?php
require_once 'backend/inc/news_manager.php';

// Initialize NewsManager
$newsManager = new NewsManager();

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get news list
$newsList = $newsManager->getNews($category, $search);

// Get categories for filter
$categories = $newsManager->getCategories();
?>

<style>
    :root {
        --primary-color: #0d9488;
        --secondary-color: #f0fdfa;
        --accent-color: #14b8a6;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #d1fae5;
        --success-color: #10b981;
        --warning-color: #f59e0b;
    }

    .news-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    }

    /* Hero Header */
    .news-hero {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        border-radius: 20px;
        padding: 4rem 2rem;
        text-align: center;
        color: white;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .news-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="20" cy="80" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }

    .news-hero h1 {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }

    .news-hero .subtitle {
        font-size: 1.3rem;
        opacity: 0.9;
        font-weight: 300;
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }

    /* Modern Filters */
    .news-filters {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        border: 1px solid var(--border-color);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        align-items: start;
    }

    .category-section h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .category-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .category-pill {
        padding: 0.75rem 1.5rem;
        background: var(--secondary-color);
        color: var(--text-secondary);
        border-radius: 50px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .category-pill::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .category-pill:hover::before {
        left: 100%;
    }

    .category-pill:hover,
    .category-pill.active {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    }

    .search-section h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-wrapper {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--secondary-color);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 1.1rem;
    }

    /* Main Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
    }

    /* Article Cards */
    .articles-section h2 {
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .article-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .article-image {
        height: 200px;
        background: linear-gradient(45deg, #0d9488, #14b8a6);
        position: relative;
        overflow: hidden;
    }

    .article-image::after {
        content: '📰';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 3rem;
        opacity: 0.3;
    }

    .article-content {
        padding: 2rem;
    }

    .article-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .article-category {
        background: var(--primary-color);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .article-date {
        color: var(--text-secondary);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .article-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
        margin-bottom: 1rem;
    }

    .article-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .article-title a:hover {
        color: var(--primary-color);
    }

    .article-excerpt {
        color: var(--text-secondary);
        line-height: 1.7;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    .read-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .read-more-btn:hover {
        background: var(--accent-color);
        transform: translateX(5px);
    }

    /* Sidebar */
    .sidebar {
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .sidebar-widget {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        border: 1px solid var(--border-color);
    }

    .widget-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .recent-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .recent-item:last-child {
        border-bottom: none;
    }

    .recent-image {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .recent-content {
        flex: 1;
    }

    .recent-title {
        font-weight: 600;
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }

    .recent-title a {
        color: var(--text-primary);
        text-decoration: none;
    }

    .recent-title a:hover {
        color: var(--primary-color);
    }

    .recent-meta {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    /* Stats Widget */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        background: var(--secondary-color);
        border-radius: 12px;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .sidebar {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .news-page {
            padding: 1rem 0.5rem;
        }

        .news-hero {
            padding: 2rem 1rem;
        }

        .news-hero h1 {
            font-size: 2.2rem;
        }

        .filter-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .category-pills {
            justify-content: center;
        }

        .article-content {
            padding: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="news-page">
    <!-- Hero Header -->
    <div class="news-hero">
        <h1>📰 Bản Tin Vật Tư</h1>
        <p class="subtitle">Cập nhật thông tin mới nhất về vật liệu xây dựng, công nghệ và xu hướng thị trường</p>
    </div>

    <!-- Modern Filters -->
    <div class="news-filters">
        <div class="filter-grid">
            <div class="category-section">
                <h3><i class="fas fa-layer-group"></i> Danh mục tin tức</h3>
                <div class="category-pills">
                    <a href="news-modern.php" class="category-pill <?php echo empty($category) ? 'active' : ''; ?>">
                        Tất cả
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="news-modern.php?category=<?php echo urlencode($cat); ?>" 
                           class="category-pill <?php echo $category === $cat ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="search-section">
                <h3><i class="fas fa-search"></i> Tìm kiếm bài viết</h3>
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           class="search-input"
                           placeholder="Nhập từ khóa..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           onkeypress="if(event.key==='Enter') window.location.href='news-modern.php?search='+encodeURIComponent(this.value)">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Articles Section -->
        <div class="articles-section">
            <h2><i class="fas fa-newspaper"></i> Bài viết mới nhất</h2>
            
            <?php foreach ($newsList as $news): ?>
            <article class="article-card">
                <div class="article-image"></div>
                <div class="article-content">
                    <div class="article-meta">
                        <span class="article-category"><?php echo $news['category']; ?></span>
                        <span class="article-date">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo date('d/m/Y', strtotime($news['published_date'])); ?>
                        </span>
                    </div>
                    
                    <h3 class="article-title">
                        <a href="article-detail.php?slug=<?php echo $news['slug']; ?>">
                            <?php echo htmlspecialchars($news['title']); ?>
                        </a>
                    </h3>
                    
                    <p class="article-excerpt">
                        <?php echo htmlspecialchars($news['excerpt']); ?>
                    </p>
                    
                    <a href="article-detail.php?slug=<?php echo $news['slug']; ?>" class="read-more-btn">
                        Đọc tiếp <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Recent Posts Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">
                    <i class="fas fa-clock"></i> Bài viết gần đây
                </h3>
                
                <?php 
                $recentNews = array_slice($newsList, 0, 4);
                foreach ($recentNews as $recent): 
                ?>
                <div class="recent-item">
                    <div class="recent-image">
                        📝
                    </div>
                    <div class="recent-content">
                        <div class="recent-title">
                            <a href="article-detail.php?slug=<?php echo $recent['slug']; ?>">
                                <?php echo htmlspecialchars($recent['title']); ?>
                            </a>
                        </div>
                        <div class="recent-meta">
                            <?php echo date('d/m/Y', strtotime($recent['published_date'])); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Stats Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">
                    <i class="fas fa-chart-line"></i> Thống kê
                </h3>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($categories); ?></div>
                        <div class="stat-label">Danh mục</div>
                    </div>
                </div>
            </div>

            <!-- Categories Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">
                    <i class="fas fa-tags"></i> Danh mục
                </h3>
                
                <?php foreach ($categories as $cat): ?>
                <div style="margin-bottom: 0.5rem;">
                    <a href="news-modern.php?category=<?php echo urlencode($cat); ?>" 
                       style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">
                        <i class="fas fa-tag" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>