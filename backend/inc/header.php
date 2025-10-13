<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>VNMaterial Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script>window.appConfig = {};</script>
    <style>img.logo-sm{height:28px;vertical-align:middle}</style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-logo">🏗️</div>
      <div class="brand-text">VNMaterial</div>
    </div>
    <nav>
      <a href="index.php" class="nav-item">
        <span class="nav-icon">📊</span>
        <span class="nav-text">Bảng điều khiển</span>
      </a>
      <a href="users.php" class="nav-item">
        <span class="nav-icon">👥</span>
        <span class="nav-text">Người dùng</span>
      </a>
      <a href="products.php" class="nav-item">
        <span class="nav-icon">📦</span>
        <span class="nav-text">Sản phẩm</span>
      </a>
      <a href="suppliers.php" class="nav-item">
        <span class="nav-icon">🏢</span>
        <span class="nav-text">Nhà cung cấp</span>
      </a>
      <a href="vouchers.php" class="nav-item">
        <span class="nav-icon">🎫</span>
        <span class="nav-text">Mã giảm giá</span>
      </a>
      <a href="sliders.php" class="nav-item">
        <span class="nav-icon">🖼️</span>
        <span class="nav-text">Quản lý Slider</span>
      </a>
      <a href="activity_logs.php" class="nav-item">
        <span class="nav-icon">📋</span>
        <span class="nav-text">Nhật ký hoạt động</span>
      </a>
      <a href="scheduled_publishing.php" class="nav-item">
        <span class="nav-icon">⏰</span>
        <span class="nav-text">Lịch xuất bản</span>
      </a>
      <a href="logout.php" class="nav-item logout">
        <span class="nav-icon">🚪</span>
        <span class="nav-text">Đăng xuất</span>
      </a>
    </nav>
  </aside>
  <div class="main">
    <div class="topbar">
      <div style="display:flex;gap:12px;align-items:center">
        <button class="small-btn ghost" id="btn-toggle-sidebar">☰</button>
        <div style="font-weight:700">Quản trị VNMaterial</div>
      </div>
      <div style="display:flex;gap:12px;align-items:center">
        <div style="color:var(--muted-2)">Xin chào, Quản trị viên</div>
      </div>
    </div>
    <div class="container">
