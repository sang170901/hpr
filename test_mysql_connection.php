<?php
// Test MySQL connection without XAMPP dependency
$host = 'localhost';
$username = 'root';
$password = '';

try {
    echo "Checking MySQL connection methods...\n\n";
    
    // Method 1: Try direct connection
    echo "1. Attempting direct MySQL connection...\n";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    echo "✅ Direct connection successful!\n\n";
    
    // Method 2: Create database
    echo "2. Creating database...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS vnmt_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'vnmt_db' created/verified\n\n";
    
    // Method 3: Connect to specific database
    echo "3. Connecting to vnmt_db...\n";
    $pdo = new PDO("mysql:host=$host;dbname=vnmt_db", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to vnmt_db successfully!\n\n";
    
    // Method 4: Create simple test table
    echo "4. Testing table operations...\n";
    $pdo->exec("DROP TABLE IF EXISTS test_connection");
    $pdo->exec("CREATE TABLE test_connection (
        id INT AUTO_INCREMENT PRIMARY KEY,
        message VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("INSERT INTO test_connection (message) VALUES ('Connection test successful')");
    
    $stmt = $pdo->query("SELECT * FROM test_connection");
    $result = $stmt->fetch();
    
    echo "✅ Table operations successful!\n";
    echo "Test data: " . $result['message'] . "\n\n";
    
    // Cleanup
    $pdo->exec("DROP TABLE test_connection");
    echo "✅ All MySQL tests passed!\n";
    echo "🎯 Ready to run supplier database setup!\n";
    
} catch (Exception $e) {
    echo "❌ MySQL Error: " . $e->getMessage() . "\n";
    echo "\n📝 Solutions to try:\n";
    echo "1. Open XAMPP Control Panel\n";
    echo "2. Click 'Start' next to MySQL\n";
    echo "3. Or run this command as Administrator: net start mysql\n";
    echo "4. Check if port 3306 is available\n";
}
?>