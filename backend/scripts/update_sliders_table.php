<?php
/**
 * Migration: Update sliders table with new columns
 * Run this script to add subtitle, description, link_text columns if they don't exist
 */

require_once __DIR__ . '/../inc/db.php';

try {
    $pdo = getPDO();
    
    echo "Updating sliders table...\n";
    
    // Check current columns
    $columns = $pdo->query("SHOW COLUMNS FROM sliders")->fetchAll(PDO::FETCH_COLUMN);
    echo "Current columns: " . implode(', ', $columns) . "\n\n";
    
    // Add subtitle column if it doesn't exist
    if (!in_array('subtitle', $columns)) {
        $pdo->exec("ALTER TABLE sliders ADD COLUMN subtitle VARCHAR(255) AFTER title");
        echo "✓ Added 'subtitle' column\n";
    } else {
        echo "- 'subtitle' column already exists\n";
    }
    
    // Add description column if it doesn't exist
    if (!in_array('description', $columns)) {
        $pdo->exec("ALTER TABLE sliders ADD COLUMN description TEXT AFTER subtitle");
        echo "✓ Added 'description' column\n";
    } else {
        echo "- 'description' column already exists\n";
    }
    
    // Add link_text column if it doesn't exist
    if (!in_array('link_text', $columns)) {
        $pdo->exec("ALTER TABLE sliders ADD COLUMN link_text VARCHAR(100) AFTER link");
        echo "✓ Added 'link_text' column\n";
    } else {
        echo "- 'link_text' column already exists\n";
    }
    
    // Add created_at column if it doesn't exist
    if (!in_array('created_at', $columns)) {
        $pdo->exec("ALTER TABLE sliders ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "✓ Added 'created_at' column\n";
    } else {
        echo "- 'created_at' column already exists\n";
    }
    
    // Update column types if needed
    $pdo->exec("ALTER TABLE sliders MODIFY COLUMN image VARCHAR(500)");
    $pdo->exec("ALTER TABLE sliders MODIFY COLUMN link VARCHAR(500)");
    $pdo->exec("ALTER TABLE sliders MODIFY COLUMN start_date DATE");
    $pdo->exec("ALTER TABLE sliders MODIFY COLUMN end_date DATE");
    echo "✓ Updated column types\n";
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

