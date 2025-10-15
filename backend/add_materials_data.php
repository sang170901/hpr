<?php
// Add sample material data via web interface
require_once '../backend/inc/db.php';

// Check if we can connect
try {
    $pdo = getPDO();
    echo "<h2>Database Connection: Success</h2>";
    
    // Check current materials count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category = ?");
    $stmt->execute(['vật liệu']);
    $currentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<p>Current materials count: $currentCount</p>";
    
    if (isset($_POST['add_materials'])) {
        // Sample materials data
        $materialsData = [
            [
                'name' => 'Gạch Ceramic Viglacera',
                'description' => 'Gạch ceramic Viglacera với độ hấp thụ nước thấp và độ bền cao. Phù hợp cho ốp lát sàn và tường trong nhà và ngoài trời.',
                'category' => 'vật liệu',
                'subcategory' => 'Gạch ốp lát',
                'supplier_name' => 'Viglacera Corporation',
                'tags' => 'gạch, ceramic, viglacera, ốp lát',
                'price' => 350000
            ],
            [
                'name' => 'Thép Hòa Phát D16',
                'description' => 'Thép cây vằn Hòa Phát đường kính 16mm, tiêu chuẩn TCVN 1651-1:2008. Sử dụng cho kết cấu bê tông cốt thép.',
                'category' => 'vật liệu',
                'subcategory' => 'Thép xây dựng',
                'supplier_name' => 'Hòa Phát Group',
                'tags' => 'thép, hòa phát, cây vằn, kết cấu',
                'price' => 18500
            ],
            [
                'name' => 'Xi măng Holcim PCB40',
                'description' => 'Xi măng Portland hỗn hợp Holcim PCB40 với cường độ chịu nén 40MPa. Phù hợp cho các công trình dân dụng và công nghiệp.',
                'category' => 'vật liệu',
                'subcategory' => 'Xi măng',
                'supplier_name' => 'Holcim Vietnam',
                'tags' => 'xi măng, holcim, portland, PCB40',
                'price' => 95000
            ],
            [
                'name' => 'Gỗ Thông Chile',
                'description' => 'Gỗ thông Chile xẻ sấy khô, độ ẩm 8-12%. Sử dụng cho kết cấu gỗ, ván khuôn và nội thất.',
                'category' => 'vật liệu',
                'subcategory' => 'Gỗ xây dựng',
                'supplier_name' => 'VRG Đồng Nai',
                'tags' => 'gỗ, thông chile, sấy khô, kết cấu',
                'price' => 8500000
            ],
            [
                'name' => 'Cát san lấp',
                'description' => 'Cát san lấp đạt tiêu chuẩn xây dựng, độ sạch cao. Sử dụng cho san lấp mặt bằng và trộn bê tông.',
                'category' => 'vật liệu',
                'subcategory' => 'Cát đá',
                'supplier_name' => 'Công ty Cát đá Đồng Nai',
                'tags' => 'cát, san lấp, xây dựng, bê tông',
                'price' => 180000
            ],
            [
                'name' => 'Đá 1x2 Tân Uyên',
                'description' => 'Đá dăm 1x2 chất lượng cao từ mỏ đá Tân Uyên. Sử dụng cho trộn bê tông và làm đường.',
                'category' => 'vật liệu',
                'subcategory' => 'Cát đá',
                'supplier_name' => 'Công ty Đá Tân Uyên',
                'tags' => 'đá, dăm, tân uyên, bê tông',
                'price' => 220000
            ],
            [
                'name' => 'Sơn Dulux Weathershield',
                'description' => 'Sơn ngoại thất Dulux Weathershield chống thấm và chống phai màu. Bảo vệ tường khỏi tác động thời tiết.',
                'category' => 'vật liệu',
                'subcategory' => 'Sơn nước',
                'supplier_name' => 'AkzoNobel Vietnam',
                'tags' => 'sơn, dulux, ngoại thất, chống thấm',
                'price' => 750000
            ],
            [
                'name' => 'Tôn lạnh Hoa Sen',
                'description' => 'Tôn lạnh Hoa Sen dày 0.5mm, mạ kẽm chống gỉ. Sử dụng cho mái nhà và ốp tường.',
                'category' => 'vật liệu',
                'subcategory' => 'Tôn lạnh',
                'supplier_name' => 'Hoa Sen Group',
                'tags' => 'tôn, hoa sen, mạ kẽm, mái nhà',
                'price' => 185000
            ],
            [
                'name' => 'Gạch block 200x200x400',
                'description' => 'Gạch block rỗng 200x200x400mm, cường độ M50. Sử dụng cho xây tường không chịu lực.',
                'category' => 'vật liệu',
                'subcategory' => 'Gạch xây',
                'supplier_name' => 'Công ty Gạch Đồng Tâm',
                'tags' => 'gạch, block, rỗng, xây tường',
                'price' => 3500
            ],
            [
                'name' => 'Kính cường lực 8mm',
                'description' => 'Kính cường lực trong suốt dày 8mm, độ bền cao và an toàn. Sử dụng cho cửa kính và vách ngăn.',
                'category' => 'vật liệu',
                'subcategory' => 'Kính xây dựng',
                'supplier_name' => 'Công ty Kính Việt Nhật',
                'tags' => 'kính, cường lực, trong suốt, cửa kính',
                'price' => 280000
            ],
            [
                'name' => 'Gạch men Đồng Tâm 300x300',
                'description' => 'Gạch men Đồng Tâm 300x300mm, bề mặt bóng và chống trượt. Phù hợp cho ốp lát phòng bếp và phòng tắm.',
                'category' => 'vật liệu',
                'subcategory' => 'Gạch ốp lát',
                'supplier_name' => 'Công ty Gạch Đồng Tâm',
                'tags' => 'gạch men, đồng tâm, bóng, chống trượt',
                'price' => 65000
            ],
            [
                'name' => 'Ống PPR Bình Minh D25',
                'description' => 'Ống PPR Bình Minh đường kính 25mm, chịu nhiệt và áp suất cao. Sử dụng cho hệ thống cấp nước nóng lạnh.',
                'category' => 'vật liệu',
                'subcategory' => 'Ống nước',
                'supplier_name' => 'Nhựa Bình Minh',
                'tags' => 'ống, PPR, bình minh, cấp nước',
                'price' => 18000
            ],
            [
                'name' => 'Ngói Đất Việt',
                'description' => 'Ngói đất nung Việt Nam chất lượng cao, màu đỏ tự nhiên. Chống thấm và cách nhiệt tốt.',
                'category' => 'vật liệu',
                'subcategory' => 'Ngói lợp',
                'supplier_name' => 'Công ty Ngói Đất Việt',
                'tags' => 'ngói, đất nung, việt nam, chống thấm',
                'price' => 4500
            ],
            [
                'name' => 'Sàn gỗ An Cường',
                'description' => 'Sàn gỗ công nghiệp An Cường dày 12mm, chống nước và chống trầy xước. Lắp đặt dễ dàng.',
                'category' => 'vật liệu',
                'subcategory' => 'Sàn gỗ',
                'supplier_name' => 'An Cường Wood',
                'tags' => 'sàn gỗ, an cường, công nghiệp, chống nước',
                'price' => 485000
            ],
            [
                'name' => 'Gạch Granite 600x600',
                'description' => 'Gạch granite Ấn Độ 600x600mm, bề mặt nhám chống trượt. Độ bền cao và màu sắc đa dạng.',
                'category' => 'vật liệu',
                'subcategory' => 'Đá tự nhiên',
                'supplier_name' => 'Công ty Đá Granite Việt',
                'tags' => 'granite, ấn độ, chống trượt, bền',
                'price' => 320000
            ],
            [
                'name' => 'Tấm Cemboard SCG',
                'description' => 'Tấm xi măng nhẹ SCG dày 6mm, chống cháy và chống ẩm. Sử dụng cho tường vách và trần nhà.',
                'category' => 'vật liệu',
                'subcategory' => 'Tấm ốp',
                'supplier_name' => 'SCG Vietnam',
                'tags' => 'cemboard, SCG, xi măng nhẹ, chống cháy',
                'price' => 125000
            ],
            [
                'name' => 'Cửa nhựa uPVC Vĩnh Hưng',
                'description' => 'Cửa nhựa uPVC Vĩnh Hưng, cách nhiệt và cách âm tốt. Không bị mối mọt và biến dạng.',
                'category' => 'vật liệu',
                'subcategory' => 'Cửa nhựa',
                'supplier_name' => 'Vĩnh Hưng JSC',
                'tags' => 'cửa nhựa, uPVC, vĩnh hưng, cách nhiệt',
                'price' => 1850000
            ],
            [
                'name' => 'Keo dán gạch Mapei',
                'description' => 'Keo dán gạch Mapei Keraflex, độ bám dính cao và chống nước. Phù hợp cho mọi loại gạch.',
                'category' => 'vật liệu',
                'subcategory' => 'Keo dán',
                'supplier_name' => 'Mapei Vietnam',
                'tags' => 'keo dán, mapei, keraflex, chống nước',
                'price' => 185000
            ]
        ];
        
        $insertCount = 0;
        foreach ($materialsData as $material) {
            try {
                $sql = "INSERT INTO products (name, description, category, subcategory, supplier_name, tags, price, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    $material['name'],
                    $material['description'],
                    $material['category'],
                    $material['subcategory'],
                    $material['supplier_name'],
                    $material['tags'],
                    $material['price'],
                    date('Y-m-d H:i:s')
                ]);
                
                if ($result) {
                    $insertCount++;
                    echo "<p style='color: green;'>✓ Inserted: " . $material['name'] . "</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Failed to insert " . $material['name'] . ": " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<h3 style='color: blue;'>Successfully inserted $insertCount material items!</h3>";
        
        // Show updated count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category = ?");
        $stmt->execute(['vật liệu']);
        $newCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>New materials count: $newCount</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Database Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Materials Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Materials Sample Data</h1>
        
        <form method="POST">
            <button type="submit" name="add_materials" class="button">Add Sample Materials Data</button>
        </form>
        
        <hr>
        
        <p><a href="../materials.php">→ View Materials Page</a></p>
        <p><a href="../backend/products.php">→ Manage Products (Backend)</a></p>
    </div>
</body>
</html>