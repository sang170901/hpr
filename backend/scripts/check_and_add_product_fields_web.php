<?php
// Web wrapper to run the check_and_add_product_fields script from browser
// Usage: http://localhost:8080/vnmt/backend/scripts/check_and_add_product_fields_web.php?slug=your-slug
header('Content-Type: text/html; charset=utf-8');
echo '<pre style="white-space:pre-wrap;font-family:Consolas,monospace">';
try {
    $slug = $_GET['slug'] ?? 'resilient-flooring-san-nhua-vinyl-san-cao-su';
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    echo "Checking database for product with slug: $slug\n\n";

    $cols = $pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC);
    if (!$cols) {
        echo "No products table found.\n";
    } else {
        echo "Current products table columns:\n";
        foreach ($cols as $c) echo " - {$c['name']} ({$c['type']})\n";

        $existing = array_map(function($c){return $c['name'];}, $cols);
        $needed = ['sku','stock','unit','thickness','color','warranty','meta_title','meta_description','featured_image','technical_specs'];
        $toAdd = array_filter($needed, function($n) use ($existing){ return !in_array($n, $existing); });
        if (empty($toAdd)) {
            echo "\nAll common product fields already present.\n";
        } else {
            echo "\nWill add missing columns: " . implode(', ', $toAdd) . "\n";
            foreach ($toAdd as $col) {
                $type = 'TEXT';
                if (in_array($col, ['stock'])) $type = 'INTEGER DEFAULT 0';
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

        echo "\nUpdated products table columns:\n";
        $cols2 = $pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols2 as $c) echo " - {$c['name']} ({$c['type']})\n";

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
            echo "\nNo product found with that slug.\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "\nDone.\n";
echo '</pre>';
