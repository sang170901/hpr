<?php
// Web script: insert sample products into DB for testing
// Open in browser: /backend/scripts/insert_sample_products_web.php
header('Content-Type: text/html; charset=utf-8');
echo '<pre style="white-space:pre-wrap;font-family:Consolas,monospace">';
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();

    $samples = [
        [
            'name'=>'Sàn nhựa vinyl Resilient A',
            'slug'=>'resilient-flooring-san-nhua-vinyl-resilient-a',
            'price'=>250000,
            'manufacturer'=>'VinFloor Co.',
            'origin'=>'Việt Nam',
            'material_type'=>'Vinyl',
            'application'=>'Nội thất, Thương mại',
            'product_function'=>'Chống ẩm, chống trượt',
            'category'=>'Sàn nhựa',
            'thickness'=>'3.0',
            'color'=>'Be',
            'warranty'=>'24 tháng',
            'featured_image'=>'/backend/assets/images/sample1.jpg',
            'images'=>'/backend/assets/images/sample1.jpg',
            'stock'=>50
        ],
        [
            'name'=>'Sàn vinyl cao cấp B',
            'slug'=>'vinyl-cao-cap-b',
            'price'=>320000,
            'manufacturer'=>'FloorPro',
            'origin'=>'Thái Lan',
            'material_type'=>'Vinyl',
            'application'=>'Dân dụng, Văn phòng',
            'product_function'=>'Chống va đập, dễ vệ sinh',
            'category'=>'Sàn vinyl',
            'thickness'=>'4.0',
            'color'=>'Xám',
            'warranty'=>'36 tháng',
            'featured_image'=>'/backend/assets/images/sample2.jpg',
            'images'=>'/backend/assets/images/sample2.jpg',
            'stock'=>30
        ],
        [
            'name'=>'Sàn cao su EPDM C',
            'slug'=>'san-cao-su-epdm-c',
            'price'=>280000,
            'manufacturer'=>'RubberTech',
            'origin'=>'Việt Nam',
            'material_type'=>'Cao su',
            'application'=>'Ngoại thất, sân chơi',
            'product_function'=>'Êm, giảm chấn',
            'category'=>'Sàn cao su',
            'thickness'=>'6.0',
            'color'=>'Đen',
            'warranty'=>'24 tháng',
            'featured_image'=>'/backend/assets/images/sample3.jpg',
            'images'=>'/backend/assets/images/sample3.jpg',
            'stock'=>20
        ],
        [
            'name'=>'Sàn nhựa SPC D',
            'slug'=>'spc-flooring-d',
            'price'=>400000,
            'manufacturer'=>'SPC Maker',
            'origin'=>'Hàn Quốc',
            'material_type'=>'SPC',
            'application'=>'Cư dân cao cấp',
            'product_function'=>'Chống nước, ổn định',
            'category'=>'Sàn SPC',
            'thickness'=>'5.5',
            'color'=>'Nâu',
            'warranty'=>'60 tháng',
            'featured_image'=>'/backend/assets/images/sample4.jpg',
            'images'=>'/backend/assets/images/sample4.jpg',
            'stock'=>15
        ],
        [
            'name'=>'Sàn nhựa thương mại E',
            'slug'=>'san-nhua-thuong-mai-e',
            'price'=>180000,
            'manufacturer'=>'CommercialFloor',
            'origin'=>'Trung Quốc',
            'material_type'=>'Vinyl',
            'application'=>'Thương mại, cửa hàng',
            'product_function'=>'Bền, giá rẻ',
            'category'=>'Sàn nhựa thương mại',
            'thickness'=>'2.0',
            'color'=>'Đỏ',
            'warranty'=>'12 tháng',
            'featured_image'=>'/backend/assets/images/sample5.jpg',
            'images'=>'/backend/assets/images/sample5.jpg',
            'stock'=>100
        ],
    ];

    echo "Inserting sample products (idempotent by slug)...\n";
    foreach ($samples as $s) {
        // check existing
        $stmt = $pdo->prepare('SELECT id FROM products WHERE slug = ? LIMIT 1');
        $stmt->execute([$s['slug']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo "Exists: {$s['slug']} (id={$row['id']})\n";
            continue;
        }

        // build insert fields present in DB
        $cols = $pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC);
        $names = array_map(function($c){return $c['name'];}, $cols);
        $insertFields = [];
        $insertValues = [];
        foreach ($s as $k=>$v) {
            if (in_array($k, $names)) { $insertFields[] = $k; $insertValues[] = $v; }
        }
        if (empty($insertFields)) { echo "No matching columns for product {$s['slug']}, skipped.\n"; continue; }

        $placeholders = implode(',', array_fill(0, count($insertFields), '?'));
        $sql = 'INSERT INTO products (' . implode(',', $insertFields) . ') VALUES (' . $placeholders . ')';
        $pdo->prepare($sql)->execute($insertValues);
        $newId = $pdo->lastInsertId();
        echo "Inserted {$s['slug']} as id={$newId}\n";
    }

    echo "\nDone. Check admin products list.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo '</pre>';
