<?php 
include 'inc/header-new.php';

// Include news manager
require_once 'backend/inc/news_manager.php';

// Get current page and category
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$category = isset($_GET['category']) ? $_GET['category'] : 'tat-ca';

// Get news data
$newsData = NewsManager::getNewsByPage($page, 6);
$newsList = $newsData['data'];
$pagination = $newsData['pagination'];

// Get featured news
$featuredNews = NewsManager::getFeaturedNews();

// Get categories
$categories = NewsManager::getCategories();
?>

<style>
/* News Page Styles */
.news-hero {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #bae6fd 100%);
    padding: 6rem 0 4rem;
    text-align: center;
}

.news-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.news-hero p {
    font-size: 1.2rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

.news-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Featured Article */
.featured-article {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin: -2rem auto 4rem;
    max-width: 1000px;
    transition: transform 0.3s ease;
}

.featured-article:hover {
    transform: translateY(-5px);
}

.featured-image {
    height: 400px;
    background: linear-gradient(45deg, #3b82f6, #8b5cf6);
    position: relative;
    overflow: hidden;
}

.featured-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-content {
    padding: 2.5rem;
}

.featured-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #64748b;
}

.featured-category {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.featured-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.featured-excerpt {
    font-size: 1.1rem;
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.read-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

/* News Grid */
.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.news-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.news-image {
    height: 200px;
    background: linear-gradient(45deg, #f1f5f9, #e2e8f0);
    position: relative;
    overflow: hidden;
}

.news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.news-card:hover .news-image img {
    transform: scale(1.05);
}

.news-card-content {
    padding: 1.5rem;
}

.news-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    color: #64748b;
}

.news-category {
    background: #f1f5f9;
    color: #3b82f6;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-weight: 600;
}

.news-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.8rem;
    line-height: 1.4;
}

.news-excerpt {
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.3s ease;
}

.news-link:hover {
    color: #8b5cf6;
}

/* Categories Filter */
.categories-filter {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.category-btn {
    padding: 0.6rem 1.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 50px;
    background: white;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-btn:hover,
.category-btn.active {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-color: transparent;
    color: white;
    transform: translateY(-2px);
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin: 3rem 0;
}

.pagination a,
.pagination span {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination a {
    background: white;
    color: #64748b;
    border: 2px solid #e2e8f0;
}

.pagination a:hover {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-color: transparent;
    color: white;
    transform: scale(1.1);
}

.pagination .current {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    border: 2px solid transparent;
}

/* Responsive */
@media (max-width: 768px) {
    .news-hero h1 {
        font-size: 2rem;
    }
    
    .news-container {
        padding: 0 1rem;
    }
    
    .featured-content {
        padding: 1.5rem;
    }
    
    .featured-title {
        font-size: 1.5rem;
    }
    
    .news-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .categories-filter {
        gap: 0.5rem;
    }
    
    .category-btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .news-hero {
        padding: 4rem 0 3rem;
    }
    
    .news-hero h1 {
        font-size: 1.8rem;
    }
    
    .featured-article {
        margin: -1rem auto 3rem;
    }
    
    .featured-content {
        padding: 1.2rem;
    }
    
    .news-card-content {
        padding: 1.2rem;
    }
}
</style>

<!-- News Hero Section -->
<section class="news-hero">
    <div class="news-container">
        <h1>Tin Tức & Cập Nhật</h1>
        <p>Những thông tin mới nhất về vật liệu xây dựng, công nghệ và xu hướng ngành</p>
    </div>
</section>

<!-- Main Content -->
<div class="news-container">
    
    <!-- Categories Filter -->
    <div class="categories-filter">
        <?php foreach ($categories as $cat): ?>
            <a href="news.php?category=<?php echo $cat['slug']; ?>" 
               class="category-btn <?php echo $category === $cat['slug'] ? 'active' : ''; ?>">
                <?php echo $cat['name']; ?>
                <?php if ($cat['count'] > 0): ?>
                    <span style="font-size: 0.7rem; opacity: 0.8;">(<?php echo $cat['count']; ?>)</span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Featured Article -->
    <?php if ($featuredNews): ?>
    <article class="featured-article">
        <div class="featured-image">
            <img src="<?php echo $featuredNews['featured_image']; ?>" alt="<?php echo htmlspecialchars($featuredNews['title']); ?>" 
                 onerror="this.style.display='none'; this.parentElement.style.background='linear-gradient(45deg, #3b82f6, #8b5cf6)';">
        </div>
        <div class="featured-content">
            <div class="featured-meta">
                <span class="featured-category"><?php echo $featuredNews['category']; ?></span>
                <span><?php echo NewsManager::formatDate($featuredNews['published_date']); ?></span>
                <span><?php echo $featuredNews['reading_time']; ?> phút đọc</span>
            </div>
            <h2 class="featured-title"><?php echo htmlspecialchars($featuredNews['title']); ?></h2>
            <p class="featured-excerpt"><?php echo htmlspecialchars($featuredNews['excerpt']); ?></p>
            <a href="article-detail.php?slug=<?php echo $featuredNews['slug']; ?>" class="read-more-btn">
                Đọc Tiếp <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </article>
    <?php endif; ?>

    <!-- News Grid -->
    <div class="news-grid">
        <?php foreach ($newsList as $news): ?>
            <?php if (!$news['featured']): // Skip featured news in grid ?>
            <!-- News Card -->
            <article class="news-card">
                <div class="news-image">
                    <img src="<?php echo $news['featured_image']; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" 
                         onerror="this.style.display='none'">
                </div>
                <div class="news-card-content">
                    <div class="news-meta">
                        <span class="news-category"><?php echo $news['category']; ?></span>
                        <span><?php echo date('d/m/Y', strtotime($news['published_date'])); ?></span>
                    </div>
                    <h3 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                    <p class="news-excerpt"><?php echo htmlspecialchars($news['excerpt']); ?></p>
                    <a href="article-detail.php?slug=<?php echo $news['slug']; ?>" class="news-link">
                        Xem chi tiết <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </article>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <a href="#" aria-label="Trang trước"><i class="fas fa-chevron-left"></i></a>
        <span class="current">1</span>
        <a href="#">2</a>
        <a href="#">3</a>
        <a href="#">4</a>
        <span>...</span>
        <a href="#">10</a>
        <a href="#" aria-label="Trang sau"><i class="fas fa-chevron-right"></i></a>
    </div>

</div>

<!-- JavaScript for filtering and interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category filter functionality
    const categoryBtns = document.querySelectorAll('.category-btn');
    const newsCards = document.querySelectorAll('.news-card');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all buttons
            categoryBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const category = this.textContent.trim();
            
            // Filter news cards (placeholder functionality)
            newsCards.forEach(card => {
                if (category === 'Tất Cả') {
                    card.style.display = 'block';
                } else {
                    const cardCategory = card.querySelector('.news-category').textContent.trim();
                    if (cardCategory === category || category === 'Tất Cả') {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
    
    // Smooth animations for cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all news cards
    newsCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>

<?php include 'inc/footer-new.php'; ?>