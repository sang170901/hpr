<?php
require_once 'backend/inc/db.php';
require_once __DIR__ . '/../lang/db_translate_helper.php';

// Lấy danh sách slider active và trong thời gian hiển thị
function getActiveSliders() {
    try {
        $pdo = getPDO();
        $today = date('Y-m-d');
        
        $sql = "SELECT *, title_en, subtitle_en, description_en, link_text_en FROM sliders 
                WHERE status = 1 
                AND (start_date IS NULL OR start_date <= ?) 
                AND (end_date IS NULL OR end_date >= ?) 
                ORDER BY display_order ASC, id ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$today, $today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Lỗi khi lấy slider: " . $e->getMessage());
        return [];
    }
}

$sliders = getActiveSliders();
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
                        <h2 class="slide-title"><?php echo htmlspecialchars(getTranslated($slider, 'title')); ?></h2>
                        <?php if (!empty(getTranslated($slider, 'subtitle'))): ?>
                        <p class="slide-subtitle"><?php echo htmlspecialchars(getTranslated($slider, 'subtitle')); ?></p>
                        <?php endif; ?>
                        <?php if (!empty(getTranslated($slider, 'description'))): ?>
                        <p class="slide-description"><?php echo htmlspecialchars(getTranslated($slider, 'description')); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($slider['link']) && !empty(getTranslated($slider, 'link_text'))): ?>
                        <a href="<?php echo htmlspecialchars($slider['link']); ?>" class="slide-btn">
                            <?php echo htmlspecialchars(getTranslated($slider, 'link_text')); ?>
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
        <?php endif; ?>
    </div>
</section>

<style>
/* 🎨 Ultra Modern Slider with Glass Morphism & Animations */

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(60px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.main-slider {
    position: relative;
    width: 100%;
    height: 700px;
    overflow: hidden;
    margin: 0 !important;
    padding: 0 !important;
    background: transparent; /* Loại bỏ background tối */
    left: 0;
    right: 0;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}

.slider-container {
    position: relative;
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    border: none !important;
    outline: none !important;
}

.slider-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
}

.slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    visibility: hidden;
    transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1), visibility 1s;
    z-index: 1;
}

.slide.active {
    opacity: 1;
    visibility: visible;
    z-index: 2;
}

.slide-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover; /* Lấp đầy khung, ít crop nhất */
    background-position: center top; /* Ưu tiên hiển thị phần trên, crop phần dưới */
    background-repeat: no-repeat;
    background-color: transparent; /* Loại bỏ background tối */
    transform: scale(1);
    transition: transform 10s ease-out;
    border: none !important;
}

.slide.active .slide-background {
    transform: scale(1); /* Không phóng to */
}

.slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.1); /* Giảm overlay từ 0.3 xuống 0.1 - sáng hơn */
}

.slide-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    z-index: 3;
    width: 100%;
    max-width: 1200px;
    padding: 40px 20px;
    opacity: 0;
    transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-content .container {
    padding: 0 !important;
    margin: 0 auto;
    max-width: 100%;
}

/* Override body container padding for slider */
body > .main-slider {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

section.main-slider {
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* Chỉ hiển thị content khi hover vào slide */
.slide:hover .slide-content {
    opacity: 1;
}

.slide:not(.active) .slide-content > * {
    opacity: 0;
}

.slide.active .slide-title {
    animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.2s both;
}

.slide.active .slide-subtitle {
    animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.4s both;
}

.slide.active .slide-description {
    animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.6s both;
}

.slide.active .slide-btn {
    animation: fadeInScale 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.8s both;
}

.slide-title {
    font-size: 4.5rem;
    font-weight: 900;
    margin-bottom: 1.5rem;
    line-height: 1.1;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    position: relative;
}

.slide-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, transparent, #38bdf8, transparent);
    border-radius: 2px;
}

.slide-subtitle {
    font-size: 1.8rem;
    font-weight: 400;
    margin-bottom: 1.5rem;
    color: #e0f2fe;
    letter-spacing: 0.02em;
    line-height: 1.6;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 15px 35px;
    border-radius: 50px;
    display: inline-block;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.slide-description {
    font-size: 1.3rem;
    margin: 1.5rem auto;
    max-width: 800px;
    color: #f0f9ff;
    line-height: 1.8;
    font-weight: 300;
    letter-spacing: 0.01em;
    opacity: 0.95;
}

.slide-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 18px 45px;
    margin-top: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.15rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
    border: 2px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.slide-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.slide-btn:hover::before {
    left: 100%;
}

.slide-btn:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.6);
    color: white;
    text-decoration: none;
    border-color: rgba(255, 255, 255, 0.4);
}

.slide-btn::after {
    content: '→';
    margin-left: 8px;
    transition: transform 0.3s;
}

.slide-btn:hover::after {
    transform: translateX(5px);
}

/* Navigation Buttons */
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
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    width: 65px;
    height: 65px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 22px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.slider-prev {
    left: 40px;
}

.slider-next {
    right: 40px;
}

.slider-prev:hover,
.slider-next:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: scale(1.15);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.slider-prev:active,
.slider-next:active {
    transform: scale(1.05);
}

/* Dot Navigation - REMOVED */

