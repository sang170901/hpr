<?php
// Slider component with hardcoded data for testing
$sliders = [
    [
        'id' => 1,
        'title' => 'Vật Liệu Xây Dựng Chất Lượng Cao',
        'description' => 'Chúng tôi cung cấp các sản phẩm vật liệu xây dựng chất lượng cao từ những thương hiệu uy tín hàng đầu.',
        'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600',
        'link' => 'materials.php',
        'link_text' => 'Khám Phá Ngay',
        'display_order' => 1,
        'status' => 1
    ],
    [
        'id' => 2,
        'title' => 'Công Nghệ Xây Dựng Tiên Tiến',
        'description' => 'Ứng dụng những công nghệ tiên tiến nhất để nâng cao hiệu quả và chất lượng trong xây dựng.',
        'image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600',
        'link' => 'technology.php',
        'link_text' => 'Tìm Hiểu Thêm',
        'display_order' => 2,
        'status' => 1
    ],
    [
        'id' => 3,
        'title' => 'Thiết Bị Xây Dựng Chuyên Nghiệp',
        'description' => 'Cung cấp đầy đủ các loại thiết bị xây dựng từ cơ bản đến chuyên nghiệp cho mọi dự án.',
        'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600',
        'link' => 'equipment.php',
        'link_text' => 'Xem Sản Phẩm',
        'display_order' => 3,
        'status' => 1
    ]
];
?>

<?php if (!empty($sliders)): ?>
<!-- Slider Section -->
<section class="main-slider" id="main-slider">
    <div class="slider-container">
        <div class="slider-wrapper">
            <?php foreach ($sliders as $index => $slider): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
                <div class="slide-background" style="background-image: url('<?php echo htmlspecialchars($slider['image']); ?>');">
                    <div class="slide-overlay"></div>
                </div>
                <div class="slide-content">
                    <div class="container">
                        <h2 class="slide-title"><?php echo htmlspecialchars($slider['title']); ?></h2>
                        <?php if (!empty($slider['description'])): ?>
                        <p class="slide-description"><?php echo htmlspecialchars($slider['description']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($slider['link']) && !empty($slider['link_text'])): ?>
                        <a href="<?php echo htmlspecialchars($slider['link']); ?>" class="slide-btn">
                            <?php echo htmlspecialchars($slider['link_text']); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($sliders) > 1): ?>
        <!-- Slider Navigation -->
        <div class="slider-nav">
            <button class="slider-prev" onclick="prevSlide()">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="slider-next" onclick="nextSlide()">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <!-- Bỏ dots navigation -->
        <?php endif; ?>
    </div>
</section>

<style>
/* Slider Styles - Modern Design */
.main-slider {
    position: relative;
    width: 100%;
    height: 500px; /* Chiều cao về 500px */
    overflow: hidden;
    margin: 0;
    border-radius: 0; /* Bỏ bo góc */
    box-shadow: 0 15px 60px rgba(0,0,0,0.15); /* Shadow giữ nguyên */
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); /* Gradient giống màu header */
}

.main-slider::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        45deg,
        rgba(255,255,255,0.1) 0%,
        transparent 50%,
        rgba(255,255,255,0.05) 100%
    );
    pointer-events: none;
    z-index: 10;
    border-radius: 20px;
}

.slider-container {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden; /* Ngăn slides chạy ra ngoài */
}

.slider-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden; /* Đảm bảo slides ở trong wrapper */
}

.slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
    z-index: 1;
    transform: translateX(0) !important; /* Force không bị dịch chuyển */
    right: auto !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

.slide.active {
    opacity: 1;
    z-index: 2;
    transform: translateX(0) !important; /* Giữ nguyên vị trí */
    left: 0 !important;
    right: auto !important;
}

.slide-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-color: #f5f5f5;
    transition: transform 0.6s ease; /* Smooth zoom effect */
    filter: brightness(1.1) contrast(1.05); /* Tăng độ sáng và tương phản */
}

.slide:hover .slide-background {
    transform: scale(1.05); /* Nhẹ nhàng zoom khi hover */
    filter: brightness(1.0) contrast(1.1); /* Hiệu ứng filter khi hover */
}

.slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.05) 100%);
    transition: all 0.4s ease;
}

.slide:hover .slide-overlay {
    background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.2) 100%);
}

.slide-content {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-30%);
    text-align: center;
    color: white;
    z-index: 3;
    opacity: 0;
    transition: all 0.5s ease;
    padding: 40px 20px;
}

.slide:hover .slide-content {
    opacity: 1;
    transform: translateY(-50%);
}

