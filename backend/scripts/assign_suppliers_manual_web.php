<?php
// Web helper: assign supplier_id for known sample products using manual mapping
header('Content-Type: text/plain; charset=utf-8');
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    // Manual mapping: product slug => supplier slug
    $map = [
        'resilient-flooring-san-nhua-vinyl-resilient-a' => 'vinfloor-co',
        'vinyl-cao-cap-b' => 'floorpro',
        'san-cao-su-epdm-c' => 'rubbertech',
        'spc-flooring-d' => 'spc-maker',
        'san-nhua-thuong-mai-e' => 'commercialfloor',
    ];

    $updated = 0;
    foreach ($map as $pslug => $sslug) {
        $s = $pdo->prepare('SELECT id FROM suppliers WHERE slug = ? LIMIT 1');
        $s->execute([$sslug]);
        $srow = $s->fetch(PDO::FETCH_ASSOC);
        if (!$srow) { echo "Supplier slug '$sslug' not found, skipped mapping for product '$pslug'.\n"; continue; }
        $supplier_id = $srow['id'];
        $p = $pdo->prepare('SELECT id,supplier_id FROM products WHERE slug = ? LIMIT 1');
        $p->execute([$pslug]);
        $prow = $p->fetch(PDO::FETCH_ASSOC);
        if (!$prow) { echo "Product slug '$pslug' not found, skipped.\n"; continue; }
        if (!empty($prow['supplier_id'])) { echo "Product id={$prow['id']} already has supplier_id={$prow['supplier_id']}, skipped.\n"; continue; }
        $pdo->prepare('UPDATE products SET supplier_id = ? WHERE id = ?')->execute([$supplier_id, $prow['id']]);
        echo "Assigned product id={$prow['id']} (slug='$pslug') -> supplier_id={$supplier_id}\n";
        $updated++;
    }

    echo "\nDone. Updated={$updated}\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
