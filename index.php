<?php 
// Set proper encoding for Vietnamese
header('Content-Type: text/html; charset=UTF-8');
include 'inc/header-new.php'; 
?>
<?php include 'inc/slider-demo.php'; ?>
<?php
require_once 'backend/inc/db.php';

// Đảm bảo kết nối cơ sở dữ liệu
$conn = getPDO(); // Sử dụng hàm getPDO() để khởi tạo kết nối

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

    <!-- Modern Search Section -->
    <section class="modern-search-section">
        <div class="search-hero">
        <div class="container">
                <div class="search-hero-content">
                    <div class="search-icon-main">
                        <i class="fas fa-search"></i>
                </div>
                    <h2 class="search-hero-title">Tìm kiếm sản phẩm</h2>
                    <p class="search-hero-subtitle">Khám phá hàng ngàn sản phẩm vật liệu xây dựng chất lượng cao</p>
                    
                    <!-- Modern Search Box -->
                    <div class="modern-search-container">
                        <form action="products.php" method="GET" class="modern-search-form">
                            <div class="search-box-wrapper">
                                <div class="search-icon-wrapper">
                                    <i class="fas fa-search"></i>
            </div>
                        <input type="text" 
                               name="q" 
                                       id="modernSearchInput"
                                       placeholder="Nhập tên sản phẩm, danh mục hoặc thương hiệu..." 
                                       class="modern-search-input"
                               autocomplete="off">
                                <button type="submit" class="modern-search-btn">
                                    <span>Tìm kiếm</span>
                                    <i class="fas fa-arrow-right"></i>
                        </button>
                            </div>
                            <div id="modernSearchResults" class="modern-search-results" style="display: none;"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Categories -->
        <div class="container">
            <div class="quick-categories">
                <h3 class="quick-title">
                    <i class="fas fa-layer-group"></i>
                    Danh mục phổ biến
                </h3>
                <div class="categories-grid">
                    <a href="materials.php" class="category-quick-card">
                        <div class="category-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="category-info">
                            <h4>Vật Liệu</h4>
                            <p>Gỗ, thép, bê tông, gạch...</p>
                        </div>
                        <div class="category-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="equipment.php" class="category-quick-card">
                        <div class="category-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="category-info">
                            <h4>Thiết Bị</h4>
                            <p>Máy móc, dụng cụ thi công...</p>
                        </div>
                        <div class="category-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="landscape.php" class="category-quick-card">
                        <div class="category-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="category-info">
                            <h4>Cảnh Quan</h4>
                            <p>Cây xanh, đá tự nhiên...</p>
                        </div>
                        <div class="category-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="technology.php" class="category-quick-card">
                        <div class="category-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="category-info">
                            <h4>Công Nghệ</h4>
                            <p>Giải pháp xây dựng hiện đại...</p>
                        </div>
                        <div class="category-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="search-quick-links">
                <a href="suppliers.php" class="quick-link-card">
                    <div class="quick-link-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="quick-link-content">
                        <h4>Nhà cung cấp</h4>
                        <p><?php echo number_format($totalSuppliers); ?> đơn vị uy tín</p>
                    </div>
                    <div class="quick-link-badge"><?php echo $totalSuppliers; ?></div>
                </a>

                <a href="products.php" class="quick-link-card">
                    <div class="quick-link-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="quick-link-content">
                        <h4>Sản phẩm</h4>
                        <p><?php echo number_format($totalProducts); ?> sản phẩm đa dạng</p>
                    </div>
                    <div class="quick-link-badge"><?php echo $totalProducts; ?></div>
                </a>
            </div>
        </div>
    </section>
    
    <style>
    /* Modern Search Section */
    .modern-search-section {
        padding: 0;
        background: #f0f9ff;
    }

    .search-hero {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .search-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(56,189,248,0.15)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.5;
    }

    .search-hero-content {
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .search-icon-main {
        width: 80px;
        height: 80px;
        margin: 0 auto 24px;
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(56, 189, 248, 0.3);
        animation: floatIcon 3s ease-in-out infinite;
    }

    .search-icon-main i {
        font-size: 32px;
        color: white;
    }

    @keyframes floatIcon {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .search-hero-title {
        font-size: 3rem;
        font-weight: 800;
        color: #0284c7;
        margin-bottom: 16px;
        text-shadow: 0 2px 4px rgba(2, 132, 199, 0.1);
    }

    .search-hero-subtitle {
        font-size: 1.2rem;
        color: #0ea5e9;
        margin-bottom: 40px;
        font-weight: 400;
    }

    /* Modern Search Box */
    .modern-search-container {
        max-width: 800px;
        margin: 0 auto;
        position: relative;
    }
    
    .modern-search-form {
        position: relative;
    }
    
    .search-box-wrapper {
        display: flex;
        align-items: center;
        background: white;
        border-radius: 60px;
        padding: 8px;
        box-shadow: 0 20px 60px rgba(56, 189, 248, 0.2);
        border: 3px solid white;
        transition: all 0.3s ease;
    }
    
    .search-box-wrapper:focus-within {
        box-shadow: 0 25px 80px rgba(56, 189, 248, 0.35);
        transform: translateY(-2px);
    }

    .search-icon-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #38bdf8;
        font-size: 20px;
    }

    .modern-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 18px;
        padding: 12px 20px;
        background: transparent;
        color: #1e293b;
    }

    .modern-search-input::placeholder {
        color: #94a3b8;
    }

    .modern-search-btn {
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        color: white;
        border: none;
        padding: 16px 36px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3);
    }

    .modern-search-btn:hover {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        box-shadow: 0 6px 25px rgba(56, 189, 248, 0.4);
        transform: translateX(5px);
    }

    .modern-search-btn i {
        transition: transform 0.3s ease;
    }

    .modern-search-btn:hover i {
        transform: translateX(5px);
    }

    /* Quick Categories */
    .quick-categories {
        padding: 60px 0;
    }

    .quick-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e293b;
        text-align: center;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .quick-title i {
        color: #38bdf8;
        font-size: 1.6rem;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 40px;
    }

    .category-quick-card {
        background: white;
        border-radius: 20px;
        padding: 32px 24px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid #e0f2fe;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .category-quick-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .category-quick-card:hover::before {
        transform: scaleX(1);
    }

    .category-quick-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(56, 189, 248, 0.2);
        border-color: #38bdf8;
    }

    .category-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .category-icon i {
        font-size: 32px;
        color: #38bdf8;
        transition: all 0.3s ease;
    }

    .category-quick-card:hover .category-icon {
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        transform: scale(1.1) rotate(-5deg);
    }

    .category-quick-card:hover .category-icon i {
        color: white;
    }

    .category-info h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        transition: color 0.3s ease;
    }

    .category-info p {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
    }

    .category-arrow {
        margin-top: 16px;
        color: #38bdf8;
        font-size: 18px;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
    }

    .category-quick-card:hover .category-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .category-quick-card:hover .category-info h4 {
        color: #38bdf8;
    }

    /* Quick Links */
    .search-quick-links {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        padding-bottom: 60px;
    }

    .quick-link-card {
        background: white;
        border-radius: 20px;
        padding: 32px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 24px;
        transition: all 0.3s ease;
        border: 2px solid #e0f2fe;
        position: relative;
        overflow: hidden;
    }

    .quick-link-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .quick-link-card:hover::before {
        transform: scaleY(1);
    }

    .quick-link-card:hover {
        transform: translateX(10px);
        box-shadow: 0 15px 40px rgba(56, 189, 248, 0.15);
        border-color: #38bdf8;
    }

    .quick-link-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .quick-link-icon i {
        font-size: 28px;
        color: #38bdf8;
        transition: all 0.3s ease;
    }

    .quick-link-card:hover .quick-link-icon {
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        transform: scale(1.1);
    }

    .quick-link-card:hover .quick-link-icon i {
        color: white;
    }

    .quick-link-content {
        flex: 1;
    }

    .quick-link-content h4 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        transition: color 0.3s ease;
    }
    
    .quick-link-content p {
        font-size: 1rem;
        color: #64748b;
        margin: 0;
    }

    .quick-link-card:hover .quick-link-content h4 {
        color: #38bdf8;
    }

    .quick-link-badge {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: 800;
        color: #38bdf8;
        transition: all 0.3s ease;
    }

    .quick-link-card:hover .quick-link-badge {
        background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        color: white;
        transform: scale(1.15);
    }

    /* Search Results */
    .modern-search-results {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
        border: 2px solid #e0f2fe;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .search-hero-title {
            font-size: 2rem;
        }

        .categories-grid {
            grid-template-columns: 1fr;
        }

        .search-quick-links {
            grid-template-columns: 1fr;
        }

        .modern-search-btn span {
            display: none;
        }

        .search-box-wrapper {
            padding: 6px;
        }

        .modern-search-input {
            font-size: 16px;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('modernSearchInput');
        const searchResults = document.getElementById('modernSearchResults');
        
        if (searchInput && searchResults) {
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
                
                let html = '<div style="padding: 12px 20px; background: #f0f9ff; border-bottom: 2px solid #e0f2fe;"><strong style="color: #0284c7;">Kết quả tìm kiếm</strong></div>';
                results.slice(0, 5).forEach(item => {
                    html += `
                        <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 12px;" 
                             onmouseover="this.style.background='#f8fafc'" 
                             onmouseout="this.style.background='white'"
                             onclick="window.location.href='product.php?id=${item.id}'">
                            <i class="fas fa-box" style="color: #38bdf8; font-size: 18px;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">${item.name}</div>
                                <div style="font-size: 12px; color: #64748b;">${item.category || 'Sản phẩm'}</div>
                            </div>
                            <i class="fas fa-arrow-right" style="color: #38bdf8; font-size: 14px;"></i>
                        </div>
                    `;
                });
                html += `<div style="padding: 12px 20px; text-align: center; background: #f8fafc;"><a href="products.php?q=${encodeURIComponent(searchInput.value)}" style="color: #38bdf8; text-decoration: none; font-weight: 600;">Xem tất cả kết quả <i class="fas fa-arrow-right"></i></a></div>`;
                
                searchResults.innerHTML = html;
                searchResults.style.display = 'block';
            }
            
            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.modern-search-container')) {
                    searchResults.style.display = 'none';
                }
            });

            // Hide results when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchResults.style.display = 'none';
                }
            });
        }
    });
    </script>

    <!-- News/Blog Section -->
    <?php include 'inc/news-section.php'; ?>

    <!-- Partners Section -->
    <?php
    // Lấy danh sách đối tác từ database
    try {
        $stmtPartners = $pdo->prepare("SELECT * FROM partners WHERE status = 1 ORDER BY display_order ASC, id ASC");
        $stmtPartners->execute();
        $partners = $stmtPartners->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $partners = [];
        error_log("Lỗi khi truy xuất đối tác: " . $e->getMessage());
    }
    ?>
    
    <?php if (!empty($partners)): ?>
    <section class="partners">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Đối tác</h2>
            </div>
            <div class="partners-grid">
                <?php foreach ($partners as $partner): ?>
                <div class="partner-item">
                    <img src="<?php echo htmlspecialchars($partner['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($partner['name']); ?>"
                         title="<?php echo htmlspecialchars($partner['name']); ?>">
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($partners) > 7): ?>
            <div class="partners-footer">
                <a href="suppliers.php" class="btn btn-outline">Xem thêm Đối tác</a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

<?php include 'inc/footer-new.php'; ?>