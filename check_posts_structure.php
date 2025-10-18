<?php
require_once 'backend/inc/db.php';
try {
    $pdo = getPDO();
    $stmt = $pdo->query('PRAGMA table_info(posts)');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in posts table:\n";
    foreach ($columns as $column) {
        echo "- " . $column['name'] . " (" . $column['type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>