<?php
require_once 'backend/inc/db.php';

echo "<h2>🔍 Test Slider Display</h2>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}</style>";

try {
    $pdo = getPDO();
    $today = date('Y-m-d');
    
    echo "<h3>Ngày hiện tại: $today</h3>";
    
    // Test query giống như trong slider.php
    $sql = "SELECT * FROM sliders 
            WHERE status = 1 
            AND (start_date IS NULL OR start_date <= ?) 
            AND (end_date IS NULL OR end_date >= ?) 
            ORDER BY display_order ASC, id ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$today, $today]);
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Kết quả: " . count($sliders) . " slider sẽ hiển thị</h3>";
    
    if (empty($sliders)) {
        echo "<p style='color:red;font-weight:bold;'>❌ KHÔNG CÓ SLIDER NÀO!</p>";
        echo "<p>Điều này có nghĩa là slider sẽ KHÔNG hiển thị trên trang chủ.</p>";
    } else {
        echo "<pre style='background:white;padding:20px;border-radius:8px;'>";
        print_r($sliders);
        echo "</pre>";
        
        echo "<h3>Preview Slider Code:</h3>";
        echo "<div style='background:#1e293b;color:#e2e8f0;padding:20px;border-radius:8px;'>";
        echo "<code>";
        foreach ($sliders as $index => $s) {
            echo "Slide " . ($index + 1) . ":<br>";
            echo "- Title: " . htmlspecialchars($s['title']) . "<br>";
            echo "- Image: " . htmlspecialchars($s['image']) . "<br>";
            echo "- Status: " . $s['status'] . "<br>";
            echo "- Display Order: " . $s['display_order'] . "<br><br>";
        }
        echo "</code>";
        echo "</div>";
    }
    
    echo "<hr><p><a href='index.php'>← Quay về trang chủ</a> | <a href='backend/sliders.php'>Quản lý Slider →</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Lỗi: " . $e->getMessage() . "</p>";
}
?>

