<?php include 'inc/header-new.php'; ?>

<section style="padding: 60px 0;">
    <div class="container">
        <!-- Breadcrumb -->
        <nav style="margin-bottom: 2rem;">
            <a href="/" style="color: #64748b; text-decoration: none;">Trang chủ</a>
            <span style="color: #64748b; margin: 0 8px;">/</span>
            <a href="/materials.php" style="color: #64748b; text-decoration: none;">Vật liệu</a>
            <span style="color: #64748b; margin: 0 8px;">/</span>
            <span style="color: #1e40af; font-weight: 600;">Gạch Ceramic</span>
        </nav>
        
        <!-- Product Info -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
            <!-- Product Image -->
            <div>
                <div style="background: #f1f5f9; border-radius: 12px; padding: 40px; text-align: center; margin-bottom: 1rem;">
                    <i class="fas fa-image" style="font-size: 80px; color: #cbd5e1;"></i>
                    <p style="color: #64748b; margin-top: 1rem;">Hình ảnh sản phẩm</p>
                </div>
                <!-- Thumbnail gallery -->
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <div style="width: 60px; height: 60px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-image" style="color: #94a3b8; font-size: 20px;"></i>
                    </div>
                    <div style="width: 60px; height: 60px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-image" style="color: #94a3b8; font-size: 20px;"></i>
                    </div>
                    <div style="width: 60px; height: 60px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-image" style="color: #94a3b8; font-size: 20px;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Product Details -->
            <div>
                <h1 style="font-size: 2.5rem; font-weight: 700; color: #1e40af; margin-bottom: 1rem;">
                    Gạch Ceramic Cao Cấp 60x60
                </h1>
                <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 2rem;">
                    Gạch ceramic chống trượt, kháng khuẩn với công nghệ nano tiên tiến. 
                    Phù hợp cho phòng khách, phòng ngủ và các khu vực thương mại.
                </p>
                
                <!-- Price -->
                <div style="margin-bottom: 2rem;">
                    <span style="font-size: 2rem; font-weight: 700; color: #dc2626;">250.000đ</span>
                    <span style="color: #64748b;">/m²</span>
                    <span style="font-size: 1.1rem; color: #64748b; text-decoration: line-through; margin-left: 1rem;">320.000đ</span>
                </div>
                
                <!-- Specifications -->
                <div style="background: #f8fafc; padding: 24px; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #1e40af; margin-bottom: 1rem;">Thông số kỹ thuật</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div><strong>Kích thước:</strong> 60x60cm</div>
                        <div><strong>Độ dày:</strong> 9.5mm</div>
                        <div><strong>Chất liệu:</strong> Ceramic</div>
                        <div><strong>Xuất xứ:</strong> Việt Nam</div>
                        <div><strong>Bảo hành:</strong> 10 năm</div>
                        <div><strong>Kháng nước:</strong> Cao</div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div style="display: flex; gap: 15px;">
                    <button style="background: #2563eb; color: white; padding: 16px 32px; border: none; border-radius: 8px; font-weight: 600; font-size: 1.1rem; cursor: pointer; flex: 1;">
                        Liên hệ báo giá
                    </button>
                    <button style="background: transparent; color: #2563eb; padding: 16px 24px; border: 2px solid #2563eb; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <section style="margin-top: 80px;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #1e40af; margin-bottom: 2rem; text-align: center;">
                Sản phẩm liên quan
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                <!-- Related product cards would go here -->
                <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
                    <div style="background: #f1f5f9; height: 200px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image" style="font-size: 40px; color: #cbd5e1;"></i>
                    </div>
                    <div style="padding: 20px;">
                        <h3 style="font-weight: 600; color: #1e40af; margin-bottom: 0.5rem;">Gạch Granite 80x80</h3>
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1rem;">Gạch granite cao cấp chống trượt</p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; color: #dc2626;">180.000đ/m²</span>
                            <button style="background: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer;">Xem</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

<?php include 'inc/footer-new.php'; ?>