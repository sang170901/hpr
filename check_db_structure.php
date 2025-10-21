<?php
require 'backend/inc/db.php';
$pdo = getPDO();

echo "=== BẢNG SUPPLIERS ===\n";
$result = $pdo->query('DESCRIBE suppliers');
while ($row = $result->fetch()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
}

echo "\n=== DỮ LIỆU MẪU SUPPLIERS ===\n";
$suppliers = $pdo->query('SELECT id, name, category_id, services, specialties FROM suppliers LIMIT 10')->fetchAll();
foreach ($suppliers as $s) {
    echo 'ID: ' . $s['id'] . ' - ' . $s['name'] . ' - Category: ' . $s['category_id'] . ' - Services: ' . $s['services'] . ' - Specialties: ' . $s['specialties'] . "\n";
}

echo "\n=== BẢNG PRODUCTS ===\n";
$result = $pdo->query('DESCRIBE products');
while ($row = $result->fetch()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
}

echo "\n=== DỮ LIỆU MẪU PRODUCTS (CATEGORY) ===\n";
$categories = $pdo->query('SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ""')->fetchAll();
foreach ($categories as $c) {
    echo 'Category: ' . $c['category'] . "\n";
}

echo "\n=== DỮ LIỆU MẪU PRODUCTS (CLASSIFICATION) ===\n";
$classifications = $pdo->query('SELECT DISTINCT classification FROM products WHERE classification IS NOT NULL AND classification != ""')->fetchAll();
foreach ($classifications as $c) {
    echo 'Classification: ' . $c['classification'] . "\n";
}

echo "\n=== DỮ LIỆU MẪU PRODUCTS (MATERIAL_TYPE) ===\n";
$material_types = $pdo->query('SELECT DISTINCT material_type FROM products WHERE material_type IS NOT NULL AND material_type != ""')->fetchAll();
foreach ($material_types as $m) {
    echo 'Material Type: ' . $m['material_type'] . "\n";
}
?>