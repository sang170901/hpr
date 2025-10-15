<?php
require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();
    
    // Check table structure
    echo "=== PRODUCTS TABLE STRUCTURE ===\n";
    $result = $pdo->query('DESCRIBE products');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
    echo "\n=== SAMPLE DATA CHECK ===\n";
    $count = $pdo->query("SELECT COUNT(*) as total FROM products WHERE category = 'thiết bị'")->fetch()['total'];
    echo "Equipment count: $count\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>