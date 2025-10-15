<?php
// Run: php scripts/check_and_add_product_fields.php
$root = __DIR__ . '/..';
require $root . '/inc/db.php';
$pdo = getPDO();

$slug = $argv[1] ?? 'resilient-flooring-san-nhua-vinyl-san-cao-su';
echo "Checking database for product with slug: $slug\n\n";

// Print current schema for products
$cols = $pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC);
if (!$cols) {
    echo "No products table found.\n";
    exit(1);
}

echo "Current products table columns:\n";
foreach ($cols as $c) {
    echo " - {$c['name']} ({$c['type']})\n";
}

$existing = array_map(function($c){return $c['name'];}, $cols);

$needed = [
    'sku','stock','unit','thickness','color','warranty','meta_title','meta_description','featured_image','technical_specs'
];

$toAdd = array_filter($needed, function($n) use ($existing){ return !in_array($n, $existing); });

if (empty($toAdd)) {
    echo "\nAll common product fields already present.\n";
} else {
    echo "\nWill add missing columns: " . implode(', ', $toAdd) . "\n";
    foreach ($toAdd as $col) {
        // Choose types conservatively for SQLite
        $type = 'TEXT';
        if (in_array($col, ['stock'])) $type = 'INTEGER DEFAULT 0';
        if (in_array($col, ['price'])) $type = 'REAL';
        if (in_array($col, ['thickness'])) $type = 'REAL';
        $sql = "ALTER TABLE products ADD COLUMN $col $type";
        try {
            $pdo->exec($sql);
            echo "Added column $col\n";
        } catch (Exception $e) {
            echo "Failed to add $col: " . $e->getMessage() . "\n";
        }
    }
}

// Print updated schema
$cols2 = $pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC);
echo "\nUpdated products table columns:\n";
foreach ($cols2 as $c) echo " - {$c['name']} ({$c['type']})\n";

// Try find product by slug
$stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo "\nProduct found:\n";
    foreach ($row as $k=>$v) {
        echo "$k: ";
        if (is_null($v)) echo "NULL\n"; else echo (strlen($v)>200 ? substr($v,0,200)."...\n" : $v."\n");
    }
} else {
    echo "\nNo product found with that slug. You can pass a slug as argument to this script.\n";
}

echo "\nDone. If you want additional custom fields added, list them in the script.\n";
