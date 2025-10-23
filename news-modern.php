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
        --primary-color: #38bdf8; /* pastel sky blue */
        --primary-600: #0ea5e9;
        --secondary-color: #f0f9ff;
        --accent-color: #22d3ee;
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --border-color: #e0f2fe;
        --success-color: #10b981;
        --warning-color: #f59e0b;
    }

    body {
        background: #f0f9ff;
    }

    .news-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
        font-family: 'Nunito Sans', 'Open Sans', 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
    }

    /* Hero Header */
    .news-hero {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-radius: 20px;
        padding: 4rem 2rem;
        text-align: center;
        color: #0369a1;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(56, 189, 248, 0.2);
        border: 2px solid #7dd3fc;
    }

    .news-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(56,189,248,0.15)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.5;
        pointer-events: none;
    }

    .news-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: #0284c7;
        text-shadow: 0 2px 4px rgba(2, 132, 199, 0.1);
        position: relative;
        z-index: 1;
    }

    .news-hero .subtitle {
        font-size: 1.3rem;
        font-weight: 400;
        line-height: 1.6;
        color: #0ea5e9;
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
        background: white;
        color: var(--text-secondary);
        border-radius: 50px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: 2px solid #e0f2fe;
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
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(56, 189, 248, 0.35);
        border-color: transparent;
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
        border: 2px solid #7dd3fc;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
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
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
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
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.25);
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
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.25);
    }

    .read-more-btn:hover {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(56, 189, 248, 0.35);
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
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(56, 189, 248, 0.25);
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
        padding: 1.5rem;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-radius: 12px;
        border: 1px solid #7dd3fc;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        color: #0284c7;
        margin-bottom: 0.25rem;
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