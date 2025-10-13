<?php
try {
    require_once 'backend/inc/db.php';
    $pdo = getPDO();
    echo "✅ Kết nối database thành công!\n";
    echo "Database path: " . realpath(__DIR__ . '/backend/database.sqlite') . "\n";
    
    // Kiểm tra các bảng
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Các bảng có sẵn: " . implode(', ', $tables) . "\n";
    
    // Kiểm tra số lượng records
    foreach($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        echo "  - {$table}: {$count} records\n";
    }
    
    // Kiểm tra admin user
    $admin = $pdo->query("SELECT email, role FROM users WHERE role = 'admin' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if($admin) {
        echo "👤 Admin user: " . $admin['email'] . "\n";
    }
    
} catch(Exception $e) {
    echo "❌ Lỗi kết nối: " . $e->getMessage() . "\n";
}
?>