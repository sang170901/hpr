<?php
require_once 'backend/inc/db.php';

// Lấy danh sách slider active và trong thời gian hiển thị
function getActiveSliders() {
    try {
        $pdo = getPDO();
        $today = date('Y-m-d');
        
        $sql = "SELECT * FROM sliders 
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
                        <h2 class="slide-title"><?php echo htmlspecialchars($slider['title']); ?></h2>
                        <?php if (!empty($slider['subtitle'])): ?>
                        <p class="slide-subtitle"><?php echo htmlspecialchars($slider['subtitle']); ?></p>
                        <?php endif; ?>
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
        
        <!-- Slider Dots -->
        <div class="slider-dots">
            <?php foreach ($sliders as $index => $slider): ?>
            <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                  onclick="currentSlide(<?php echo $index; ?>)" 
                  data-slide="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Slider Styles */
.main-slider {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    margin: 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.slider-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.slider-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
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
}

.slide.active {
    opacity: 1;
    z-index: 2;
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
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
    line-height: 1.2;
    background: rgba(0,0,0,0.5);
    padding: 15px 30px;
    border-radius: 10px;
    display: inline-block;
    backdrop-filter: blur(10px);
}

.slide-subtitle {
    font-size: 1.4rem;
    margin-bottom: 1rem;
    opacity: 0.95;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    background: rgba(0,0,0,0.4);
    padding: 10px 20px;
    border-radius: 8px;
    display: inline-block;
    backdrop-filter: blur(5px);
}

.slide-description {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    background: rgba(0,0,0,0.3);
    padding: 15px 25px;
    border-radius: 8px;
    backdrop-filter: blur(5px);
}

.slide-btn {
    display: inline-block;
    padding: 15px 35px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    text-decoration: none;
    border-radius: 30px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    text-shadow: none;
}

.slide-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    color: white;
    text-decoration: none;
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
    width: 55px;
    height: 55px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
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

.slider-dots {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 12px;
    z-index: 4;
}

.dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: rgba(255,255,255,0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.2);
}

.dot.active {
    background: white;
    transform: scale(1.2);
}

.dot:hover {
    background: rgba(255,255,255,0.7);
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-slider {
        height: 400px;
    }
    
    .slide-title {
        font-size: 2.5rem;
        padding: 10px 20px;
    }
    
    .slide-subtitle {
        font-size: 1.2rem;
        padding: 8px 15px;
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
    
    .slider-dots {
        bottom: 20px;
    }
}

@media (max-width: 480px) {
    .main-slider {
        height: 350px;
    }
    
    .slide-title {
        font-size: 2rem;
        padding: 8px 15px;
    }
    
    .slide-subtitle {
        font-size: 1.1rem;
        padding: 6px 12px;
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
    const dots = document.querySelectorAll('.dot');
    
    if (n >= slides.length) currentSlideIndex = 0;
    if (n < 0) currentSlideIndex = slides.length - 1;
    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    if (slides[currentSlideIndex]) {
        slides[currentSlideIndex].classList.add('active');
    }
    if (dots[currentSlideIndex]) {
        dots[currentSlideIndex].classList.add('active');
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