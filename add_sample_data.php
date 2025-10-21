<?php
require 'backend/inc/db.php';
$pdo = getPDO();

echo "=== THÊM DỮ LIỆU MẪU: 20 NHÀ CUNG CẤP + 50 SẢN PHẨM ===\n\n";

// Danh sách 20 nhà cung cấp thực tế tại Việt Nam
$suppliers = [
    // Vật liệu xây dựng (category_id = 1)
    [
        'name' => 'CÔNG TY CỔ PHẦN XI MĂNG HOÀNG THẠCH',
        'slug' => 'xi-mang-hoang-thach',
        'email' => 'info@hoangthachcement.com',
        'phone' => '0274.3831.888',
        'address' => 'Đường Quốc lộ 1A, Phường Hoàng Văn Thụ, TP. Hòa Bình',
        'description' => 'Sản xuất và phân phối xi măng chất lượng cao, cung cấp toàn quốc',
        'category_id' => 1,
        'logo' => 'https://hoangthachcement.com/wp-content/uploads/2020/01/logo.png'
    ],
    [
        'name' => 'CÔNG TY TNHH THÉP POMINA',
        'slug' => 'thep-pomina',
        'email' => 'sales@pomina.com.vn',
        'phone' => '028.3622.4567',
        'address' => 'Số 2A Đường Nguyễn Thị Minh Khai, Q.3, TP.HCM',
        'description' => 'Sản xuất thép xây dựng, thép cuộn, thép hình',
        'category_id' => 1,
        'logo' => 'https://pomina.com.vn/images/logo.png'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN GẠCH ỐP LÁT ĐỒNG TÂM',
        'slug' => 'gach-dong-tam',
        'email' => 'info@dongtam.vn',
        'phone' => '0650.3831.999',
        'address' => 'KCN Mỹ Xuân B, Tỉnh Bà Rịa - Vũng Tàu',
        'description' => 'Sản xuất gạch ốp lát, gạch granite, gạch ceramic cao cấp',
        'category_id' => 1,
        'logo' => 'https://dongtam.vn/images/logo-dongtam.png'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN SƠN HÀ NỘI',
        'slug' => 'son-ha-noi',
        'email' => 'contact@sonhanoi.com',
        'phone' => '024.3574.8888',
        'address' => 'Số 8 Phạm Hùng, Cầu Giấy, Hà Nội',
        'description' => 'Sản xuất sơn nước, sơn dầu, sơn chống thấm chất lượng cao',
        'category_id' => 1,
        'logo' => 'https://sonhanoi.com/assets/images/logo.png'
    ],
    [
        'name' => 'CÔNG TY TNHH KÍNH VIỆT NHẬT',
        'slug' => 'kinh-viet-nhat',
        'email' => 'info@vietnhatglass.com',
        'phone' => '028.3825.7777',
        'address' => '123 Lê Lợi, Q.1, TP.HCM',
        'description' => 'Kính xây dựng, kính cường lực, kính phản quang',
        'category_id' => 1,
        'logo' => 'https://vietnhatglass.com/logo.png'
    ],

    // Nội thất (category_id = 2)
    [
        'name' => 'CÔNG TY TNHH NỘI THẤT HOÀNG GIA',
        'slug' => 'noi-that-hoang-gia',
        'email' => 'sales@hoanggiagroup.vn',
        'phone' => '024.3573.2222',
        'address' => 'Số 456 Nguyễn Trãi, Thanh Xuân, Hà Nội',
        'description' => 'Thiết kế và thi công nội thất cao cấp, tủ bếp, nội thất văn phòng',
        'category_id' => 2,
        'logo' => 'https://hoanggiagroup.vn/images/logo.png'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN NỘI THẤT AN CƯỜNG',
        'slug' => 'noi-that-an-cuong',
        'email' => 'info@ancuong.com',
        'phone' => '028.3927.1111',
        'address' => 'Lô E2a-7, Đường D1, KCN Sài Gòn, TP.HCM',
        'description' => 'Sản xuất gỗ MDF, HDF, tủ bếp, cửa gỗ công nghiệp',
        'category_id' => 2,
        'logo' => 'https://ancuong.com/assets/logo.png'
    ],
    [
        'name' => 'CÔNG TY TNHH TỦ BẾP ACRYLIC VIỆT NAM',
        'slug' => 'tu-bep-acrylic',
        'email' => 'contact@acrylicvn.com',
        'phone' => '0236.3955.333',
        'address' => 'KCN Hòa Khánh, Q.Liên Chiểu, TP.Đà Nẵng',
        'description' => 'Chuyên sản xuất tủ bếp acrylic, tủ bếp laminate cao cấp',
        'category_id' => 2,
        'logo' => 'https://acrylicvn.com/logo.jpg'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN NITORI VIỆT NAM',
        'slug' => 'nitori-vietnam',
        'email' => 'info@nitori.vn',
        'phone' => '028.7108.8888',
        'address' => 'Tầng 6, Tòa nhà Sài Gòn Centre, Q.1, TP.HCM',
        'description' => 'Nội thất gia đình phong cách Nhật Bản, đồ dùng gia đình',
        'category_id' => 2,
        'logo' => 'https://nitori.vn/assets/logo.png'
    ],

    // Cảnh quan (category_id = 3)
    [
        'name' => 'CÔNG TY TNHH CẢNH QUAN XANH VIỆT',
        'slug' => 'canh-quan-xanh-viet',
        'email' => 'info@greenlandscape.vn',
        'phone' => '028.3848.9999',
        'address' => '789 Võ Văn Tần, Q.3, TP.HCM',
        'description' => 'Thiết kế cảnh quan, thi công sân vườn, cây xanh đô thị',
        'category_id' => 3,
        'logo' => 'https://greenlandscape.vn/logo.png'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN CÔNG VIÊN CÂY XANH',
        'slug' => 'cong-vien-cay-xanh',
        'email' => 'sales@greenparkco.vn',
        'phone' => '024.3715.4444',
        'address' => 'Số 15 Tô Ngọc Vân, Tây Hồ, Hà Nội',
        'description' => 'Cung cấp cây giống, thiết bị tưới, vật tư cảnh quan',
        'category_id' => 3,
        'logo' => 'https://greenparkco.vn/images/logo.jpg'
    ],

    // Điện - Nước (category_id = 4)
    [
        'name' => 'CÔNG TY TNHH ĐIỆN LẠNH ĐẠI PHÁT',
        'slug' => 'dien-lanh-dai-phat',
        'email' => 'info@dienlanhphat.com',
        'phone' => '028.3842.6666',
        'address' => '234 Lý Thường Kiệt, Q.Tân Bình, TP.HCM',
        'description' => 'Hệ thống điện, điện lạnh, máy lạnh công nghiệp',
        'category_id' => 4,
        'logo' => 'https://dienlanhphat.com/logo.png'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN NHỰA ĐÔNG Á',
        'slug' => 'nhua-dong-a',
        'email' => 'contact@dongaplastic.com',
        'phone' => '0274.3567.888',
        'address' => 'KCN Phố Nối A, Huyện Yên Mỹ, Tỉnh Hưng Yên',
        'description' => 'Ống nhựa PVC, ống HDPE, phụ kiện đường ống',
        'category_id' => 4,
        'logo' => 'https://dongaplastic.com/assets/logo.png'
    ],
    [
        'name' => 'CÔNG TY TNHH THIẾT BỊ VỆ SINH INAX',
        'slug' => 'thiet-bi-ve-sinh-inax',
        'email' => 'info@inax.com.vn',
        'phone' => '028.3848.1111',
        'address' => 'Lô I-3A, KCN Đông Nam, Long Thành, Đồng Nai',
        'description' => 'Thiết bị vệ sinh cao cấp, bồn cầu, lavabo, vòi sen',
        'category_id' => 4,
        'logo' => 'https://inax.com.vn/images/logo.png'
    ],

    // Sàn và tường (category_id = 5)
    [
        'name' => 'CÔNG TY TNHH SÀN GỖ VIỆT ĐỨC',
        'slug' => 'san-go-viet-duc',
        'email' => 'sales@vietducfloor.com',
        'phone' => '024.3768.5555',
        'address' => 'Km 28, Quốc lộ 5, Huyện Gia Lâm, Hà Nội',
        'description' => 'Sàn gỗ tự nhiên, sàn gỗ công nghiệp, sàn laminate',
        'category_id' => 5,
        'logo' => 'https://vietducfloor.com/logo.jpg'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN THẢM TRANG TRÍ ĐÔNG DƯƠNG',
        'slug' => 'tham-dong-duong',
        'email' => 'info@dongduongcarpet.vn',
        'phone' => '028.3715.2222',
        'address' => 'Số 67 Nam Kỳ Khởi Nghĩa, Q.3, TP.HCM',
        'description' => 'Thảm trải sàn, thảm trang trí, thảm văn phòng cao cấp',
        'category_id' => 5,
        'logo' => 'https://dongduongcarpet.vn/images/logo.png'
    ],
    [
        'name' => 'CÔNG TY TNHH GIẤY DÁN TƯỜNG KOREA',
        'slug' => 'giay-dan-tuong-korea',
        'email' => 'contact@koreawallpaper.vn',
        'phone' => '028.3927.7777',
        'address' => '456 Nguyễn Đình Chiểu, Q.3, TP.HCM',
        'description' => 'Giấy dán tường Hàn Quốc, giấy dán tường cao cấp',
        'category_id' => 5,
        'logo' => 'https://koreawallpaper.vn/logo.png'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN SÀN NHỰA VINYL PLUS',
        'slug' => 'san-nhua-vinyl-plus',
        'email' => 'info@vinylplus.vn',
        'phone' => '0236.3822.666',
        'address' => 'KCN Điện Nam - Điện Ngọc, Quảng Nam',
        'description' => 'Sàn nhựa vinyl, sàn SPC, sàn nhựa hèm khóa',
        'category_id' => 5,
        'logo' => 'https://vinylplus.vn/assets/logo.jpg'
    ],
    [
        'name' => 'CÔNG TY TNHH VẬT LIỆU XÂY DỰNG SAIGON',
        'slug' => 'vat-lieu-saigon',
        'email' => 'info@saigonmaterials.com',
        'phone' => '028.3974.8888',
        'address' => '123 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM',
        'description' => 'Phân phối vật liệu xây dựng, thiết bị công trình',
        'category_id' => 1,
        'logo' => 'https://saigonmaterials.com/logo.png'
    ],
    [
        'name' => 'CÔNG TY CỔ PHẦN KÍNH CƯỜNG LỰC VIỆT NAM',
        'slug' => 'kinh-cuong-luc-vietnam',
        'email' => 'sales@vietnamglass.vn',
        'phone' => '024.3562.9999',
        'address' => 'KCN Quang Minh, Mê Linh, Hà Nội',
        'description' => 'Kính cường lực, kính an toàn, mặt dựng kính',
        'category_id' => 1,
        'logo' => 'https://vietnamglass.vn/images/logo.png'
    ]
];

