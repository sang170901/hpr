<?php
require_once 'backend/inc/db.php';

try {
    echo "Initializing database...\n";
    
    $pdo = getPDO();
    echo "✅ Connected to database!\n";
    
    // Load config
    $config = require 'backend/config.php';
    
    // Initialize database
    require_once 'backend/inc/init_db.php';
    init_db($pdo, $config);
    
    echo "✅ Database initialized successfully!\n";
    
    // Check tables
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\n📋 Created tables:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>