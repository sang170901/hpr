<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/auth.php';
$pdo = getPDO();

// Basic Stats
$userCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$productCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$supplierCount = $pdo->query('SELECT COUNT(*) FROM suppliers')->fetchColumn();

// Get real statistics from access_logs
$todayStart = date('Y-m-d 00:00:00');
$yesterdayStart = date('Y-m-d 00:00:00', strtotime('-1 day'));
$yesterdayEnd = date('Y-m-d 23:59:59', strtotime('-1 day'));

// Check if access_logs table exists
$tableExists = $pdo->query("SHOW TABLES LIKE 'access_logs'")->fetch();

if (!$tableExists) {
    // Create table if it doesn't exist
    $pdo->exec("CREATE TABLE access_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_url VARCHAR(500),
        user_ip VARCHAR(45),
        user_agent TEXT,
        access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        session_id VARCHAR(255),
        referrer VARCHAR(500)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

// Get actual statistics
$totalVisitsToday = $pdo->query("SELECT COUNT(*) FROM access_logs WHERE access_time >= '$todayStart'")->fetchColumn();
$totalVisitsYesterday = $pdo->query("SELECT COUNT(*) FROM access_logs WHERE access_time BETWEEN '$yesterdayStart' AND '$yesterdayEnd'")->fetchColumn();

$uniqueVisitorsToday = $pdo->query("SELECT COUNT(DISTINCT user_ip) FROM access_logs WHERE access_time >= '$todayStart'")->fetchColumn();
$uniqueVisitorsYesterday = $pdo->query("SELECT COUNT(DISTINCT user_ip) FROM access_logs WHERE access_time BETWEEN '$yesterdayStart' AND '$yesterdayEnd'")->fetchColumn();

// Calculate percentage changes
$visitChange = $totalVisitsYesterday > 0 ? round((($totalVisitsToday - $totalVisitsYesterday) / $totalVisitsYesterday) * 100) : 0;
$uniqueChange = $uniqueVisitorsYesterday > 0 ? round((($uniqueVisitorsToday - $uniqueVisitorsYesterday) / $uniqueVisitorsYesterday) * 100) : 0;

// Page statistics - real data
$pageStats = $pdo->query("
    SELECT 
        page_url,
        COUNT(*) as visits,
        COUNT(DISTINCT user_ip) as unique_visitors
    FROM access_logs 
    WHERE access_time >= '$todayStart'
    GROUP BY page_url 
    ORDER BY visits DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/inc/header.php';
?>
<div class="card">
    <h2 style="margin-top:0">Bảng điều khiển</h2>
    <div class="stats">
        <div class="stat">
            <div class="num"><?php echo $userCount; ?></div>
            <div class="muted">Người dùng</div>
        </div>
        <div class="stat">
            <div class="num"><?php echo $productCount; ?></div>
            <div class="muted">Sản phẩm</div>
        </div>
        <div class="stat">
            <div class="num"><?php echo $supplierCount; ?></div>
            <div class="muted">Nhà cung cấp</div>
        </div>
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:16px;gap:12px;flex-wrap:wrap">
        <div>
            <a class="small-btn primary" href="products.php">Thêm sản phẩm</a>
            <a class="small-btn" href="users.php">Quản lý người dùng</a>
        </div>
        <div>
            <span class="muted">Chào mừng, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Quản trị viên') ?></span>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-top:0">📊 Thống kê truy cập</h3>
    <div class="access-stats">
        <div class="stat-item">
            <div class="stat-icon">🌐</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($totalVisitsToday); ?></div>
                <div class="stat-label">Lượt truy cập hôm nay</div>
                <div class="stat-change"><?php echo ($visitChange >= 0 ? '+' : '') . $visitChange; ?>% so với hôm qua</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($uniqueVisitorsToday); ?></div>
                <div class="stat-label">Người dùng duy nhất</div>
                <div class="stat-change"><?php echo ($uniqueChange >= 0 ? '+' : '') . $uniqueChange; ?>% so với hôm qua</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">⏱️</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo date('H:i'); ?></div>
                <div class="stat-label">Cập nhật lúc</div>
                <div class="stat-change">Thời gian thực</div>
            </div>
        </div>
    </div>
    
    <h4 style="margin:24px 0 12px 0">📈 Lượt truy cập theo trang hôm nay</h4>
    <div class="page-stats">
        <?php if (!empty($pageStats)): ?>
            <?php 
            $maxVisits = max(array_column($pageStats, 'visits'));
            foreach ($pageStats as $stat): 
                $percentage = $maxVisits > 0 ? ($stat['visits'] / $maxVisits) * 100 : 0;
                $pageName = '';
                $url = $stat['page_url'];
                
                // Map URLs to friendly names
                if (strpos($url, '/vnmt/') === 0 || $url === '/') {
                    $pageName = '🏠 Trang chủ';
                } elseif (strpos($url, 'products') !== false) {
                    $pageName = '📦 Sản phẩm';
                } elseif (strpos($url, 'materials') !== false) {
                    $pageName = '🧱 Vật liệu';
                } elseif (strpos($url, 'suppliers') !== false) {
                    $pageName = '🏢 Nhà cung cấp';
                } elseif (strpos($url, 'ceramic') !== false) {
                    $pageName = '🔲 Gạch ceramic';
                } elseif (strpos($url, 'eco-paint') !== false) {
                    $pageName = '🎨 Sơn sinh thái';
                } else {
                    $pageName = '📄 ' . basename($url, '.php');
                }
            ?>
            <div class="page-stat">
                <div class="page-name"><?php echo $pageName; ?></div>
                <div class="page-views"><?php echo number_format($stat['visits']); ?> lượt</div>
                <div class="page-bar"><div class="page-bar-fill" style="width: <?php echo $percentage; ?>%"></div></div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center;color:var(--muted);padding:20px;">
                📈 Bắt đầu truy cập trang web để xem thống kê!<br>
                <small>Dữ liệu sẽ được cập nhật tự động khi có lượt truy cập mới.</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto-refresh statistics every 30 seconds
setInterval(function() {
    fetch('api_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update numbers without full page reload
                location.reload();
            }
        })
        .catch(error => console.log('Stats update error:', error));
}, 30000);
</script>
</div>

<div class="card">
    <h3 style="margin-top:0">Người dùng gần đây</h3>
    <?php
    $recent = $pdo->query('SELECT id,name,email,created_at FROM users ORDER BY id DESC LIMIT 6')->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Ngày tham gia</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recent as $r): ?>
            <tr>
                <td><?php echo $r['id'] ?></td>
                <td><?php echo htmlspecialchars($r['name']) ?></td>
                <td><?php echo htmlspecialchars($r['email']) ?></td>
                <td><?php echo $r['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>