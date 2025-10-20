<?php
// Database connection for frontend (supplier pages)
function getFrontendPDO() {
    $host = 'localhost';
    $dbname = 'vnmt_db';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (Exception $e) {
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}
?>