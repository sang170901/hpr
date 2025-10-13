<?php
// Web helper: add `status` column to suppliers table if missing
header('Content-Type: text/plain; charset=utf-8');
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    $cols = $pdo->query("PRAGMA table_info(suppliers)")->fetchAll(PDO::FETCH_ASSOC);
    $names = array_map(function($c){return $c['name'];}, $cols);
    if (in_array('status', $names)) {
        echo "Column 'status' already exists on suppliers.\n";
        exit;
    }

    // Add column
    $pdo->exec('ALTER TABLE suppliers ADD COLUMN status INTEGER DEFAULT 1');
    echo "Added column 'status' to suppliers.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
