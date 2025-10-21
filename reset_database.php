<?php
/**
 * Script xóa và tái tạo cơ sở dữ liệu với UTF-8 encoding đúng cách
 * Chạy script này để khắc phục lỗi font tiếng Việt
 */

$config = require __DIR__ . '/backend/config.php';

try {
    // Kết nối MySQL mà không chỉ định database cụ thể
    $host = $config['db_host'] ?? 'localhost';
    $username = $config['db_user'] ?? 'root';
    $password = $config['db_password'] ?? '';
    $dbname = $config['db_name'] ?? 'vnmt_db';
    
    // Kết nối tới MySQL server (không chỉ định database)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Đang kết nối tới MySQL server...\n";
    
    // Drop database nếu tồn tại
    echo "Đang xóa database cũ nếu có...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    
    // Tạo database mới với UTF-8 encoding
    echo "Đang tạo database mới với UTF-8 encoding...\n";
    $pdo->exec("CREATE DATABASE `$dbname` 
                CHARACTER SET utf8mb4 
                COLLATE utf8mb4_unicode_ci");
    
    // Sử dụng database vừa tạo
    $pdo->exec("USE `$dbname`");
    
    // Thiết lập UTF-8 cho session hiện tại
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "Database '$dbname' đã được tạo thành công với UTF-8 encoding!\n";
    echo "Bây giờ sẽ tạo các bảng và thêm dữ liệu mẫu...\n";
    
    // Gọi hàm init_db để tạo bảng
    require_once __DIR__ . '/backend/inc/init_db.php';
    init_db($pdo, $config);
    
    echo "Các bảng đã được tạo thành công!\n";
    echo "Hoàn tất! Bạn có thể kiểm tra lại dữ liệu.\n";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
?>