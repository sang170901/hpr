    </main>
    <!-- MAIN CONTENT END -->

    <!-- Modern Compact Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- About Section -->
                <div class="footer-col">
                    <h3 class="footer-logo">VNMaterial</h3>
                    <p class="footer-desc">Nền tảng thông tin vật liệu xây dựng hàng đầu Việt Nam</p>
                    <div class="footer-contact">
                        <a href="tel:+84347703123"><i class="fas fa-phone"></i> (+84) 347 703 123</a>
                        <a href="mailto:info@vnmaterial.vn"><i class="fas fa-envelope"></i> info@vnmaterial.vn</a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-col">
                    <h4 class="footer-title">Khám phá</h4>
                    <ul class="footer-links">
                        <li><a href="materials.php">Vật liệu</a></li>
                        <li><a href="equipment.php">Thiết bị</a></li>
                        <li><a href="technology.php">Công nghệ</a></li>
                        <li><a href="landscape.php">Cảnh quan</a></li>
                        <li><a href="suppliers.php">Nhà cung cấp</a></li>
                    </ul>
                </div>
                
                <!-- Company -->
                <div class="footer-col">
                    <h4 class="footer-title">Công ty</h4>
                    <ul class="footer-links">
                        <li><a href="index.php#about">Giới thiệu</a></li>
                        <li><a href="news.php">Tin tức</a></li>
                        <li><a href="suppliers.php">Đối tác</a></li>
                        <li><a href="contact.php">Liên hệ</a></li>
                    </ul>
                </div>
                
                <!-- Social -->
                <div class="footer-col">
                    <h4 class="footer-title">Kết nối</h4>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div class="footer-badge">
                        <span>🏆 Trusted by 500+</span>
                        <small>Nhà thầu & Chủ đầu tư</small>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p class="copyright">© 2025 VNMaterial.vn - All rights reserved</p>
                <div class="footer-legal">
                    <a href="#terms">Điều khoản</a>
                    <a href="#privacy">Bảo mật</a>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
    /* 🎨 Modern Compact Footer */
    .footer {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #e2e8f0;
        padding: 50px 0 0;
        margin-top: 80px;
        position: relative;
    }
    
    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, #38bdf8, transparent);
    }
    
    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1.5fr;
        gap: 40px;
        padding-bottom: 40px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }
    
    .footer-col {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .footer-logo {
        font-size: 1.5rem;
        font-weight: 800;
        color: #38bdf8;
        margin: 0 0 8px 0;
        letter-spacing: -0.02em;
    }
    
    .footer-desc {
        font-size: 0.9rem;
        color: #94a3b8;
        line-height: 1.6;
        margin: 0;
    }
    
    .footer-contact {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 8px;
    }
    
    .footer-contact a {
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    
    .footer-contact a:hover {
        color: #38bdf8;
        transform: translateX(4px);
    }
    
    .footer-contact i {
        width: 16px;
        color: #38bdf8;
    }
    
    .footer-title {
        font-size: 1rem;
        font-weight: 700;
        color: #f1f5f9;
        margin: 0 0 12px 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.875rem;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .footer-links a {
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.3s;
        display: inline-block;
    }
    
    .footer-links a:hover {
        color: #38bdf8;
        transform: translateX(4px);
    }
    
    .social-links {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(56, 189, 248, 0.1);
        border: 1px solid rgba(56, 189, 248, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #38bdf8;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 1rem;
    }
    
    .social-link:hover {
        background: #38bdf8;
        color: white;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(56, 189, 248, 0.3);
    }
    
    .footer-badge {
        margin-top: 12px;
        padding: 12px 16px;
        background: rgba(56, 189, 248, 0.05);
        border: 1px solid rgba(56, 189, 248, 0.1);
        border-radius: 10px;
        text-align: center;
    }
    
    .footer-badge span {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #38bdf8;
        margin-bottom: 4px;
    }
    
    .footer-badge small {
        font-size: 0.75rem;
        color: #94a3b8;
    }
    
    .footer-bottom {
        padding: 24px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .copyright {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }
    
    .footer-legal {
        display: flex;
        gap: 20px;
    }
    
    .footer-legal a {
        font-size: 0.875rem;
        color: #64748b;
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .footer-legal a:hover {
        color: #38bdf8;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
    }
    
    @media (max-width: 640px) {
        .footer {
            padding: 40px 0 0;
            margin-top: 60px;
        }
        
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-legal {
            justify-content: center;
        }
    }
    </style>

    <!-- JAVASCRIPT -->
    <script src="assets/js/main-new.js"></script>
</body>
</html>