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
    <link rel="stylesheet" href="/vnmt/assets/css/styles-new.css">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- NEW CLEAN HEADER -->
    <header class="new-header">
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
                        <span class="nav-number">06</span>
                        <span class="nav-text">TIN TỨC</span>
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

    <!-- MAIN CONTENT START -->
    <main class="main-content">