/* Responsive Design */
@media (max-width: 1024px) {
    .main-slider {
        height: 600px;
    }
    
    .slide-title {
        font-size: 3.5rem;
    }
    
    .slide-subtitle {
        font-size: 1.5rem;
        padding: 12px 28px;
    }
    
    .slide-description {
        font-size: 1.2rem;
    }
}

@media (max-width: 768px) {
    .main-slider {
        height: 400px; /* Quay lại chiều cao ban đầu */
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    
    .slide-content {
        width: 100%;
        padding: 30px 15px;
    }
    
    .slide-content .container {
        padding: 0;
    }
    
    .slide-title {
        font-size: 2.4rem;
    }
    
    .slide-title::after {
        width: 60px;
        height: 3px;
    }
    
    .slide-subtitle {
        font-size: 1.2rem;
        padding: 8px 20px;
    }
    
    .slide-description {
        font-size: 1rem;
    }
    
    .slide-btn {
        padding: 14px 30px;
        font-size: 0.95rem;
    }
    
    .slider-prev,
    .slider-next {
        width: 50px;
        height: 50px;
        font-size: 16px;
    }
    
    .slider-prev {
        left: 15px;
    }
    
    .slider-next {
        right: 15px;
    }
    
}

@media (max-width: 640px) {
    .main-slider {
        height: 350px; /* Quay lại chiều cao ban đầu */
        margin: 0;
        padding: 0;
    }
    
    .slide-content {
        width: 100%;
        padding: 25px 12px;
    }
    
    .slide-content .container {
        padding: 0;
    }
    
    .slide-title {
        font-size: 2.2rem;
        margin-bottom: 0.9rem;
    }
    
    .slide-subtitle {
        font-size: 1.1rem;
        padding: 7px 18px;
    }
    
    .slide-description {
        font-size: 0.95rem;
    }
    
    .slide-btn {
        padding: 13px 28px;
        font-size: 0.9rem;
    }
    
    .slider-prev,
    .slider-next {
        width: 46px;
        height: 46px;
        font-size: 15px;
    }
    
    .slider-prev {
        left: 12px;
    }
    
    .slider-next {
        right: 12px;
    }
}

@media (max-width: 480px) {
    .main-slider {
        height: 300px; /* Quay lại chiều cao ban đầu */
        margin: 0;
        padding: 0;
    }
    
    .slide-content {
        width: 100%;
        padding: 20px 10px;
    }
    
    .slide-content .container {
        padding: 0;
    }
    
    .slide-title {
        font-size: 1.8rem;
        margin-bottom: 0.8rem;
    }
    
    .slide-title::after {
        width: 50px;
        height: 2px;
        bottom: -8px;
    }
    
    .slide-subtitle {
        font-size: 1rem;
        padding: 6px 16px;
        margin-bottom: 0.8rem;
    }
    
    .slide-description {
        font-size: 0.88rem;
        margin: 0.8rem auto;
    }
    
    .slide-btn {
        padding: 12px 26px;
        font-size: 0.85rem;
        margin-top: 12px;
    }
    
    .slide-btn::after {
        content: '';
    }
    
    .slider-prev,
    .slider-next {
        width: 42px;
        height: 42px;
        font-size: 14px;
    }
    
    .slider-prev {
        left: 10px;
    }
    
    .slider-next {
        right: 10px;
    }
}

@media (max-width: 375px) {
    .main-slider {
        height: 280px; /* Quay lại chiều cao ban đầu */
        margin: 0;
        padding: 0;
    }
    
    .slide-content {
        width: 100%;
        padding: 18px 8px;
    }
    
    .slide-content .container {
        padding: 0;
    }
    
    .slide-title {
        font-size: 1.65rem;
    }
    
    .slide-subtitle {
        font-size: 0.95rem;
        padding: 6px 14px;
    }
    
    .slide-description {
        font-size: 0.82rem;
    }
    
    .slide-btn {
        padding: 11px 24px;
        font-size: 0.8rem;
    }
    
    .slider-prev,
    .slider-next {
        width: 38px;
        height: 38px;
        font-size: 13px;
    }
}

@media (max-width: 320px) {
    .main-slider {
        height: 260px; /* Quay lại chiều cao ban đầu */
        margin: 0;
        padding: 0;
    }
    
    .slide-content {
        width: 100%;
        padding: 15px 8px;
    }
    
    .slide-content .container {
        padding: 0;
    }
    
    .slide-title {
        font-size: 1.5rem;
        margin-bottom: 0.7rem;
    }
    
    .slide-subtitle {
        font-size: 0.9rem;
        padding: 5px 12px;
    }
    
    .slide-description {
        font-size: 0.78rem;
        margin: 0.6rem auto;
    }
    
    .slide-btn {
        padding: 10px 22px;
        font-size: 0.75rem;
    }
    
    .slider-prev,
    .slider-next {
        width: 36px;
        height: 36px;
        font-size: 12px;
    }
    
    .slider-prev {
        left: 8px;
    }
    
    .slider-next {
        right: 8px;
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
    
    slides.forEach(slide => slide.classList.remove('active'));
    
    if (slides[currentSlideIndex]) {
        slides[currentSlideIndex].classList.add('active');
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

function currentSlide(n) {
    currentSlideIndex = n;
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