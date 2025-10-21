<?php
require 'backend/inc/db.php';
$pdo = getPDO();

echo "=== KIỂM TRA DỮ LIỆU SAU MIGRATE ===\n\n";

echo "Sản phẩm có category_id:\n";
$result = $pdo->query("SELECT p.category_id, pc.name, COUNT(*) as count FROM products p LEFT JOIN product_categories pc ON p.category_id = pc.id WHERE p.category_id IS NOT NULL GROUP BY p.category_id");
while ($row = $result->fetch()) {
    echo "  - {$row['name']}: {$row['count']} sản phẩm\n";
}

echo "\nNhà cung cấp có category_id:\n";
$result = $pdo->query("SELECT s.category_id, sc.name, COUNT(*) as count FROM suppliers s LEFT JOIN supplier_categories sc ON s.category_id = sc.id WHERE s.category_id IS NOT NULL GROUP BY s.category_id");
while ($row = $result->fetch()) {
    echo "  - {$row['name']}: {$row['count']} nhà cung cấp\n";
}

echo "\n✅ Dữ liệu đã sẵn sàng!\n";
?>