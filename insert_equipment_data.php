<?php
require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();
    
    // Sample equipment data
    $equipmentData = [
        [
            'name' => 'Máy đào Hitachi ZX200-6',
            'description' => 'Máy đào thủy lực Hitachi ZX200-6 với động cơ tiết kiệm nhiên liệu và hiệu suất vận hành cao. Phù hợp cho các công trình xây dựng dân dụng và công nghiệp.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy đào',
            'supplier_name' => 'Hitachi Construction Machinery',
            'tags' => 'máy đào, thủy lực, hitachi, xây dựng',
            'price' => 2500000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy ủi Komatsu D65PX-18',
            'description' => 'Máy ủi bánh xích Komatsu D65PX-18 với lưỡi ủi hydrostatic và hệ thống điều khiển tự động. Công suất mạnh mẽ cho các dự án san lấp mặt bằng.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy ủi',
            'supplier_name' => 'Komatsu Vietnam',
            'tags' => 'máy ủi, komatsu, bánh xích, san lấp',
            'price' => 3200000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Cần cẩu tháp Liebherr 256 HC',
            'description' => 'Cần cẩu tháp Liebherr 256 HC với tầm với 65m và sức nâng tối đa 12 tấn. Phù hợp cho các dự án xây dựng cao tầng.',
            'category' => 'thiết bị',
            'subcategory' => 'Cần cẩu',
            'supplier_name' => 'Liebherr Vietnam',
            'tags' => 'cần cẩu, tháp, liebherr, cao tầng',
            'price' => 8500000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy trộn bê tông Schwing CP30',
            'description' => 'Máy trộn bê tông di động Schwing CP30 với năng suất 30m³/h. Tích hợp hệ thống điều khiển tự động và làm sạch tự động.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy trộn bê tông',
            'supplier_name' => 'Schwing Stetter Vietnam',
            'tags' => 'máy trộn, bê tông, schwing, di động',
            'price' => 1800000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy lu rung Bomag BW 174 AD-5',
            'description' => 'Máy lu rung đôi Bomag BW 174 AD-5 với trọng lượng 7.2 tấn. Phù hợp cho đầm nén đường bộ và nền móng.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy lu',
            'supplier_name' => 'Bomag Vietnam',
            'tags' => 'máy lu, rung, bomag, đầm nén',
            'price' => 950000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy khoan cọc Soilmec SR-40',
            'description' => 'Máy khoan cọc khô Soilmec SR-40 với độ sâu khoan tối đa 40m và đường kính cọc 1.5m. Trang bị hệ thống định vị GPS.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy khoan',
            'supplier_name' => 'Soilmec Asia',
            'tags' => 'máy khoan, cọc, soilmec, GPS',
            'price' => 4200000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Xe tải ben Hyundai HD270',
            'description' => 'Xe tải ben Hyundai HD270 với tải trọng 15 tấn và thùng ben thủy lực. Động cơ D6CA tiết kiệm nhiên liệu.',
            'category' => 'thiết bị',
            'subcategory' => 'Xe tải ben',
            'supplier_name' => 'Hyundai Thanh Cong',
            'tags' => 'xe tải, ben, hyundai, thủy lực',
            'price' => 1450000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy nén khí Atlas Copco GA75',
            'description' => 'Máy nén khí trục vít Atlas Copco GA75 với công suất 75kW và lưu lượng 12.5m³/phút. Tích hợp hệ thống tiết kiệm năng lượng.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy nén khí',
            'supplier_name' => 'Atlas Copco Vietnam',
            'tags' => 'máy nén, khí, atlas copco, trục vít',
            'price' => 850000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy bơm bê tông Putzmeister BSF36',
            'description' => 'Máy bơm bê tông tĩnh Putzmeister BSF36 với áp suất bơm 200 bar và lưu lượng 36m³/h. Phù hợp cho các công trình cao tầng.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy bơm bê tông',
            'supplier_name' => 'Putzmeister Vietnam',
            'tags' => 'máy bơm, bê tông, putzmeister, tĩnh',
            'price' => 2200000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy hàn Lincoln Electric Flextec 650X',
            'description' => 'Máy hàn đa năng Lincoln Electric Flextec 650X với công nghệ Inverter. Hỗ trợ hàn TIG, MIG/MAG và que hàn.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy hàn',
            'supplier_name' => 'Lincoln Electric Vietnam',
            'tags' => 'máy hàn, lincoln, inverter, đa năng',
            'price' => 180000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy cắt plasma Hypertherm Powermax 125',
            'description' => 'Máy cắt plasma Hypertherm Powermax 125 với khả năng cắt thép dày 38mm. Tích hợp công nghệ SmartSense.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy cắt',
            'supplier_name' => 'Hypertherm Vietnam',
            'tags' => 'máy cắt, plasma, hypertherm, thép',
            'price' => 320000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy phát điện Caterpillar C18',
            'description' => 'Máy phát điện Caterpillar C18 với công suất 600kVA và động cơ diesel tiết kiệm nhiên liệu. Tự động khởi động khi mất điện.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy phát điện',
            'supplier_name' => 'Caterpillar Vietnam',
            'tags' => 'máy phát điện, caterpillar, diesel, tự động',
            'price' => 1200000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Thang máy công trình Alimak Scando',
            'description' => 'Thang máy công trình Alimak Scando với tải trọng 2 tấn và tốc độ 36m/phút. Hệ thống an toàn kép và điều khiển tần số.',
            'category' => 'thiết bị',
            'subcategory' => 'Thang máy công trình',
            'supplier_name' => 'Alimak Vietnam',
            'tags' => 'thang máy, alimak, công trình, an toàn',
            'price' => 2800000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy làm đường Volvo P6820D',
            'description' => 'Máy rải thảm Volvo P6820D với khổ rải 2.5-8.5m và chiều dày tối đa 300mm. Hệ thống điều khiển tự động LeVeling Pro.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy làm đường',
            'supplier_name' => 'Volvo Construction Equipment',
            'tags' => 'máy rải, thảm, volvo, tự động',
            'price' => 3800000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy đầm bàn Wacker Neuson AP 1850',
            'description' => 'Máy đầm bàn Wacker Neuson AP 1850 với lực ly tâm 18kN và bề mặt đầm 500x370mm. Phù hợp cho đầm nền móng và đường giao thông.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy đầm',
            'supplier_name' => 'Wacker Neuson Vietnam',
            'tags' => 'máy đầm, bàn, wacker neuson, ly tâm',
            'price' => 85000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy cưa bê tông Husqvarna FS 7000 D',
            'description' => 'Máy cưa bê tông Husqvarna FS 7000 D với lưỡi cưa đường kính 1000mm và chiều sâu cắt 400mm. Hệ thống làm mát bằng nước.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy cưa',
            'supplier_name' => 'Husqvarna Vietnam',
            'tags' => 'máy cưa, bê tông, husqvarna, làm mát',
            'price' => 450000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy ép cọc JUNTTAN PMx26',
            'description' => 'Máy ép cọc thủy lực JUNTTAN PMx26 với lực ép 2600kN và chiều cao nâng 26m. Phù hợp cho ép cọc bê tông và cọc thép.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy ép cọc',
            'supplier_name' => 'JUNTTAN Vietnam',
            'tags' => 'máy ép, cọc, junttan, thủy lực',
            'price' => 5200000000,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Máy thổi bê tông Aliva 246',
            'description' => 'Máy thổi bê tông khô Aliva 246 với lưu lượng 6-20m³/h và áp suất làm việc 30 bar. Tích hợp hệ thống phun ướt tự động.',
            'category' => 'thiết bị',
            'subcategory' => 'Máy thổi bê tông',
            'supplier_name' => 'Aliva Vietnam',
            'tags' => 'máy thổi, bê tông, aliva, khô',
            'price' => 680000000,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo "Inserting equipment data...\n";
    
    foreach ($equipmentData as $equipment) {
        $sql = "INSERT INTO products (name, description, category, subcategory, supplier_name, tags, price, created_at) 
                VALUES (:name, :description, :category, :subcategory, :supplier_name, :tags, :price, :created_at)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($equipment);
        
        echo "Inserted: " . $equipment['name'] . "\n";
    }
    
    echo "\nSuccessfully inserted " . count($equipmentData) . " equipment items.\n";
    
    // Check total count
    $count = $pdo->query("SELECT COUNT(*) as total FROM products WHERE category = 'thiết bị'")->fetch()['total'];
    echo "Total equipment in database: $count\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo 'Stack trace: ' . $e->getTraceAsString() . "\n";
}
?>