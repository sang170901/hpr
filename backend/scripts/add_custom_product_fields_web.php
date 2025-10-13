<?php
// Web utility to add custom fields to products/suppliers and optionally update a product by slug.
// Usage (browser):
// /backend/scripts/add_custom_product_fields_web.php?slug=your-slug&manufacturer=...&origin=...&material_type=...&application=...&website=...&supplier_phone=...&supplier_location=...&featured_image=...
header('Content-Type: text/html; charset=utf-8');
echo '<pre style="white-space:pre-wrap;font-family:Consolas,monospace">';
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    // Fields to add to products and suppliers
    $prodFields = [
        'manufacturer' => 'TEXT',
        'origin' => 'TEXT',
        'material_type' => 'TEXT',
        'application' => 'TEXT',
        'website' => 'TEXT',
        // featured_image maybe already exists from previous script
        'featured_image' => 'TEXT',
        // Vietnamese labels: chức năng -> product_function, phân loại -> category
        'product_function' => 'TEXT',
        'category' => 'TEXT',
    ];
    $suppFields = [
        'phone' => 'TEXT',
        'location' => 'TEXT'
    ];

    // Helper to add columns
    $existingProd = array_map(function($c){return $c['name'];}, $pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC));
    $existingSupp = array_map(function($c){return $c['name'];}, $pdo->query("PRAGMA table_info(suppliers)")->fetchAll(PDO::FETCH_ASSOC));

    echo "Checking and adding missing product columns...\n";
    foreach ($prodFields as $col=>$type) {
        if (!in_array($col, $existingProd)) {
            $sql = "ALTER TABLE products ADD COLUMN $col $type";
            try { $pdo->exec($sql); echo "Added product column: $col\n"; } catch (Exception $e){ echo "Failed to add $col: " . $e->getMessage() . "\n"; }
        } else { echo "Product column exists: $col\n"; }
    }

    echo "\nChecking and adding missing supplier columns...\n";
    foreach ($suppFields as $col=>$type) {
        if (!in_array($col, $existingSupp)) {
            $sql = "ALTER TABLE suppliers ADD COLUMN $col $type";
            try { $pdo->exec($sql); echo "Added supplier column: $col\n"; } catch (Exception $e){ echo "Failed to add $col: " . $e->getMessage() . "\n"; }
        } else { echo "Supplier column exists: $col\n"; }
    }

    // If slug provided, update product and optionally supplier
    $slug = $_GET['slug'] ?? null;
    if ($slug) {
        echo "\nUpdating product for slug: $slug\n";
        $stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) { echo "Product not found for slug: $slug\n"; }
        else {
            // Build update for product
            $up = [];
            $params = [];
            $map = ['manufacturer','origin','material_type','application','website','featured_image'];
            foreach ($map as $k) {
                if (isset($_GET[$k])) { $up[] = "$k = ?"; $params[] = $_GET[$k]; }
            }
            if (!empty($up)) {
                $sql = 'UPDATE products SET ' . implode(',', $up) . ' WHERE id = ?';
                $params[] = $product['id'];
                $pdo->prepare($sql)->execute($params);
                echo "Updated product fields.\n";
            } else { echo "No product fields provided to update.\n"; }

            // Update supplier if phone/location provided and supplier_id exists
            if (!empty($product['supplier_id'])) {
                $sParams = [];
                $sUp = [];
                if (isset($_GET['supplier_phone'])) { $sUp[] = 'phone = ?'; $sParams[] = $_GET['supplier_phone']; }
                if (isset($_GET['supplier_location'])) { $sUp[] = 'location = ?'; $sParams[] = $_GET['supplier_location']; }
                if (!empty($sUp)) {
                    $sParams[] = $product['supplier_id'];
                    $sql = 'UPDATE suppliers SET ' . implode(',', $sUp) . ' WHERE id = ?';
                    $pdo->prepare($sql)->execute($sParams);
                    echo "Updated supplier (id={$product['supplier_id']}) fields: " . implode(',', $sUp) . "\n";
                }
            } else {
                echo "Product has no supplier_id to update supplier info.\n";
            }
        }
    } else {
        echo "\nNo slug provided; only added columns. To update data, pass ?slug=...&manufacturer=... etc.\n";
    }

    echo "\nDone.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo '</pre>';
