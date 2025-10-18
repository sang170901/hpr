<?php
require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();
    
    // Tạo bảng sliders
    $sql = "CREATE TABLE IF NOT EXISTS sliders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(255) NULL,
        description TEXT NULL,
        image VARCHAR(500) NOT NULL,
        link VARCHAR(500) NULL,
        link_text VARCHAR(100) NULL,
        display_order INTEGER DEFAULT 0,
        status INTEGER DEFAULT 1,
        start_date DATE NULL,
        end_date DATE NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    // Thêm dữ liệu mẫu
    $sampleData = [
        [
            'title' => 'Vật Liệu Xây Dựng Chất Lượng Cao',
            'subtitle' => 'Khám phá bộ sưu tập vật liệu xây dựng đa dạng và hiện đại',
            'description' => 'Chúng tôi cung cấp các sản phẩm vật liệu xây dựng chất lượng cao từ những thương hiệu uy tín hàng đầu.',
            'image' => 'assets/images/slider/slide-1.jpg',
            'link' => 'materials.php',
            'link_text' => 'Khám Phá Ngay',
            'display_order' => 1,
            'status' => 1
        ],
        [
            'title' => 'Công Nghệ Xây Dựng Tiên Tiến',
            'subtitle' => 'Giải pháp công nghệ hiện đại cho ngành xây dựng Việt Nam',
            'description' => 'Ứng dụng những công nghệ tiên tiến nhất để nâng cao hiệu quả và chất lượng trong xây dựng.',
            'image' => 'assets/images/slider/slide-2.jpg',
            'link' => 'technology.php',
            'link_text' => 'Tìm Hiểu Thêm',
            'display_order' => 2,
            'status' => 1
        ],
        [
            'title' => 'Thiết Bị Xây Dựng Chuyên Nghiệp',
            'subtitle' => 'Máy móc và thiết bị hỗ trợ thi công hiệu quả',
            'description' => 'Cung cấp đầy đủ các loại thiết bị xây dựng từ cơ bản đến chuyên nghiệp cho mọi dự án.',
            'image' => 'assets/images/slider/slide-3.jpg',
            'link' => 'equipment.php',
            'link_text' => 'Xem Sản Phẩm',
            'display_order' => 3,
            'status' => 1
        ]
    ];
    
    // Kiểm tra xem đã có dữ liệu chưa
    $check = $pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn();
    
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO sliders (title, subtitle, description, image, link, link_text, display_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($sampleData as $data) {
            $stmt->execute([
                $data['title'],
                $data['subtitle'],
                $data['description'],
                $data['image'],
                $data['link'],
                $data['link_text'],
                $data['display_order'],
                $data['status']
            ]);
        }
        echo "Đã tạo bảng sliders và thêm dữ liệu mẫu thành công!\n";
    } else {
        echo "Bảng sliders đã tồn tại với $check bản ghi.\n";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>