// Thêm suppliers vào database
echo "1. THÊM 20 NHÀ CUNG CẤP:\n";
$supplierIds = [];
foreach ($suppliers as $index => $supplier) {
    $stmt = $pdo->prepare("INSERT INTO suppliers (name, slug, email, phone, address, description, category_id, logo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([
        $supplier['name'],
        $supplier['slug'],
        $supplier['email'],
        $supplier['phone'],
        $supplier['address'],
        $supplier['description'],
        $supplier['category_id'],
        $supplier['logo']
    ]);
    $supplierId = $pdo->lastInsertId();
    $supplierIds[] = $supplierId;
    echo "  ✓ " . ($index + 1) . ". {$supplier['name']}\n";
}

echo "\n2. THÊM 50 SẢN PHẨM:\n";

// Danh sách 50 sản phẩm thực tế
$products = [
    // Xi măng (category_id = 10 - Xi măng)
    ['name' => 'Xi măng Hoàng Thạch PCB40', 'category_id' => 10, 'price' => 105000, 'supplier_idx' => 0],
    ['name' => 'Xi măng Portland PCB30', 'category_id' => 10, 'price' => 98000, 'supplier_idx' => 0],
    
    // Thép (category_id = 1 - Vật liệu chính)
    ['name' => 'Thép CB240-T Pomina Φ10', 'category_id' => 1, 'price' => 17500, 'supplier_idx' => 1],
    ['name' => 'Thép CB300-V Pomina Φ12', 'category_id' => 1, 'price' => 18200, 'supplier_idx' => 1],
    ['name' => 'Thép CB400-V Pomina Φ16', 'category_id' => 1, 'price' => 19800, 'supplier_idx' => 1],
    
    // Gạch (category_id = 6 - Gạch)
    ['name' => 'Gạch granite 60x60 Đồng Tâm 6060MOMENT001', 'category_id' => 6, 'price' => 185000, 'supplier_idx' => 2],
    ['name' => 'Gạch ốp tường 30x60 Đồng Tâm 3060STYLE005', 'category_id' => 6, 'price' => 125000, 'supplier_idx' => 2],
    ['name' => 'Gạch lát nền 80x80 Đồng Tâm 8080LUXURY002', 'category_id' => 6, 'price' => 285000, 'supplier_idx' => 2],
    ['name' => 'Gạch mosaic thủy tinh Đồng Tâm MS001', 'category_id' => 6, 'price' => 450000, 'supplier_idx' => 2],
    
    // Sơn (category_id = 5 - Sơn)
    ['name' => 'Sơn nước nội thất Hà Nội SPEC 18L', 'category_id' => 5, 'price' => 875000, 'supplier_idx' => 3],
    ['name' => 'Sơn dầu ngoại thất Hà Nội CLASSIC 18L', 'category_id' => 5, 'price' => 920000, 'supplier_idx' => 3],
    ['name' => 'Sơn chống thấm Hà Nội WATERPROOF 20kg', 'category_id' => 5, 'price' => 1250000, 'supplier_idx' => 3],
    ['name' => 'Sơn lót kháng kiềm Hà Nội PRIMER 18L', 'category_id' => 5, 'price' => 680000, 'supplier_idx' => 3],
    
    // Kính (category_id = 7 - Kính)
    ['name' => 'Kính cường lực 8mm Việt Nhật', 'category_id' => 7, 'price' => 380000, 'supplier_idx' => 4],
    ['name' => 'Kính phản quang 6mm Việt Nhật', 'category_id' => 7, 'price' => 320000, 'supplier_idx' => 4],
    ['name' => 'Kính Low-E tiết kiệm năng lượng 10mm', 'category_id' => 7, 'price' => 580000, 'supplier_idx' => 4],
    
    // Nội thất (category_id = 11 - Nội thất)
    ['name' => 'Tủ bếp Acrylic cao cấp HG-TB001', 'category_id' => 11, 'price' => 35000000, 'supplier_idx' => 5],
    ['name' => 'Tủ quần áo 4 cánh HG-TQA002', 'category_id' => 11, 'price' => 12500000, 'supplier_idx' => 5],
    ['name' => 'Bàn ăn gỗ sồi HG-BA003', 'category_id' => 11, 'price' => 8900000, 'supplier_idx' => 5],
    ['name' => 'Sofa góc da thật HG-SF004', 'category_id' => 11, 'price' => 18500000, 'supplier_idx' => 5],
    
    // Gỗ MDF/HDF (category_id = 11 - Nội thất)
    ['name' => 'Tấm MDF An Cường 17mm', 'category_id' => 11, 'price' => 485000, 'supplier_idx' => 6],
    ['name' => 'Tấm HDF An Cường 8mm', 'category_id' => 11, 'price' => 285000, 'supplier_idx' => 6],
    ['name' => 'Cửa gỗ công nghiệp An Cường AC-CG001', 'category_id' => 11, 'price' => 2450000, 'supplier_idx' => 6],
    
    // Tủ bếp (category_id = 11 - Nội thất)
    ['name' => 'Tủ bếp Acrylic trắng bóng ACR-TB001', 'category_id' => 11, 'price' => 28000000, 'supplier_idx' => 7],
    ['name' => 'Tủ bếp Laminate vân gỗ LAM-TB002', 'category_id' => 11, 'price' => 22000000, 'supplier_idx' => 7],
    ['name' => 'Mặt bàn bếp đá Granite tự nhiên', 'category_id' => 11, 'price' => 3800000, 'supplier_idx' => 7],
    
    // Nội thất Nitori (category_id = 11 - Nội thất)
    ['name' => 'Giường ngủ gỗ thông Nitori NI-GN001', 'category_id' => 11, 'price' => 4500000, 'supplier_idx' => 8],
    ['name' => 'Tủ đầu giường Nitori NI-TDG002', 'category_id' => 11, 'price' => 1200000, 'supplier_idx' => 8],
    ['name' => 'Bàn làm việc Nitori NI-BLV003', 'category_id' => 11, 'price' => 2800000, 'supplier_idx' => 8],
    
    // Cảnh quan (category_id = 4 - Cảnh quan)
    ['name' => 'Cây cảnh Bonsai Tùng La Hán', 'category_id' => 4, 'price' => 850000, 'supplier_idx' => 9],
    ['name' => 'Hệ thống tưới phun mưa tự động', 'category_id' => 4, 'price' => 2500000, 'supplier_idx' => 9],
    ['name' => 'Đèn sân vườn LED năng lượng mặt trời', 'category_id' => 4, 'price' => 650000, 'supplier_idx' => 9],
    
    // Cây giống (category_id = 4 - Cảnh quan)
    ['name' => 'Cây Kim Ngân cao 1.5m', 'category_id' => 4, 'price' => 1200000, 'supplier_idx' => 10],
    ['name' => 'Cây Phượng Vĩ giống cao 2m', 'category_id' => 4, 'price' => 680000, 'supplier_idx' => 10],
    ['name' => 'Hạt giống cỏ nhung Nhật', 'category_id' => 4, 'price' => 85000, 'supplier_idx' => 10],
    
    // Thiết bị điện (category_id = 12 - Thiết bị điện)
    ['name' => 'Máy lạnh trung tâm Đại Phát 50HP', 'category_id' => 12, 'price' => 185000000, 'supplier_idx' => 11],
    ['name' => 'Hệ thống điều hòa VRV Đại Phát', 'category_id' => 12, 'price' => 95000000, 'supplier_idx' => 11],
    ['name' => 'Tủ điện phân phối 3 pha DP-TD001', 'category_id' => 12, 'price' => 8500000, 'supplier_idx' => 11],
    
    // Ống nhựa (category_id = 12 - Thiết bị điện/nước)
    ['name' => 'Ống nhựa PVC Đông Á Φ114', 'category_id' => 12, 'price' => 125000, 'supplier_idx' => 12],
    ['name' => 'Ống HDPE Đông Á Φ90', 'category_id' => 12, 'price' => 95000, 'supplier_idx' => 12],
    ['name' => 'Phụ kiện ống nước Đông Á - Khớp nối', 'category_id' => 12, 'price' => 45000, 'supplier_idx' => 12],
    
    // Thiết bị vệ sinh (category_id = 13 - Thiết bị vệ sinh)
    ['name' => 'Bồn cầu 1 khối INAX AC-700VAN', 'category_id' => 13, 'price' => 4850000, 'supplier_idx' => 13],
    ['name' => 'Lavabo âm bàn INAX AL-300V', 'category_id' => 13, 'price' => 1250000, 'supplier_idx' => 13],
    ['name' => 'Vòi sen tắm đứng INAX BFV-10S', 'category_id' => 13, 'price' => 2800000, 'supplier_idx' => 13],
    ['name' => 'Gương soi phòng tắm INAX KF-4560VA', 'category_id' => 13, 'price' => 980000, 'supplier_idx' => 13],
    
    // Sàn gỗ (category_id = 8 - Sàn gỗ)
    ['name' => 'Sàn gỗ tự nhiên Sồi Việt Đức 15mm', 'category_id' => 8, 'price' => 680000, 'supplier_idx' => 14],
    ['name' => 'Sàn gỗ công nghiệp HDF Việt Đức 12mm', 'category_id' => 8, 'price' => 385000, 'supplier_idx' => 14],
    ['name' => 'Sàn Laminate chống nước Việt Đức 8mm', 'category_id' => 8, 'price' => 285000, 'supplier_idx' => 14],
    
    // Thảm (category_id = 8 - Sàn gỗ/thảm)
    ['name' => 'Thảm trải sàn len Đông Dương 2x3m', 'category_id' => 8, 'price' => 2500000, 'supplier_idx' => 15],
    ['name' => 'Thảm văn phòng chống cháy DD-VP001', 'category_id' => 8, 'price' => 450000, 'supplier_idx' => 15]
];

