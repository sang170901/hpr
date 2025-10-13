<?php include __DIR__ . '/inc/header.php'; ?>

    <!-- Product Slider Section -->
    <section class="product-slider">
        <div class="slider-container">
            <div class="slider-wrapper">
                <div class="slide active" data-link="product.php?slug=gach-ceramic-cao-cap">
                    <img src="assets/images/slider/slide-1.svg" alt="Gạch Ceramic Cao Cấp">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Gạch Ceramic Cao Cấp 2024</h3>
                            <p>Bộ sưu tập mới nhất với công nghệ chống trượt</p>
                            <a class="slide-cta" href="product.php?slug=gach-ceramic-cao-cap">Xem Chi Tiết →</a>
                        </div>
                    </div>
                </div>
                <div class="slide" data-link="product.php?slug=son-chong-tham-eco">
                    <img src="assets/images/slider/slide-2.svg" alt="Sơn Chống Thấm Eco">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Sơn Chống Thấm Eco-Friendly</h3>
                            <p>Thân thiện môi trường, an toàn sức khỏe</p>
                            <a class="slide-cta" href="product.php?slug=son-chong-tham-eco">Khám Phá →</a>
                        </div>
                    </div>
                </div>
                <div class="slide" data-link="product.php?slug=xi-mang-thong-minh">
                    <img src="assets/images/slider/slide-3.svg" alt="Xi Măng Thông Minh">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Xi Măng Thông Minh IoT</h3>
                            <p>Công nghệ tiên tiến, tự điều chỉnh</p>
                            <a class="slide-cta" href="product.php?slug=xi-mang-thong-minh">Tìm Hiểu →</a>
                        </div>
                    </div>
                </div>
                <div class="slide" data-link="product.php?slug=thep-xay-dung-chat-luong-cao">
                    <img src="assets/images/slider/slide-4.svg" alt="Thép Xây Dựng">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3>Thép Xây Dựng Chất Lượng Cao</h3>
                            <p>Đạt chuẩn quốc tế, độ bền vượt trội</p>
                            <a class="slide-cta" href="product.php?slug=thep-xay-dung-chat-luong-cao">Báo Giá →</a>
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
                <a href="#" class="search-tag">XI MĂNG</a>
                <a href="#" class="search-tag">VỈ THOÁT NƯỚC</a>
                <a href="#" class="search-tag">MÀNG GCL</a>
                <a href="#" class="search-tag">MÀNG HDPE</a>
                <a href="#" class="search-tag">THIẾT BỊ VỆ SINH</a>
                <a href="#" class="search-tag">THẢM</a>
                <a href="#" class="search-tag">GIẤY DÁN TƯỜNG</a>
                <a href="#" class="search-tag">ĐÁ TỰ NHIÊN</a>
            </div>
            <div class="search-links">
                <a href="#" class="search-link">
                    <span>Nhà cung cấp</span>
                    <span>Đơn vị cung cấp vật tư</span>
                </a>
                <a href="#" class="search-link">
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

<?php include __DIR__ . '/inc/footer.php'; ?>
