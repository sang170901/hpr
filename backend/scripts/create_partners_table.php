<?php
/**
 * Migration: Create partners table
 * Run this script to add the partners table to your database
 */

require_once __DIR__ . '/../inc/db.php';

try {
    $pdo = getPDO();
    
    echo "Creating partners table...\n";
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS partners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        image_path VARCHAR(500),
        status TINYINT(1) DEFAULT 1,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "✓ Partners table created successfully!\n";
    
    // Check if table has any data
    $count = $pdo->query("SELECT COUNT(*) FROM partners")->fetchColumn();
    echo "Current partners count: $count\n";
    
    if ($count == 0) {
        echo "\nAdding sample partner data...\n";
        
        $samplePartners = [
            ['Armstrong', 'assets/images/partner-1.svg', 1, 1],
            ['AICA', 'assets/images/partner-2.svg', 1, 2],
            ['ABC Play', 'assets/images/partner-3.svg', 1, 3],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO partners (name, image_path, status, display_order) VALUES (?, ?, ?, ?)");
        
        foreach ($samplePartners as $partner) {
            $stmt->execute($partner);
        }
        
        echo "✓ Sample partner data added!\n";
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

