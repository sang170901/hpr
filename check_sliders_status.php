<?php
require_once 'backend/inc/db.php';

echo "<h2>🔍 Kiểm tra trạng thái Slider</h2>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} table{background:white;border-collapse:collapse;width:100%;} th,td{padding:12px;text-align:left;border:1px solid #ddd;} th{background:#38bdf8;color:white;} .active{color:green;font-weight:bold;} .inactive{color:red;font-weight:bold;}</style>";

try {
    $pdo = getPDO();
    $today = date('Y-m-d');
    
    echo "<h3>📅 Ngày hiện tại: <strong>$today</strong></h3>";
    
    // Lấy TẤT CẢ slider
    echo "<h3>📋 TẤT CẢ SLIDER TRONG DATABASE</h3>";
    $allSliders = $pdo->query("SELECT * FROM sliders ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Tiêu đề</th><th>Status</th><th>Start Date</th><th>End Date</th><th>Display Order</th><th>Hiển thị?</th></tr>";
    
    foreach ($allSliders as $slider) {
        $willDisplay = true;
        $reason = [];
        
        // Kiểm tra status
        if ($slider['status'] != 1) {
            $willDisplay = false;
            $reason[] = "Status = 0 (Tắt)";
        }
        
        // Kiểm tra start_date
        if (!empty($slider['start_date']) && $slider['start_date'] > $today) {
            $willDisplay = false;
            $reason[] = "Chưa đến ngày bắt đầu";
        }
        
        // Kiểm tra end_date
        if (!empty($slider['end_date']) && $slider['end_date'] < $today) {
            $willDisplay = false;
            $reason[] = "Đã hết hạn";
        }
        
        $statusText = $slider['status'] == 1 ? '<span class="active">✓ Hoạt động</span>' : '<span class="inactive">✗ Tắt</span>';
        $displayText = $willDisplay ? '<span class="active">✓ CÓ HIỂN THỊ</span>' : '<span class="inactive">✗ KHÔNG (' . implode(', ', $reason) . ')</span>';
        
        echo "<tr>";
        echo "<td>{$slider['id']}</td>";
        echo "<td>" . htmlspecialchars($slider['title']) . "</td>";
        echo "<td>$statusText</td>";
        echo "<td>" . ($slider['start_date'] ?: '<em>Không giới hạn</em>') . "</td>";
        echo "<td>" . ($slider['end_date'] ?: '<em>Không giới hạn</em>') . "</td>";
        echo "<td>{$slider['display_order']}</td>";
        echo "<td>$displayText</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Lấy slider ACTIVE theo logic hiện tại
    echo "<h3>✅ SLIDER SẼ HIỂN THỊ TRÊN TRANG CHỦ (theo logic getActiveSliders)</h3>";
    
    $sql = "SELECT * FROM sliders 
            WHERE status = 1 
            AND (start_date IS NULL OR start_date <= ?) 
            AND (end_date IS NULL OR end_date >= ?) 
            ORDER BY display_order ASC, id ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$today, $today]);
    $activeSliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($activeSliders)) {
        echo "<p style='padding:20px;background:#fef2f2;color:#dc2626;border:2px solid #fca5a5;border-radius:8px;'><strong>⚠️ KHÔNG CÓ SLIDER NÀO SẼ HIỂN THỊ!</strong><br>Tất cả slider đã bị tắt hoặc không trong thời gian hiển thị.</p>";
    } else {
        echo "<p style='padding:20px;background:#f0fdf4;color:#059669;border:2px solid #86efac;border-radius:8px;'><strong>✓ Có " . count($activeSliders) . " slider sẽ hiển thị</strong></p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Tiêu đề</th><th>Thứ tự</th><th>Hình ảnh</th></tr>";
        foreach ($activeSliders as $slider) {
            echo "<tr>";
            echo "<td>{$slider['id']}</td>";
            echo "<td>" . htmlspecialchars($slider['title']) . "</td>";
            echo "<td>{$slider['display_order']}</td>";
            echo "<td>" . htmlspecialchars($slider['image']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr style='margin:30px 0;'>";
    echo "<p style='text-align:center;color:#666;'><a href='index.php' style='color:#38bdf8;'>← Quay về trang chủ</a> | <a href='backend/sliders.php' style='color:#38bdf8;'>Quản lý Slider →</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Lỗi: " . $e->getMessage() . "</p>";
}
?>

