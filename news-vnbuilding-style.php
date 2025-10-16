<?php include 'inc/header-new.php'; ?>
<?php
require_once 'backend/inc/news_manager_fixed.php';

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
    .news-page {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Arial', sans-serif;
        line-height: 1.6;
        color: #2c3e50;
    }

    .news-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        text-align: center;
        margin-bottom: 3rem;
        border-bottom: 3px solid #3498db;
        padding-bottom: 1rem;
    }

    .news-filters {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 3rem;
        border-left: 4px solid #3498db;
    }

    .filter-section {
        margin-bottom: 1rem;
    }

    .filter-section:last-child {
        margin-bottom: 0;
    }

    .filter-section label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .category-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .category-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .category-btn:hover,
    .category-btn.active {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    .search-box input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 1rem;
    }

    .search-box input:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }

    /* News Articles */
    .news-article {
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .news-article:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .article-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .article-title a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .article-title a:hover {
        color: #3498db;
    }

    .article-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .article-category {
        background: #3498db;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .article-excerpt {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #495057;
        margin-bottom: 1rem;
        text-align: justify;
    }

    .read-more {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        border-bottom: 1px solid transparent;
        transition: border-color 0.3s ease;
    }

    .read-more:hover {
        border-bottom-color: #3498db;
    }

    /* Recent Posts Section */
    .recent-posts {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 8px;
        margin-top: 3rem;
        border-left: 4px solid #27ae60;
    }

    .recent-posts h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }

    .recent-item {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .recent-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .recent-item-title {
        font-weight: 600;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .recent-item-title a {
        color: #2c3e50;
        text-decoration: none;
    }

    .recent-item-title a:hover {
        color: #3498db;
    }

    .recent-item-meta {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .recent-item-category {
        background: #27ae60;
        color: white;
        padding: 0.15rem 0.5rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-right: 0.5rem;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 3rem;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .pagination a:hover,
    .pagination .current {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    @media (max-width: 768px) {
        .news-page {
            padding: 1rem 0.5rem;
        }

        .news-title {
            font-size: 2rem;
        }

        .article-title {
            font-size: 1.5rem;
        }

        .category-filters {
            justify-content: center;
        }

        .article-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<div class="news-page">
    <!-- News Title -->
    <h1 class="news-title">Bản tin vật tư</h1>

    <!-- News Filters -->
    <div class="news-filters">
        <div class="filter-section">
            <label>Danh mục tin tức:</label>
            <div class="category-filters">
                <a href="news-vnbuilding-style.php" class="category-btn <?php echo empty($category) ? 'active' : ''; ?>">
                    Tất cả
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="news-vnbuilding-style.php?category=<?php echo urlencode($cat); ?>" 
                       class="category-btn <?php echo $category === $cat ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="filter-section">
            <label>Tìm kiếm bài viết:</label>
            <div class="search-box">
                <input type="text" 
                       placeholder="Nhập từ khóa tìm kiếm..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       onkeypress="if(event.key==='Enter') window.location.href='news-vnbuilding-style.php?search='+encodeURIComponent(this.value)">
            </div>
        </div>
    </div>

    <!-- News Articles -->
    <?php foreach ($newsList as $news): ?>
    <article class="news-article">
        <h3 class="article-title">
            <a href="article-detail.php?slug=<?php echo $news['slug']; ?>">
                <?php echo htmlspecialchars($news['title']); ?>
            </a>
        </h3>
        
        <div class="article-meta">
            <span class="article-category"><?php echo $news['category']; ?></span>
            <span><?php echo date('d/m/Y', strtotime($news['published_date'])); ?></span>
        </div>
        
        <p class="article-excerpt">
            <?php echo htmlspecialchars($news['excerpt']); ?>
        </p>
        
        <a href="article-detail.php?slug=<?php echo $news['slug']; ?>" class="read-more">
            Đọc tiếp →
        </a>
    </article>
    <?php endforeach; ?>

    <!-- Recent Posts Section -->
    <div class="recent-posts">
        <h2>Bài viết gần đây</h2>
        
        <?php 
        $recentNews = array_slice($newsList, 0, 5);
        foreach ($recentNews as $recent): 
        ?>
        <div class="recent-item">
            <div>
                <div class="recent-item-title">
                    <a href="article-detail.php?slug=<?php echo $recent['slug']; ?>">
                        <?php echo htmlspecialchars($recent['title']); ?>
                    </a>
                </div>
                <div class="recent-item-meta">
                    <span class="recent-item-category"><?php echo $recent['category']; ?></span>
                    <?php echo date('d/m/Y', strtotime($recent['published_date'])); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <a href="#">← Trước</a>
        <span class="current">1</span>
        <a href="#">2</a>
        <a href="#">3</a>
        <a href="#">Sau →</a>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>