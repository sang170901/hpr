<?php
// Tạo table sliders trực tiếp trong database
try {
    // Kết nối trực tiếp đến SQLite database
    $dbPath = 'backend/database.sqlite';
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tạo bảng sliders
    $sql = "CREATE TABLE IF NOT EXISTS sliders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(255),
        description TEXT,
        image VARCHAR(500) NOT NULL,
        link VARCHAR(500),
        link_text VARCHAR(100),
        display_order INTEGER DEFAULT 0,
        status INTEGER DEFAULT 1,
        start_date DATE,
        end_date DATE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    // Kiểm tra xem đã có dữ liệu chưa
    $check = $pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn();
    
    if ($check == 0) {
        // Thêm dữ liệu mẫu
        $stmt = $pdo->prepare("INSERT INTO sliders (title, subtitle, description, image, link, link_text, display_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $sampleData = [
            [
                'Vật Liệu Xây Dựng Chất Lượng Cao',
                'Khám phá bộ sưu tập vật liệu xây dựng đa dạng và hiện đại',
                'Chúng tôi cung cấp các sản phẩm vật liệu xây dựng chất lượng cao từ những thương hiệu uy tín hàng đầu.',
                'assets/images/slider/slide-1.jpg',
                'materials.php',
                'Khám Phá Ngay',
                1,
                1
            ],
            [
                'Công Nghệ Xây Dựng Tiên Tiến',
                'Giải pháp công nghệ hiện đại cho ngành xây dựng Việt Nam',
                'Ứng dụng những công nghệ tiên tiến nhất để nâng cao hiệu quả và chất lượng trong xây dựng.',
                'assets/images/slider/slide-2.jpg',
                'technology.php',
                'Tìm Hiểu Thêm',
                2,
                1
            ],
            [
                'Thiết Bị Xây Dựng Chuyên Nghiệp',
                'Máy móc và thiết bị hỗ trợ thi công hiệu quả',
                'Cung cấp đầy đủ các loại thiết bị xây dựng từ cơ bản đến chuyên nghiệp cho mọi dự án.',
                'assets/images/slider/slide-3.jpg',
                'equipment.php',
                'Xem Sản Phẩm',
                3,
                1
            ]
        ];
        
        foreach ($sampleData as $data) {
            $stmt->execute($data);
        }
        
        echo "Đã tạo bảng sliders và thêm " . count($sampleData) . " slider mẫu thành công!\n";
    } else {
        echo "Bảng sliders đã tồn tại với $check bản ghi.\n";
    }
    
    // Hiển thị cấu trúc bảng
    echo "\nCấu trúc bảng sliders:\n";
    $result = $pdo->query("PRAGMA table_info(sliders)");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['name']} ({$row['type']})\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>