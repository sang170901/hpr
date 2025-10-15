<?php
// Add sample technology data via web interface
require_once '../backend/inc/db.php';

// Check if we can connect
try {
    $pdo = getPDO();
    echo "<h2>Database Connection: Success</h2>";
    
    // Check current technology count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category = ?");
    $stmt->execute(['công nghệ']);
    $currentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<p>Current technology count: $currentCount</p>";
    
    if (isset($_POST['add_technology'])) {
        // Sample technology data
        $technologyData = [
            [
                'name' => 'BIM (Building Information Modeling)',
                'description' => 'Công nghệ mô hình hóa thông tin công trình BIM cho phép tạo ra mô hình 3D chi tiết và quản lý thông tin toàn diện về dự án xây dựng từ thiết kế đến vận hành.',
                'category' => 'công nghệ',
                'subcategory' => 'Phần mềm thiết kế',
                'supplier_name' => 'Autodesk Vietnam',
                'tags' => 'BIM, 3D modeling, thiết kế, quản lý dự án',
                'price' => 125000000
            ],
            [
                'name' => 'Drone khảo sát địa hình',
                'description' => 'Máy bay không người lái chuyên dụng cho khảo sát địa hình, đo đạc bản đồ và giám sát tiến độ thi công với độ chính xác cao và tiết kiệm thời gian.',
                'category' => 'công nghệ',
                'subcategory' => 'Thiết bị khảo sát',
                'supplier_name' => 'DJI Enterprise Vietnam',
                'tags' => 'drone, khảo sát, đo đạc, giám sát',
                'price' => 450000000
            ],
            [
                'name' => 'Hệ thống Smart Building IoT',
                'description' => 'Giải pháp tòa nhà thông minh tích hợp IoT để quản lý chiếu sáng, điều hòa, an ninh và năng lượng tự động, tối ưu hóa hiệu quả vận hành.',
                'category' => 'công nghệ',
                'subcategory' => 'IoT & Automation',
                'supplier_name' => 'Schneider Electric Vietnam',
                'tags' => 'smart building, IoT, tự động hóa, tiết kiệm năng lượng',
                'price' => 2800000000
            ],
            [
                'name' => 'Máy in 3D bê tông',
                'description' => 'Công nghệ in 3D bê tông cho phép xây dựng các cấu kiện phức tạp một cách nhanh chóng, chính xác và giảm thiểu lãng phí vật liệu.',
                'category' => 'công nghệ',
                'subcategory' => 'In 3D',
                'supplier_name' => 'COBOD Vietnam',
                'tags' => 'in 3D, bê tông, xây dựng nhanh, công nghệ mới',
                'price' => 8500000000
            ],
            [
                'name' => 'Cảm biến theo dõi kết cấu',
                'description' => 'Hệ thống cảm biến wireless giám sát sức khỏe kết cấu công trình 24/7, cảnh báo sớm về biến dạng và rung động bất thường.',
                'category' => 'công nghệ',
                'subcategory' => 'Monitoring System',
                'supplier_name' => 'Geokon Vietnam',
                'tags' => 'cảm biến, giám sát, kết cấu, cảnh báo',
                'price' => 850000000
            ],
            [
                'name' => 'Robot hàn tự động',
                'description' => 'Robot hàn công nghiệp 6 trục với độ chính xác cao, tăng năng suất và chất lượng mối hàn trong sản xuất kết cấu thép.',
                'category' => 'công nghệ',
                'subcategory' => 'Robot công nghiệp',
                'supplier_name' => 'ABB Vietnam',
                'tags' => 'robot, hàn tự động, kết cấu thép, tự động hóa',
                'price' => 3200000000
            ],
            [
                'name' => 'Phần mềm quản lý dự án Primavera',
                'description' => 'Phần mềm quản lý dự án chuyên nghiệp cho ngành xây dựng, lập kế hoạch chi tiết, theo dõi tiến độ và quản lý nguồn lực hiệu quả.',
                'category' => 'công nghệ',
                'subcategory' => 'Phần mềm quản lý',
                'supplier_name' => 'Oracle Vietnam',
                'tags' => 'primavera, quản lý dự án, lập kế hoạch, tiến độ',
                'price' => 180000000
            ],
            [
                'name' => 'Kính thực tế ảo AR/VR',
                'description' => 'Thiết bị thực tế ảo và thực tế tăng cường cho trực quan hóa thiết kế, đào tạo an toàn và thuyết trình dự án một cách sinh động.',
                'category' => 'công nghệ',
                'subcategory' => 'AR/VR Technology',
                'supplier_name' => 'Microsoft Vietnam',
                'tags' => 'AR, VR, thực tế ảo, trực quan hóa',
                'price' => 95000000
            ],
            [
                'name' => 'Laser Scanner 3D',
                'description' => 'Máy quét laser 3D chính xác cao để đo đạc hiện trạng công trình, tạo point cloud và mô hình 3D chi tiết cho thiết kế và as-built.',
                'category' => 'công nghệ',
                'subcategory' => 'Thiết bị đo đạc',
                'supplier_name' => 'Leica Geosystems Vietnam',
                'tags' => 'laser scanner, 3D scanning, đo đạc, point cloud',
                'price' => 1850000000
            ],
            [
                'name' => 'Hệ thống RFID quản lý vật tư',
                'description' => 'Giải pháp RFID để theo dõi và quản lý vật tư, thiết bị trên công trường, tối ưu hóa logistics và giảm thất thoát.',
                'category' => 'công nghệ',
                'subcategory' => 'RFID System',
                'supplier_name' => 'Zebra Technologies Vietnam',
                'tags' => 'RFID, quản lý vật tư, theo dõi, logistics',
                'price' => 680000000
            ],
            [
                'name' => 'Phần mềm tính toán kết cấu SAP2000',
                'description' => 'Phần mềm phân tích và thiết kế kết cấu tiên tiến với khả năng mô phỏng phi tuyến, động đất và tối ưu hóa thiết kế.',
                'category' => 'công nghệ',
                'subcategory' => 'Phần mềm tính toán',
                'supplier_name' => 'CSI Vietnam',
                'tags' => 'SAP2000, tính toán kết cấu, phân tích, thiết kế',
                'price' => 220000000
            ],
            [
                'name' => 'Camera giám sát AI thông minh',
                'description' => 'Hệ thống camera AI nhận diện tự động vi phạm an toàn lao động, phát hiện người và xe không đội mũ bảo hiểm trên công trường.',
                'category' => 'công nghệ',
                'subcategory' => 'AI Security',
                'supplier_name' => 'Hikvision Vietnam',
                'tags' => 'AI camera, giám sát, an toàn lao động, nhận diện',
                'price' => 350000000
            ],
            [
                'name' => 'Máy cắt CNC plasma',
                'description' => 'Máy cắt CNC plasma điều khiển số với độ chính xác cao, cắt được thép dày 50mm, tích hợp phần mềm CAD/CAM.',
                'category' => 'công nghệ',
                'subcategory' => 'CNC Machine',
                'supplier_name' => 'Messer Vietnam',
                'tags' => 'CNC, plasma cutting, tự động hóa, chính xác',
                'price' => 2400000000
            ],
            [
                'name' => 'Hệ thống BMS tòa nhà',
                'description' => 'Building Management System tập trung điều khiển và giám sát tất cả hệ thống M&E trong tòa nhà, tối ưu hóa năng lượng và bảo trì.',
                'category' => 'công nghệ',
                'subcategory' => 'BMS System',
                'supplier_name' => 'Johnson Controls Vietnam',
                'tags' => 'BMS, quản lý tòa nhà, M&E, năng lượng',
                'price' => 1950000000
            ],
            [
                'name' => 'Máy đo GPS RTK',
                'description' => 'Máy định vị GPS RTK độ chính xác centimet, kết nối mạng CORS để đo đạc thi công chính xác cao trong thời gian thực.',
                'category' => 'công nghệ',
                'subcategory' => 'GPS Technology',
                'supplier_name' => 'Trimble Vietnam',
                'tags' => 'GPS, RTK, đo đạc, định vị chính xác',
                'price' => 420000000
            ],
            [
                'name' => 'Phần mềm Revit Architecture',
                'description' => 'Phần mềm thiết kế kiến trúc 3D chuyên nghiệp với công cụ BIM, hỗ trợ collaborative design và tự động tạo bản vẽ.',
                'category' => 'công nghệ',
                'subcategory' => 'Phần mềm thiết kế',
                'supplier_name' => 'Autodesk Vietnam',
                'tags' => 'revit, kiến trúc, 3D, BIM',
                'price' => 85000000
            ],
            [
                'name' => 'Máy thử nghiệm vật liệu tự động',
                'description' => 'Hệ thống thử nghiệm cơ tính vật liệu tự động với khả năng test nén, kéo, uốn cho bê tông, thép và vật liệu composite.',
                'category' => 'công nghệ',
                'subcategory' => 'Testing Equipment',
                'supplier_name' => 'Controls Group Vietnam',
                'tags' => 'thử nghiệm, vật liệu, tự động, cơ tính',
                'price' => 1250000000
            ],
            [
                'name' => 'Hệ thống năng lượng mặt trời',
                'description' => 'Giải pháp năng lượng mặt trời tích hợp inverter thông minh, hệ thống giám sát và lưu trữ năng lượng cho công trình xanh.',
                'category' => 'công nghệ',
                'subcategory' => 'Green Energy',
                'supplier_name' => 'SolarBK Vietnam',
                'tags' => 'năng lượng mặt trời, xanh, bền vững, inverter',
                'price' => 3800000000
            ]
        ];
        
        $insertCount = 0;
        foreach ($technologyData as $technology) {
            try {
                $sql = "INSERT INTO products (name, description, category, subcategory, supplier_name, tags, price, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    $technology['name'],
                    $technology['description'],
                    $technology['category'],
                    $technology['subcategory'],
                    $technology['supplier_name'],
                    $technology['tags'],
                    $technology['price'],
                    date('Y-m-d H:i:s')
                ]);
                
                if ($result) {
                    $insertCount++;
                    echo "<p style='color: green;'>✓ Inserted: " . $technology['name'] . "</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Failed to insert " . $technology['name'] . ": " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<h3 style='color: blue;'>Successfully inserted $insertCount technology items!</h3>";
        
        // Show updated count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category = ?");
        $stmt->execute(['công nghệ']);
        $newCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>New technology count: $newCount</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Database Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Technology Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .button { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .button:hover { background: #20c997; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Technology Sample Data</h1>
        
        <form method="POST">
            <button type="submit" name="add_technology" class="button">Add Sample Technology Data</button>
        </form>
        
        <hr>
        
        <p><a href="../technology.php">→ View Technology Page</a></p>
        <p><a href="../backend/products.php">→ Manage Products (Backend)</a></p>
    </div>
</body>
</html>