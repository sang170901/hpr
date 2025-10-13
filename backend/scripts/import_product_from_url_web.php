<?php
// Web helper: import a product from an external product page URL (heuristic)
// Usage:
//  /backend/scripts/import_product_from_url_web.php?url=https://example.com/product/123
//  Add &insert=1 to insert into DB. Optional &supplier_slug=slug to set supplier.
header('Content-Type: text/html; charset=utf-8');
echo '<pre style="white-space:pre-wrap;font-family:Consolas,monospace">';
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    $url = $_GET['url'] ?? null;
    if (!$url) { echo "Please provide ?url=...\n"; exit; }

    echo "Fetching: $url\n\n";

    // fetch
    $ctx = stream_context_create(['http'=>['timeout'=>10],'https'=>['timeout'=>10]]);
    $html = @file_get_contents($url, false, $ctx);
    if ($html === false) { echo "Failed to fetch URL (timeout or blocked).\n"; exit; }

    // parse basic meta
    $title = null; $desc = null; $image = null;
    if (preg_match("#<meta\\s+property=[\"']og:title[\"']\\s+content=[\"']([^\"']+)[\"']#i", $html, $m)) $title = html_entity_decode($m[1]);
    if (!$title && preg_match('#<title[^>]*>(.*?)</title>#is', $html, $m)) $title = trim(strip_tags($m[1]));
    if (preg_match("#<meta\\s+name=[\"']description[\"']\\s+content=[\"']([^\"']+)[\"']#i", $html, $m)) $desc = html_entity_decode($m[1]);
    if (preg_match("#<meta\\s+property=[\"']og:description[\"']\\s+content=[\"']([^\"']+)[\"']#i", $html, $m)) $desc = html_entity_decode($m[1]);
    if (preg_match("#<meta\\s+property=[\"']og:image[\"']\\s+content=[\"']([^\"']+)[\"']#i", $html, $m)) $image = $m[1];

    // price heuristics: look for common price patterns like 1,234,000 or 1234.00 or 1234
    $price = null;
    if (preg_match('#([0-9]{1,3}(?:[.,][0-9]{3})+(?:[.,][0-9]{2})?)\s*(?:₫|VND|đ|đồng)?#iu', $html, $m)) {
        $raw = $m[1];
        // normalize: remove dots/commas
        $num = preg_replace('/[^0-9]/', '', $raw);
        if (is_numeric($num)) $price = (float)$num;
    } elseif (preg_match('#([0-9]{3,9})(?:\.[0-9]{2})?\s*(?:USD|\$)#i', $html, $m)) {
        $price = (float)preg_replace('/[^0-9]/','',$m[1]);
    }

    echo "Detected:\n";
    echo "Title: " . ($title ?: '(none)') . "\n";
    echo "Description: " . ($desc ? substr($desc,0,200) : '(none)') . "\n";
    echo "Image: " . ($image ?: '(none)') . "\n";
    echo "Price: " . ($price !== null ? number_format($price,0,',','.') : '(none)') . "\n";

    if (isset($_GET['insert']) && $_GET['insert'] == '1') {
        // build product
        $slug = preg_replace('/[^a-z0-9\-]+/','-',strtolower(trim($title ?: 'imported-product')));
        $slug = trim($slug,'-');
        // avoid duplicate slug
        $base = $slug; $i = 1;
        $checkStmt = $pdo->prepare('SELECT id FROM products WHERE slug = ? LIMIT 1');
        while (true) {
            $checkStmt->execute([$slug]);
            $found = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$found) break;
            $slug = $base . '-' . $i;
            $i++;
        }

        $supplier_id = null;
        if (!empty($_GET['supplier_slug'])) {
            $s = $pdo->prepare('SELECT id FROM suppliers WHERE slug = ? LIMIT 1'); $s->execute([$_GET['supplier_slug']]); if ($r=$s->fetch(PDO::FETCH_ASSOC)) $supplier_id = $r['id'];
        }

        $name = $title ?: 'Imported product';
        $stmt = $pdo->prepare('INSERT INTO products (name,slug,description,price,status,featured,images,featured_image,supplier_id) VALUES (?,?,?,?,?,?,?,?,?)');
        $images = $image ? $image : '';
        $priceVal = $price !== null ? $price : 0;
        $stmt->execute([$name,$slug,$desc,$priceVal,1,0,$images,$image,$supplier_id]);
        $id = $pdo->lastInsertId();
        echo "\nInserted product id={$id} slug={$slug}\n";
    } else {
        echo "\nTo insert into DB, add &insert=1 and optionally &supplier_slug=the-supplier-slug\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo '</pre>';
