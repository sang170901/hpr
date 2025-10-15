<?php
// Web script: insert sample suppliers into DB for testing
// Open in browser: /backend/scripts/insert_sample_suppliers_web.php
header('Content-Type: text/html; charset=utf-8');
echo '<pre style="white-space:pre-wrap;font-family:Consolas,monospace">';
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    $samples = [
        [
            'name' => 'VinFloor Co.',
            'slug' => 'vinfloor-co',
            'email' => 'contact@vinfloor.vn',
            'phone' => '0241234567',
            'address' => 'Hà Nội, Việt Nam',
            'logo' => '/backend/assets/images/vendor1.png',
            'description' => 'Nhà sản xuất sàn nhựa chất lượng cao.'
        ],
        [
            'name' => 'FloorPro',
            'slug' => 'floorpro',
            'email' => 'info@floorpro.com',
            'phone' => '0289876543',
            'address' => 'Hồ Chí Minh, Việt Nam',
            'logo' => '/backend/assets/images/vendor2.png',
            'description' => 'Cung cấp vật liệu sàn chuyên nghiệp.'
        ],
        [
            'name' => 'RubberTech',
            'slug' => 'rubbertech',
            'email' => 'sales@rubbertech.vn',
            'phone' => '0247654321',
            'address' => 'Đà Nẵng, Việt Nam',
            'logo' => '/backend/assets/images/vendor3.png',
            'description' => 'Sản xuất sàn cao su cho sân chơi và thể thao.'
        ],
        [
            'name' => 'SPC Maker',
            'slug' => 'spc-maker',
            'email' => 'hello@spcmaker.kr',
            'phone' => '+82-10-1234-5678',
            'address' => 'Seoul, Korea',
            'logo' => '/backend/assets/images/vendor4.png',
            'description' => 'Nhà máy sản xuất SPC chất lượng cao.'
        ],
        [
            'name' => 'CommercialFloor',
            'slug' => 'commercialfloor',
            'email' => 'support@commercialfloor.cn',
            'phone' => '+86-10-5555-6666',
            'address' => 'Quảng Châu, Trung Quốc',
            'logo' => '/backend/assets/images/vendor5.png',
            'description' => 'Giải pháp sàn cho thương mại với chi phí hợp lý.'
        ],
    ];

    echo "Inserting sample suppliers (idempotent by slug)...\n";
    foreach ($samples as $s) {
        $stmt = $pdo->prepare('SELECT id FROM suppliers WHERE slug = ? LIMIT 1');
        $stmt->execute([$s['slug']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo "Exists: {$s['slug']} (id={$row['id']})\n";
            continue;
        }

        // Insert matching columns only
        $cols = $pdo->query("PRAGMA table_info(suppliers)")->fetchAll(PDO::FETCH_ASSOC);
        $names = array_map(function($c){return $c['name'];}, $cols);
        $insertFields = [];
        $insertValues = [];
        foreach ($s as $k => $v) {
            if (in_array($k, $names)) { $insertFields[] = $k; $insertValues[] = $v; }
        }
        if (empty($insertFields)) { echo "No matching columns for supplier {$s['slug']}, skipped.\n"; continue; }

        $placeholders = implode(',', array_fill(0, count($insertFields), '?'));
        $sql = 'INSERT INTO suppliers (' . implode(',', $insertFields) . ') VALUES (' . $placeholders . ')';
        $pdo->prepare($sql)->execute($insertValues);
        $newId = $pdo->lastInsertId();
        echo "Inserted {$s['slug']} as id={$newId}\n";
    }

    echo "\nDone. Check admin suppliers list.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo '</pre>';