.slide-title {
    font-size: 2.8rem; /* Giảm kích thước title */
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
    line-height: 1.2;
    background: rgba(0,0,0,0.5);
    padding: 10px 20px;
    border-radius: 8px;
    display: inline-block;
    backdrop-filter: blur(10px);
}

/* Bỏ phần subtitle */

.slide-description {
    font-size: 1.2rem;
    margin-bottom: 2.5rem;
    opacity: 0.95;
    color: white;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
    max-width: 650px;
    margin-left: auto;
    margin-right: auto;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    padding: 18px 30px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.15);
    line-height: 1.6;
    font-weight: 400;
}

.slide-btn {
    display: inline-block;
    padding: 12px 30px; /* Thu nhỏ button */
    background: linear-gradient(135deg, #a2d9ff 0%, #007bff 100%); /* Gradient xanh dương nhạt và xanh lam */
    color: white;
    text-decoration: none;
    border-radius: 30px; /* Giữ bo góc nhỏ hơn */
    font-weight: 600;
    font-size: 1rem; /* Giảm kích thước font */
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    text-shadow: none;
}

.slider-nav {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    z-index: 4;
    pointer-events: none;
}

.slider-prev,
.slider-next {
    position: absolute;
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    width: 38.5px; /* Giảm kích thước nút chuyển 2 bên xuống 70% */
    height: 38.5px; /* Giảm kích thước nút chuyển 2 bên xuống 70% */
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px; /* Giảm kích thước font */
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    pointer-events: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}

.slider-prev {
    left: 30px;
}

.slider-next {
    right: 30px;
}

.slider-prev:hover,
.slider-next:hover {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    transform: scale(1.1);
}

/* Bỏ dots CSS */

/* Responsive Design */
@media (max-width: 768px) {
    .main-slider {
        height: 400px;
    }
    
    .slide-title {
        font-size: 2.5rem;
        padding: 10px 20px;
    }
    
    .slide-description {
        font-size: 1rem;
        padding: 12px 20px;
    }
    
    .slider-prev, .slider-next {
        width: 45px;
        height: 45px;
        font-size: 16px;
    }
    
    .slider-prev {
        left: 15px;
    }
    
    .slider-next {
        right: 15px;
    }
    
    /* Bỏ dots responsive CSS */
}

@media (max-width: 480px) {
    .main-slider {
        height: 350px;
    }
    
    .slide-title {
        font-size: 2rem;
        padding: 8px 15px;
    }
    
    .slide-description {
        font-size: 0.9rem;
        padding: 10px 15px;
    }
    
    .slide-btn {
        padding: 12px 25px;
        font-size: 1rem;
    }
    
    .slide-content {
        padding: 20px 15px;
    }
}
</style>

<script>
// Slider JavaScript
let currentSlideIndex = 0;
let slideInterval;

function showSlide(n) {
    const slides = document.querySelectorAll('.slide');
    
    if (n >= slides.length) currentSlideIndex = 0;
    if (n < 0) currentSlideIndex = slides.length - 1;
    
    // Remove active class from all slides
    slides.forEach(slide => {
        slide.classList.remove('active');
        // Đảm bảo slide luôn ở vị trí đúng
        slide.style.transform = 'translateX(0)';
        slide.style.left = '0';
        slide.style.right = 'auto';
    });
    
    // Add active class to current slide
    if (slides[currentSlideIndex]) {
        slides[currentSlideIndex].classList.add('active');
        // Đảm bảo slide hiện tại ở vị trí chính xác
        slides[currentSlideIndex].style.transform = 'translateX(0)';
        slides[currentSlideIndex].style.left = '0';
        slides[currentSlideIndex].style.right = 'auto';
    }
}

function nextSlide() {
    currentSlideIndex++;
    showSlide(currentSlideIndex);
}

function prevSlide() {
    currentSlideIndex--;
    showSlide(currentSlideIndex);
}

// Auto slide (chỉ khi có nhiều hơn 1 slide)
function startAutoSlide() {
    const slides = document.querySelectorAll('.slide');
    if (slides.length > 1) {
        slideInterval = setInterval(nextSlide, 6000); // 6 seconds
    }
}

function stopAutoSlide() {
    if (slideInterval) {
        clearInterval(slideInterval);
    }
}

// Initialize slider
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('main-slider');
    if (slider) {
        startAutoSlide();
        
        // Pause auto slide on hover vào toàn bộ slider
        slider.addEventListener('mouseenter', function() {
            stopAutoSlide();
        });
        
        slider.addEventListener('mouseleave', function() {
            startAutoSlide();
        });
    }
});
</script>

<?php endif; ?>