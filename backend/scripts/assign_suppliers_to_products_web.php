<?php
// Web helper: assign supplier_id to products by matching slug/manufacturer to supplier slug/name
header('Content-Type: text/plain; charset=utf-8');
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    echo "Loading suppliers...\n";
    $suppliers = $pdo->query('SELECT id, name, slug FROM suppliers')->fetchAll(PDO::FETCH_ASSOC);
    $bySlug = [];
    foreach ($suppliers as $s) { $bySlug[strtolower($s['slug'])] = $s; $bySlug[strtolower($s['name'])] = $s; }

    echo "Scanning products without supplier_id...\n";
    $stmt = $pdo->query('SELECT id, name, slug, manufacturer, supplier_id FROM products ORDER BY id ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $updated = 0; $skipped = 0;
    foreach ($products as $p) {
        if (!empty($p['supplier_id'])) { $skipped++; continue; }
        $found = null;
        // try slug match
        $pslug = strtolower(trim($p['slug'] ?? ''));
        if ($pslug) {
            foreach ($bySlug as $key => $s) {
                if ($pslug === $key || strpos($pslug, $key) !== false || strpos($key, $pslug) !== false) { $found = $s; break; }
            }
        }
        // try manufacturer name
        if (!$found && !empty($p['manufacturer'])) {
            $m = strtolower($p['manufacturer']);
            foreach ($suppliers as $s) {
                if (strpos(strtolower($s['name']), $m) !== false || strpos($m, strtolower($s['name'])) !== false) { $found = $s; break; }
            }
        }

        if ($found) {
            $pdo->prepare('UPDATE products SET supplier_id = ? WHERE id = ?')->execute([$found['id'], $p['id']]);
            echo "Assigned product id={$p['id']} ('{$p['name']}') -> supplier_id={$found['id']} ({$found['name']})\n";
            $updated++;
        } else {
            echo "No supplier match for product id={$p['id']} ('{$p['name']}'), skipped.\n";
        }
    }

    echo "\nDone. Updated={$updated}, Skipped(existing supplier)={$skipped}\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
