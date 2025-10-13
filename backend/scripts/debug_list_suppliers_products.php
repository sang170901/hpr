<?php
header('Content-Type: text/html; charset=utf-8');
echo '<pre style="white-space:pre-wrap;font-family:Consolas,monospace">';
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    echo "Suppliers:\n";
    $sup = $pdo->query('SELECT id,name,slug FROM suppliers ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($sup as $s) {
        echo "id={$s['id']} slug='{$s['slug']}' name='{$s['name']}'\n";
    }

    echo "\nProducts:\n";
    $prod = $pdo->query('SELECT id,name,slug,manufacturer,supplier_id FROM products ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($prod as $p) {
        echo "id={$p['id']} slug='{$p['slug']}' name='{$p['name']}' manufacturer='{$p['manufacturer']}' supplier_id='".($p['supplier_id']??'')."'\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
echo '</pre>';