// Thêm sản phẩm vào database
foreach ($products as $index => $product) {
    $supplierId = $supplierIds[$product['supplier_idx']];
    $slug = strtolower(str_replace([' ', 'Φ'], ['-', 'phi'], $product['name']));
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, price, category_id, supplier_id, status, featured, description, brand, manufacturer, created_at) VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $product['name'],
        $slug,
        $product['price'],
        $product['category_id'],
        $supplierId,
        $index < 10 ? 1 : 0, // 10 sản phẩm đầu làm nổi bật
        'Sản phẩm chất lượng cao, đáp ứng tiêu chuẩn quốc gia',
        $suppliers[$product['supplier_idx']]['name'],
        $suppliers[$product['supplier_idx']]['name']
    ]);
    
    echo "  ✓ " . ($index + 1) . ". {$product['name']} - " . number_format($product['price']) . "đ\n";
}

echo "\n=== THỐNG KÊ CUỐI CÙNG ===\n";

// Thống kê nhà cung cấp
echo "\nNhà cung cấp theo danh mục:\n";
$result = $pdo->query("
    SELECT sc.name, COUNT(s.id) as count 
    FROM supplier_categories sc 
    LEFT JOIN suppliers s ON sc.id = s.category_id 
    GROUP BY sc.id, sc.name 
    ORDER BY sc.order_index
");
while ($row = $result->fetch()) {
    echo "  - {$row['name']}: {$row['count']} nhà cung cấp\n";
}

// Thống kê sản phẩm
echo "\nSản phẩm theo danh mục:\n";
$result = $pdo->query("
    SELECT 
        CASE 
            WHEN pc.parent_id IS NULL THEN pc.name
            ELSE CONCAT('  └── ', pc.name)
        END as category_name,
        COUNT(p.id) as count 
    FROM product_categories pc 
    LEFT JOIN products p ON pc.id = p.category_id 
    GROUP BY pc.id, pc.name, pc.parent_id
    ORDER BY pc.parent_id IS NULL DESC, pc.parent_id, pc.name
");
while ($row = $result->fetch()) {
    echo "  {$row['category_name']}: {$row['count']} sản phẩm\n";
}

echo "\n✅ HOÀN THÀNH!\n";
echo "🌐 Truy cập: http://localhost:8080/vnmt/backend/products.php\n";
echo "🌐 Truy cập: http://localhost:8080/vnmt/backend/suppliers.php\n";
?>