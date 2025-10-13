<?php include 'inc/header-new.php'; ?>

<section style="padding: 60px 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: #1e40af; margin-bottom: 1rem;">
                Sơn Chống Thấm Eco-Friendly
            </h1>
            <p style="font-size: 1.1rem; color: #64748b;">
                Thân thiện môi trường, an toàn sức khỏe
            </p>
        </div>
        
        <!-- Redirect notice -->
        <div style="background: #dcfce7; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 3rem;">
            <p style="color: #166534; margin-bottom: 1rem;">
                <i class="fas fa-leaf"></i>
                Trang này đang chuyển hướng đến chi tiết sản phẩm...
            </p>
            <a href="product.php?slug=son-chong-tham-eco" style="color: #16a34a; text-decoration: none; font-weight: 600;">
                → Xem chi tiết Sơn Chống Thấm Eco
            </a>
        </div>
        
        <!-- Quick preview -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
            <div style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-radius: 12px; padding: 60px; text-align: center;">
                <i class="fas fa-paint-brush" style="font-size: 80px; color: #16a34a; margin-bottom: 1rem;"></i>
                <h3 style="color: #166534; margin-bottom: 0.5rem;">Sơn Eco Paint</h3>
                <p style="color: #15803d;">100% thân thiện môi trường</p>
            </div>
            <div>
                <h3 style="color: #1e40af; margin-bottom: 1rem;">Ưu điểm vượt trội:</h3>
                <ul style="color: #64748b; line-height: 1.8;">
                    <li>🌿 Không chứa VOC độc hại</li>
                    <li>💧 Chống thấm tuyệt đối</li>
                    <li>🛡️ Kháng UV, không phai màu</li>
                    <li>⚡ Khô nhanh, dễ thi công</li>
                    <li>✨ Màng sơn mịn màng, bền đẹp</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<script>
// Auto redirect after 3 seconds
setTimeout(function() {
    window.location.href = 'product.php?slug=son-chong-tham-eco';
}, 3000);
</script>

<?php include 'inc/footer-new.php'; ?>