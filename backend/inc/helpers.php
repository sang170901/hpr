<?php
/**
 * Helper functions for backend.
 */
function find_supplier_id(PDO $pdo, ?string $productSlug, ?string $manufacturer): ?int {
    // normalize
    $productSlug = strtolower(trim((string)$productSlug));
    $manufacturer = strtolower(trim((string)$manufacturer));

    // try exact supplier slug
    if ($productSlug) {
        $parts = preg_split('/[-_\.\/]+/', $productSlug);
        foreach ($parts as $part) {
            if (strlen($part) < 3) continue;
            $stmt = $pdo->prepare('SELECT id FROM suppliers WHERE LOWER(slug) = ? LIMIT 1');
            $stmt->execute([$part]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) return (int)$r['id'];
        }
        // try contains
        $stmt = $pdo->prepare('SELECT id FROM suppliers WHERE LOWER(slug) LIKE ? LIMIT 1');
        $stmt->execute(['%'.$productSlug.'%']);
        if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) return (int)$r['id'];
    }

    // try manufacturer matching to supplier name or slug
    if ($manufacturer) {
        $stmt = $pdo->prepare('SELECT id FROM suppliers WHERE LOWER(slug)=? OR LOWER(name) LIKE ? LIMIT 1');
        $stmt->execute([$manufacturer, '%'.$manufacturer.'%']);
        if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) return (int)$r['id'];
    }

    return null;
}
