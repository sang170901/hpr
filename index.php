<?php include 'inc/header-new.php'; ?>

    <!-- Product Slider Section -->
    <section class="product-slider">
        <div class="slider-container">
            <div class="slider-wrapper">
                <div class="slide active" data-link="ceramic-tiles.html">
                    <img src="assets/images/slider/slide-1.svg" alt="Gạch Ceramic Cao Cấp">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Gạch Ceramic Cao Cấp 2024</h3>
                            <p>Bộ sưu tập mới nhất với công nghệ chống trượt</p>
                            <span class="slide-cta">Xem Chi Tiết →</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-link="eco-paint.html">
                    <img src="assets/images/slider/slide-2.svg" alt="Sơn Chống Thấm Eco">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Sơn Chống Thấm Eco-Friendly</h3>
                            <p>Thân thiện môi trường, an toàn sức khỏe</p>
                            <span class="slide-cta">Khám Phá →</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-link="smart-cement.html">
                    <img src="assets/images/slider/slide-3.svg" alt="Xi Măng Thông Minh">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Xi Măng Thông Minh IoT</h3>
                            <p>Công nghệ tiên tiến, tự điều chỉnh</p>
                            <span class="slide-cta">Tìm Hiểu →</span>
                        </div>
                    </div>
                </div>
                <div class="slide" data-link="steel-construction.html">
                    <img src="assets/images/slider/slide-4.svg" alt="Thép Xây Dựng">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Thép Xây Dựng Chất Lượng Cao</h3>
                            <p>Đạt chuẩn quốc tế, độ bền vượt trội</p>
                            <span class="slide-cta">Báo Giá →</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Arrows -->
            <button class="slider-arrow prev-arrow" onclick="changeSlide(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="slider-arrow next-arrow" onclick="changeSlide(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Dots Navigation -->
            <div class="slider-dots">
                <span class="dot active" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
                <span class="dot" onclick="currentSlide(4)"></span>
            </div>
        </div>
    </section>

    <!-- Main Search Section -->
    <section class="main-search">
        <div class="container">
            <div class="search-box">
                <h2>Tìm kiếm vật liệu xây dựng</h2>
                <p>Nhập từ khóa để tìm kiếm vật liệu phù hợp với nhu cầu của bạn</p>
                <form method="get" action="products.php" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="q" placeholder="Nhập tên vật liệu: gạch, sơn, xi măng..." class="main-search-input">
                        <button type="submit" class="main-search-btn">
                            <i class="fas fa-search"></i>
                            Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <style>
    .main-search {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 60px 0;
        text-align: center;
    }
    
    .search-box h2 {
        color: white;
        font-size: 2.2rem;
        margin-bottom: 12px;
        font-weight: 600;
    }
    
    .search-box p {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-bottom: 30px;
    }
    
    .search-input-group {
        display: flex;
        max-width: 600px;
        margin: 0 auto;
        border-radius: 50px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .main-search-input {
        flex: 1;
        padding: 18px 25px;
        border: none;
        font-size: 1rem;
        outline: none;
        background: white;
    }
    
    .main-search-btn {
        padding: 18px 30px;
        background: #2563eb;
        color: white;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s ease;
    }
    
    .main-search-btn:hover {
        background: #1d4ed8;
    }
    
    .main-search-btn i {
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .search-input-group {
            flex-direction: column;
            border-radius: 12px;
        }
        
        .main-search-input {
            border-radius: 12px 12px 0 0;
        }
        
        .main-search-btn {
            border-radius: 0 0 12px 12px;
            justify-content: center;
        }
    }
    </style>

    <!-- Statistics Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <img src="assets/images/materials-icon.svg" alt="Vật tư">
                    </div>
                    <div class="stat-number">1,483</div>
                    <div class="stat-label">Vật Tư Đăng Tải</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <img src="assets/images/suppliers-icon.svg" alt="Nhà cung cấp">
                    </div>
                    <div class="stat-number">185</div>
                    <div class="stat-label">Nhà Cung Cấp</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <img src="assets/images/categories-icon.svg" alt="Danh mục">
                    </div>
                    <div class="stat-number">37</div>
                    <div class="stat-label">Danh Mục Vật Tư</div>
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
            <div class="search-tags">
                <a href="products.php?q=xi+mang" class="search-tag">XI MĂNG</a>
                <a href="products.php?q=vi+thoat+nuoc" class="search-tag">VỈ THOÁT NƯỚC</a>
                <a href="products.php?q=mang+gcl" class="search-tag">MÀNG GCL</a>
                <a href="products.php?q=mang+hdpe" class="search-tag">MÀNG HDPE</a>
                <a href="products.php?q=thiet+bi+ve+sinh" class="search-tag">THIẾT BỊ VỆ SINH</a>
                <a href="products.php?q=tham" class="search-tag">THẢM</a>
                <a href="products.php?q=giay+dan+tuong" class="search-tag">GIẤY DÁN TƯỜNG</a>
                <a href="products.php?q=da+tu+nhien" class="search-tag">ĐÁ TỰ NHIÊN</a>
            </div>
            <div class="search-links">
                <a href="suppliers.php" class="search-link">
                    <span>Nhà cung cấp</span>
                    <span>Đơn vị cung cấp vật tư</span>
                </a>
                <a href="products.php" class="search-link">
                    <span>Danh mục vật tư</span>
                    <span>Bạn đang tìm</span>
                </a>
            </div>
        </div>
    </section>

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
                            GỖ TRONG SUỐT – VẬT LIỆU TƯƠNG LAI THAY THẾ KÍNH TRONG KIẾN TRÚC XANH
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