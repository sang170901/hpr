<?php
// Add sample equipment data via web interface
require_once '../backend/inc/db.php';

// Check if we can connect
try {
    $pdo = getPDO();
    echo "<h2>Database Connection: Success</h2>";
    
    // Check current equipment count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category = ?");
    $stmt->execute(['thiết bị']);
    $currentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<p>Current equipment count: $currentCount</p>";
    
    if (isset($_POST['add_equipment'])) {
        // Sample equipment data
        $equipmentData = [
            [
                'name' => 'Máy đào Hitachi ZX200-6',
                'description' => 'Máy đào thủy lực Hitachi ZX200-6 với động cơ tiết kiệm nhiên liệu và hiệu suất vận hành cao. Phù hợp cho các công trình xây dựng dân dụng và công nghiệp.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy đào',
                'supplier_name' => 'Hitachi Construction Machinery',
                'tags' => 'máy đào, thủy lực, hitachi, xây dựng',
                'price' => 2500000000
            ],
            [
                'name' => 'Máy ủi Komatsu D65PX-18',
                'description' => 'Máy ủi bánh xích Komatsu D65PX-18 với lưỡi ủi hydrostatic và hệ thống điều khiển tự động. Công suất mạnh mẽ cho các dự án san lấp mặt bằng.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy ủi',
                'supplier_name' => 'Komatsu Vietnam',
                'tags' => 'máy ủi, komatsu, bánh xích, san lấp',
                'price' => 3200000000
            ],
            [
                'name' => 'Cần cẩu tháp Liebherr 256 HC',
                'description' => 'Cần cẩu tháp Liebherr 256 HC với tầm với 65m và sức nâng tối đa 12 tấn. Phù hợp cho các dự án xây dựng cao tầng.',
                'category' => 'thiết bị',
                'subcategory' => 'Cần cẩu',
                'supplier_name' => 'Liebherr Vietnam',
                'tags' => 'cần cẩu, tháp, liebherr, cao tầng',
                'price' => 8500000000
            ],
            [
                'name' => 'Máy trộn bê tông Schwing CP30',
                'description' => 'Máy trộn bê tông di động Schwing CP30 với năng suất 30m³/h. Tích hợp hệ thống điều khiển tự động và làm sạch tự động.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy trộn bê tông',
                'supplier_name' => 'Schwing Stetter Vietnam',
                'tags' => 'máy trộn, bê tông, schwing, di động',
                'price' => 1800000000
            ],
            [
                'name' => 'Máy lu rung Bomag BW 174 AD-5',
                'description' => 'Máy lu rung đôi Bomag BW 174 AD-5 với trọng lượng 7.2 tấn. Phù hợp cho đầm nén đường bộ và nền móng.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy lu',
                'supplier_name' => 'Bomag Vietnam',
                'tags' => 'máy lu, rung, bomag, đầm nén',
                'price' => 950000000
            ],
            [
                'name' => 'Máy khoan cọc Soilmec SR-40',
                'description' => 'Máy khoan cọc khô Soilmec SR-40 với độ sâu khoan tối đa 40m và đường kính cọc 1.5m. Trang bị hệ thống định vị GPS.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy khoan',
                'supplier_name' => 'Soilmec Asia',
                'tags' => 'máy khoan, cọc, soilmec, GPS',
                'price' => 4200000000
            ],
            [
                'name' => 'Xe tải ben Hyundai HD270',
                'description' => 'Xe tải ben Hyundai HD270 với tải trọng 15 tấn và thùng ben thủy lực. Động cơ D6CA tiết kiệm nhiên liệu.',
                'category' => 'thiết bị',
                'subcategory' => 'Xe tải ben',
                'supplier_name' => 'Hyundai Thanh Cong',
                'tags' => 'xe tải, ben, hyundai, thủy lực',
                'price' => 1450000000
            ],
            [
                'name' => 'Máy nén khí Atlas Copco GA75',
                'description' => 'Máy nén khí trục vít Atlas Copco GA75 với công suất 75kW và lưu lượng 12.5m³/phút. Tích hợp hệ thống tiết kiệm năng lượng.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy nén khí',
                'supplier_name' => 'Atlas Copco Vietnam',
                'tags' => 'máy nén, khí, atlas copco, trục vít',
                'price' => 850000000
            ],
            [
                'name' => 'Máy bơm bê tông Putzmeister BSF36',
                'description' => 'Máy bơm bê tông tĩnh Putzmeister BSF36 với áp suất bơm 200 bar và lưu lượng 36m³/h. Phù hợp cho các công trình cao tầng.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy bơm bê tông',
                'supplier_name' => 'Putzmeister Vietnam',
                'tags' => 'máy bơm, bê tông, putzmeister, tĩnh',
                'price' => 2200000000
            ],
            [
                'name' => 'Máy hàn Lincoln Electric Flextec 650X',
                'description' => 'Máy hàn đa năng Lincoln Electric Flextec 650X với công nghệ Inverter. Hỗ trợ hàn TIG, MIG/MAG và que hàn.',
                'category' => 'thiết bị',
                'subcategory' => 'Máy hàn',
                'supplier_name' => 'Lincoln Electric Vietnam',
                'tags' => 'máy hàn, lincoln, inverter, đa năng',
                'price' => 180000000
            ]
        ];
        
        $insertCount = 0;
        foreach ($equipmentData as $equipment) {
            try {
                $sql = "INSERT INTO products (name, description, category, subcategory, supplier_name, tags, price, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    $equipment['name'],
                    $equipment['description'],
                    $equipment['category'],
                    $equipment['subcategory'],
                    $equipment['supplier_name'],
                    $equipment['tags'],
                    $equipment['price'],
                    date('Y-m-d H:i:s')
                ]);
                
                if ($result) {
                    $insertCount++;
                    echo "<p style='color: green;'>✓ Inserted: " . $equipment['name'] . "</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Failed to insert " . $equipment['name'] . ": " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<h3 style='color: blue;'>Successfully inserted $insertCount equipment items!</h3>";
        
        // Show updated count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category = ?");
        $stmt->execute(['thiết bị']);
        $newCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>New equipment count: $newCount</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Database Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Equipment Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .button:hover { background: #005a87; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Equipment Sample Data</h1>
        
        <form method="POST">
            <button type="submit" name="add_equipment" class="button">Add Sample Equipment Data</button>
        </form>
        
        <hr>
        
        <p><a href="../equipment.php">→ View Equipment Page</a></p>
        <p><a href="../backend/products.php">→ Manage Products (Backend)</a></p>
    </div>
</body>
</html>