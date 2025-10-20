<?php
$config = require __DIR__ . '/../config.php';
require_once __DIR__ . '/init_db.php';

if (!function_exists('getPDO')) {
    function getPDO(){
        global $config;
        
        // Use MySQL instead of SQLite
        $host = $config['db_host'] ?? 'localhost';
        $dbname = $config['db_name'] ?? 'vnmt_db';
        $username = $config['db_user'] ?? 'root';
        $password = $config['db_password'] ?? '';
        
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Check if users table exists, if not initialize DB schema
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            if (!$stmt->fetch()) {
                init_db($pdo, $config);
            }
            
            return $pdo;
        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
}
