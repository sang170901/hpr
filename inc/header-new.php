<?php 
// Track visits for analytics
if (!defined('TRACKING_DISABLED')) {
    require_once __DIR__ . '/../backend/inc/track_visit.php';
    trackVisit($_SERVER['REQUEST_URI'] ?? '/');
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VNMaterial</title>
    <meta name="description" content="VNMaterial - Vật liệu xây dựng Việt Nam">
    
    <!-- NEW CSS ONLY -->
    <link rel="stylesheet" href="assets/css/styles-new.css?v=<?php echo time(); ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap&subset=vietnamese" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Modern Header CSS - Embedded */
        :root {
            --header-height: 80px;
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #60a5fa;
            --accent-color: #8b5cf6;
            --accent-dark: #7c3aed;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-hover: #0f172a;
            --bg-white: #ffffff;
            --bg-header: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 30%, #bae6fd 70%, #7dd3fc 100%);
            --bg-glass: rgba(240, 249, 255, 0.95);
            --shadow-light: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-strong: 0 10px 25px rgba(0, 0, 0, 0.15);
            --border-light: rgba(0, 0, 0, 0.08);
            --gradient-primary: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            --gradient-hover: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', Gadget, sans-serif;
            padding-top: var(--header-height);
            transition: var(--transition);
        }

        /* Main Header */
        .new-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: #e0f2fe; /* Fallback color */
            background: var(--bg-header);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-light);
            z-index: 1000;
            transition: var(--transition);
            box-shadow: var(--shadow-light);
        }

        /* Header animations when scrolling */
        .new-header.scrolled {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            box-shadow: var(--shadow-strong);
            transform: translateY(-2px);
        }

        .new-header.scroll-up {
            transform: translateY(0);
        }

        .new-header.scroll-down {
            transform: translateY(-100%);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Logo Section */
        .logo-section {
            flex-shrink: 0;
            transition: var(--transition);
        }

        .logo-link {
            display: block;
            transition: var(--transition);
        }

        .logo-link:hover {
            transform: scale(1.05);
        }

        .logo {
            height: 62px; /* Increased by 20% from 52px (52 * 1.2 = 62.4px) */
            width: auto;
            transition: var(--transition);
            filter: brightness(1) contrast(1.1);
            drop-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Top Progress Bar */
        .progress-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--gradient-primary);
            width: 0%;
            transition: width 0.3s ease;
            z-index: 1001;
        }

        /* Navigation */
        .main-nav {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .nav-list {
            display: flex;
            list-style: none;
            gap: 3rem;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            position: relative;
            cursor: pointer;
        }

        .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            text-decoration: none;
            color: inherit;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        /* Hover effect with background */
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
            border-radius: 12px;
        }

        .nav-link:hover::before {
            opacity: 0.08;
        }

        .nav-link:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        /* Top bar indicator */
        .nav-link::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: var(--gradient-primary);
            transition: var(--transition);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-number {
            font-size: 0.85rem;
            font-weight: 500;
            color: #60a5fa;
            letter-spacing: 2px;
            transition: var(--transition);
        }

        .nav-text {
            font-size: 0.9rem;
            font-weight: 500;
            color: #60a5fa;
            letter-spacing: 2px;
            white-space: nowrap;
            transition: var(--transition);
        }

        .nav-link:hover .nav-number {
            color: #3b82f6;
            transform: translateY(-2px) scale(1.1);
        }

        .nav-link:hover .nav-text {
            color: #3b82f6;
            font-weight: inherit;
            transform: translateY(-2px);
            text-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }

        /* Action Buttons */
        .header-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-shrink: 0;
        }

        .action-btn {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: 12px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-hover);
            opacity: 0;
            transition: var(--transition);
            border-radius: 12px;
        }

        .action-btn:hover::before {
            opacity: 1;
        }

        .action-btn:hover {
            border-color: var(--primary-light);
            color: white;
            transform: translateY(-3px) scale(1.08);
            box-shadow: var(--shadow-medium);
        }

        .action-btn i,
        .action-btn span {
            position: relative;
            z-index: 1;
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .header-container {
                padding: 0 1.5rem;
            }
            
            .nav-list {
                gap: 2rem;
            }
            
            .nav-text {
                font-size: 0.88rem; /* Increased by 10% from 0.8rem */
            }
        }

        @media (max-width: 768px) {
            :root {
                --header-height: 70px;
            }
            
            body {
                padding-top: 70px;
            }
            
            .header-container {
                padding: 0 1rem;
            }
            
            .nav-list {
                gap: 1.5rem;
            }
            
            .nav-text {
                font-size: 0.825rem; /* Increased by 10% from 0.75rem */
            }
            
            .nav-number {
                font-size: 0.77rem; /* Increased by 10% from 0.7rem */
            }
            
            .action-btn {
                width: 40px;
                height: 40px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            :root {
                --header-height: 60px;
            }
            
            body {
                padding-top: 60px;
            }
            
            .header-container {
                padding: 0 0.75rem;
            }
            
            .nav-list {
                gap: 1rem;
            }
            
            .nav-link {
                padding: 0.5rem 0.75rem;
            }
            
            .nav-text {
                font-size: 0.77rem; /* Increased by 10% from 0.7rem */
            }
            
            .nav-number {
                display: none;
            }
        }

        /* Smooth animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .new-header {
            animation: fadeInUp 0.6s ease-out;
        }

        .nav-link:focus {
            outline: none;
        }

        /* Text stroke for nav numbers and texts */
        .nav-number,
        .nav-text {
            -webkit-text-stroke: 0.15px currentColor;
        }
    </style>
</head>
<body>
    <!-- NEW CLEAN HEADER -->
    <header class="new-header">
        <!-- Progress Bar -->
        <div class="progress-bar" id="progressBar"></div>
        
        <div class="header-container">
            <!-- Logo -->
            <div class="logo-section">
                <a href="/vnmt/" class="logo-link">
                    <img src="/vnmt/assets/images/logo.png" alt="VNMaterial" class="logo">
                </a>
            </div>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="/vnmt/materials.php" class="nav-link">
                            <span class="nav-number">01</span>
                            <span class="nav-text">VẬT LIỆU</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/vnmt/equipment.php" class="nav-link">
                            <span class="nav-number">02</span>
                            <span class="nav-text">THIẾT BỊ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/vnmt/technology.php" class="nav-link">
                            <span class="nav-number">03</span>
                            <span class="nav-text">CÔNG NGHỆ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/vnmt/landscape.php" class="nav-link">
                            <span class="nav-number">04</span>
                            <span class="nav-text">CẢNH QUAN</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/vnmt/suppliers.php" class="nav-link">
                            <span class="nav-number">05</span>
                            <span class="nav-text">NHÀ CUNG CẤP</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/vnmt/news.php" class="nav-link">
                            <span class="nav-number">06</span>
                            <span class="nav-text">TIN TỨC</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Action Buttons -->
            <div class="header-actions">
                <button class="action-btn" aria-label="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
                <button class="action-btn" aria-label="Tài khoản">
                    <i class="fas fa-user"></i>
                </button>
                <button class="action-btn">EN</button>
            </div>
        </div>
    </header>

    <script>
        // Header scroll effects and progress bar
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.new-header');
            const progressBar = document.getElementById('progressBar');
            let lastScrollTop = 0;
            let ticking = false;

            function updateScrollProgress() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercentage = (scrollTop / scrollHeight) * 100;
                
                progressBar.style.width = scrollPercentage + '%';
            }

            function updateHeaderOnScroll() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Add/remove scrolled class
                if (scrollTop > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }

                // Show/hide header based on scroll direction
                if (scrollTop > 100) {
                    if (scrollTop > lastScrollTop && !header.classList.contains('scroll-down')) {
                        // Scrolling down
                        header.classList.remove('scroll-up');
                        header.classList.add('scroll-down');
                    } else if (scrollTop < lastScrollTop && !header.classList.contains('scroll-up')) {
                        // Scrolling up
                        header.classList.remove('scroll-down');
                        header.classList.add('scroll-up');
                    }
                } else {
                    header.classList.remove('scroll-down', 'scroll-up');
                }

                lastScrollTop = scrollTop;
                updateScrollProgress();
            }

            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateHeaderOnScroll);
                    ticking = true;
                }
            }

            function handleScroll() {
                ticking = false;
                requestTick();
            }

            // Smooth scroll for navigation links
            document.querySelectorAll('.nav-link[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add scroll event listener
            window.addEventListener('scroll', handleScroll, { passive: true });
            
            // Initial call
            updateScrollProgress();
        });
    </script>

    <!-- MAIN CONTENT START -->
    <main class="main-content">