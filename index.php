<?php include 'inc/header-new.php'; ?>
<?php
require_once 'backend/inc/db.php';

// Lấy số liệu từ cơ sở dữ liệu
try {
    $pdo = getPDO();

    // Đếm số sản phẩm
    $stmtProducts = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
    $totalProducts = $stmtProducts->fetch(PDO::FETCH_ASSOC)['total_products'];

    // Đếm số nhà cung cấp (active)
    $stmtSuppliers = $pdo->query("SELECT COUNT(*) AS total_suppliers FROM suppliers WHERE status = 1");
    $totalSuppliers = $stmtSuppliers->fetch(PDO::FETCH_ASSOC)['total_suppliers'];

    // Đếm số danh mục sản phẩm (distinct categories)
    $stmtCategories = $pdo->query("SELECT COUNT(DISTINCT category) AS total_categories FROM products WHERE category IS NOT NULL AND category != ''");
    $totalCategories = $stmtCategories->fetch(PDO::FETCH_ASSOC)['total_categories'];

} catch (Exception $e) {
    $totalProducts = 0;
    $totalSuppliers = 0;
    $totalCategories = 0;
    error_log("Lỗi khi truy xuất số liệu: " . $e->getMessage());
}
?>

    <!-- Main Slider Section -->
    <section class="main-slider">
        <div class="slider-container">
            <div class="slider-wrapper">
                <div class="slide active">
                    <div class="slide-background" style="background-image: url('assets/images/slider-1.jpg');"></div>
                    <div class="slide-content">
                        <div class="container">
                            <h2 class="slide-title">Vật Liệu Xây Dựng Chất Lượng Cao</h2>
                            <p class="slide-subtitle">Khám phá bộ sưu tập vật liệu xây dựng đa dạng và hiện đại</p>
                            <a href="materials.php" class="slide-btn">Khám Phá Ngay</a>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide-background" style="background-image: url('assets/images/slider-2.jpg');"></div>
                    <div class="slide-content">
                        <div class="container">
                            <h2 class="slide-title">Công Nghệ Xây Dựng Tiên Tiến</h2>
                            <p class="slide-subtitle">Giải pháp công nghệ hiện đại cho ngành xây dựng Việt Nam</p>
                            <a href="technology.php" class="slide-btn">Tìm Hiểu Thêm</a>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide-background" style="background-image: url('assets/images/slider-3.jpg');"></div>
                    <div class="slide-content">
                        <div class="container">
                            <h2 class="slide-title">Thiết Bị Xây Dựng Chuyên Nghiệp</h2>
                            <p class="slide-subtitle">Máy móc và thiết bị hỗ trợ thi công hiệu quả</p>
                            <a href="equipment.php" class="slide-btn">Xem Sản Phẩm</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slider Navigation -->
            <div class="slider-nav">
                <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
            </div>
            
            <!-- Slider Dots -->
            <div class="slider-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>
        </div>
    </section>

    <style>
    .main-slider {
        width: 100%;
        height: 400px;
        position: relative;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }
    
    .slider-container {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .slider-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
    }
    
    .slide.active {
        opacity: 1;
    }
    
    .slide-background {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #ccc;
        position: relative;
    }
    
    .slide-background::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.4), rgba(0,0,0,0.2));
    }
    
    .slide-content {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        z-index: 2;
        text-align: center;
        color: white;
    }
    
    .slide-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .slide-subtitle {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .slide-btn {
        display: inline-block;
        padding: 12px 30px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    
    .slide-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
    }
    
    .slider-prev {
        left: 20px;
    }
    
    .slider-next {
        right: 20px;
    }
    
    .slider-prev, .slider-next {
        position: absolute;
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    
    .slider-prev:hover, .slider-next:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
    }
    
    .slider-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 3;
    }
    
    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.4);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .dot.active {
        background: white;
    }
    
    .dot:hover {
        background: rgba(255,255,255,0.7);
    }
    
    @media (max-width: 768px) {
        .main-slider {
            height: 300px;
        }
        
        .slide-title {
            font-size: 2rem;
        }
        
        .slide-subtitle {
            font-size: 1rem;
        }
        
        .slider-prev, .slider-next {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }
        
        .slider-prev {
            left: 10px;
        }
        
        .slider-next {
            right: 10px;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const prevBtn = document.querySelector('.slider-prev');
        const nextBtn = document.querySelector('.slider-next');
        let currentSlide = 0;
        
        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            if (n >= slides.length) currentSlide = 0;
            if (n < 0) currentSlide = slides.length - 1;
            
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }
        
        function nextSlide() {
            currentSlide++;
            showSlide(currentSlide);
        }
        
        function prevSlide() {
            currentSlide--;
            showSlide(currentSlide);
        }
        
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
        
        // Auto slide
        setInterval(nextSlide, 5000);
    });
    </script>

    <!-- Statistics Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <img src="assets/images/materials-icon.svg" alt="Sản phẩm">
                    </div>
                    <div class="stat-number">
                        <?php echo number_format($totalProducts); ?>
                    </div>
                    <div class="stat-label">Sản Phẩm Đăng Tải</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <img src="assets/images/suppliers-icon.svg" alt="Nhà cung cấp">
                    </div>
                    <div class="stat-number">
                        <?php echo number_format($totalSuppliers); ?>
                    </div>
                    <div class="stat-label">Nhà Cung Cấp</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <img src="assets/images/categories-icon.svg" alt="Danh mục">
                    </div>
                    <div class="stat-number">
                        <?php echo number_format($totalCategories); ?>
                    </div>
                    <div class="stat-label">Danh Mục Sản Phẩm</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About & Mission Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">VNMaterial & sứ mệnh</h2>
            </div>
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon">
                        <img src="assets/images/community-icon.svg" alt="Cộng đồng">
                    </div>
                    <div class="mission-number">01.</div>
                    <h3 class="mission-title">Cộng Đồng</h3>
                    <p class="mission-description">
                        Cộng đồng VNMaterial – Kết nối, học hỏi và cùng nhau xây dựng.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">
                        <img src="assets/images/commitment-icon.svg" alt="Cam kết">
                    </div>
                    <div class="mission-number">02.</div>
                    <h3 class="mission-title">Cam Kết</h3>
                    <p class="mission-description">
                        Tại VNMaterial, chúng tôi cam kết cung cấp thông tin vật tư chất lượng cao nhất cho ngành xây dựng.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">
                        <img src="assets/images/innovation-icon.svg" alt="Sáng tạo">
                    </div>
                    <div class="mission-number">03.</div>
                    <h3 class="mission-title">Sáng Tạo Và Đổi Mới</h3>
                    <p class="mission-description">
                        VNMaterial – Truyền cảm hứng đổi mới. Chúng tôi khuyến khích tư duy đổi mới và khám phá những ý tưởng và vật liệu mới.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">
                        <img src="assets/images/mission-icon.svg" alt="Sứ mệnh">
                    </div>
                    <div class="mission-number">04.</div>
                    <h3 class="mission-title">Sứ Mệnh</h3>
                    <p class="mission-description">
                        VNMaterial – Nền tảng vững chắc cho mọi dự án - cung cấp thông tin cập nhật và hữu ích về vật liệu, kỹ thuật và xu hướng xây dựng.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">
                        <img src="assets/images/vision-icon.svg" alt="Tầm nhìn">
                    </div>
                    <div class="mission-number">05.</div>
                    <h3 class="mission-title">Tầm Nhìn</h3>
                    <p class="mission-description">
                        VNMaterial – Cùng nhau xây dựng tương lai bền vững - hướng tới trở thành nguồn cung cấp kiến thức vật liệu xây dựng hàng đầu tại Việt Nam.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">
                        <img src="assets/images/sustainability-icon.svg" alt="Bền vững">
                    </div>
                    <div class="mission-number">06.</div>
                    <h3 class="mission-title">Bền Vững</h3>
                    <p class="mission-description">
                        VNMaterial ủng hộ các hoạt động thân thiện với môi trường và vật liệu bền vững.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Materials Section -->
    <section class="search-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Tìm kiếm vật tư</h2>
                <div class="search-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>
            <p class="search-subtitle">hoặc bạn đang tìm kiếm....</p>
            
            <!-- Live Search Box -->
            <div class="live-search-container">
                <form action="products.php" method="GET" class="search-form-main">
                    <div class="search-input-wrapper">
                        <input type="text" 
                               name="q" 
                               id="liveSearchInput"
                               placeholder="Nhập tên sản phẩm để tìm kiếm..." 
                               class="live-search-input"
                               autocomplete="off">
                        <button type="submit" class="search-submit-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div id="searchResults" class="search-results-dropdown" style="display: none;"></div>
                </form>
            </div>
            
            <!-- Popular Search Tags -->
            <?php
            try {
                $pdo = getPDO();
                // Lấy danh sách sản phẩm phổ biến
                $stmt = $pdo->query("SELECT DISTINCT name, category FROM products ORDER BY name LIMIT 8");
                $popularProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $popularProducts = [];
            }
            ?>
            
            <div class="search-tags">
                <?php if (!empty($popularProducts)): ?>
                    <?php foreach ($popularProducts as $product): ?>
                        <a href="products.php?q=<?php echo urlencode($product['name']); ?>" class="search-tag">
                            <?php echo strtoupper(substr($product['name'], 0, 15)) . (strlen($product['name']) > 15 ? '...' : ''); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a href="products.php?category=vật liệu" class="search-tag">VẬT LIỆU</a>
                    <a href="products.php?category=thiết bị" class="search-tag">THIẾT BỊ</a>
                    <a href="products.php?category=công nghệ" class="search-tag">CÔNG NGHỆ</a>
                    <a href="products.php?category=cảnh quan" class="search-tag">CẢNH QUAN</a>
                <?php endif; ?>
            </div>
            
            <div class="search-links">
                <a href="suppliers.php" class="search-link">
                    <span>Nhà cung cấp</span>
                    <span><?php echo number_format($totalSuppliers); ?> đơn vị cung cấp</span>
                </a>
                <a href="products.php" class="search-link">
                    <span>Danh mục sản phẩm</span>
                    <span><?php echo number_format($totalCategories); ?> danh mục - <?php echo number_format($totalProducts); ?> sản phẩm</span>
                </a>
            </div>
        </div>
    </section>
    
    <style>
    .live-search-container {
        max-width: 600px;
        margin: 20px auto;
        position: relative;
    }
    
    .search-form-main {
        position: relative;
    }
    
    .search-input-wrapper {
        display: flex;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        overflow: hidden;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .search-input-wrapper:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
    }
    
    .live-search-input {
        flex: 1;
        padding: 15px 20px;
        border: none;
        outline: none;
        font-size: 16px;
        background: transparent;
    }
    
    .search-submit-btn {
        padding: 15px 25px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        border: none;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .search-submit-btn:hover {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    }
    
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        margin-top: 5px;
    }
    
    .search-result-item {
        padding: 12px 20px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.2s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .search-result-item:hover {
        background: #f8fafc;
    }
    
    .search-result-item:last-child {
        border-bottom: none;
    }
    
    .result-name {
        font-weight: 500;
        color: #1e293b;
    }
    
    .result-category {
        font-size: 12px;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 10px;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearchInput');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`search_ajax.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displayResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 300);
        });
        
        function displayResults(results) {
            if (results.length === 0) {
                searchResults.style.display = 'none';
                return;
            }
            
            let html = '';
            results.forEach(item => {
                html += `
                    <div class="search-result-item" onclick="window.location.href='product.php?id=${item.id}'">
                        <div class="result-name">${item.name}</div>
                        <div class="result-category">${item.category || 'Khác'}</div>
                    </div>
                `;
            });
            
            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.live-search-container')) {
                searchResults.style.display = 'none';
            }
        });
    });
    </script>

    <!-- News/Blog Section -->
    <section id="news" class="news">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Bản tin VNMaterial</h2>
            </div>
            <div class="news-grid">
                <article class="news-item featured">
                    <div class="news-image">
                        <img src="assets/images/news-1.svg" alt="Gỗ trong suốt">
                    </div>
                    <div class="news-content">
                        <div class="news-category">BẢN TIN TỔNG HỢP</div>
                        <div class="news-date">04/10/2025</div>
                        <h3 class="news-title">
                            GỖ TRONG SUỐT – VẬT LIỆU TƯƠI TRONG KIẾN TRÚC XANH
                        </h3>
                        <p class="news-excerpt">
                            Khi các tòa nhà chuyển dịch sang kiến trúc net zero và chiếu sáng tự nhiên không gây lóa, 
                            lớp bao che bằng kính truyền thống bộc lộ hạn chế...
                        </p>
                    </div>
                </article>
                <article class="news-item">
                    <div class="news-image">
                        <img src="assets/images/news-2.svg" alt="Hệ thống thoát nước">
                    </div>
                    <div class="news-content">
                        <div class="news-category">BẢN TIN TỔNG HỢP</div>
                        <div class="news-date">27/09/2025</div>
                        <h3 class="news-title">
                            Thành phố không ngập lụt: Bí mật từ hệ thống Phúc Thọ Câu
                        </h3>
                        <p class="news-excerpt">
                            Ngập lụt đô thị đang là thách thức lớn của thời hiện đại, 
                            khi những cơn mưa bất thường có thể khiến cả thành phố tê liệt...
                        </p>
                    </div>
                </article>
            </div>
            <div class="news-navigation">
                <button class="nav-btn prev-btn">←Previous</button>
                <button class="nav-btn next-btn">Next→</button>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Đối tác</h2>
            </div>
            <div class="partners-grid">
                <div class="partner-item">
                    <img src="assets/images/partner-1.svg" alt="Armstrong">
                </div>
                <div class="partner-item">
                    <img src="assets/images/partner-2.svg" alt="Adchem">
                </div>
                <div class="partner-item">
                    <img src="assets/images/partner-3.svg" alt="ABC Play">
                </div>
                <div class="partner-item">
                    <img src="assets/images/partner-4.svg" alt="Acoustar">
                </div>
                <div class="partner-item">
                    <img src="assets/images/partner-5.svg" alt="AICA">
                </div>
                <div class="partner-item">
                    <img src="assets/images/partner-6.svg" alt="ALSAFLOOR">
                </div>
                <div class="partner-item">
                    <img src="assets/images/partner-7.svg" alt="ALMECO">
                </div>
            </div>
            <div class="partners-footer">
                <a href="#" class="btn btn-outline">Xem thêm Đối tác</a>
            </div>
        </div>
    </section>

<?php include 'inc/footer-new.php'; ?>