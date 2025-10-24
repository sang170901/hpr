<?php
/**
 * Thêm 10 bài viết mới về vật liệu xây dựng
 */

require_once __DIR__ . '/../inc/db.php';

try {
    $pdo = getPDO();
    
    echo "<h2>📝 Thêm 10 Bài Viết Mới</h2>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:green;font-weight:600;} .info{background:#e0f2fe;padding:12px;border-radius:8px;margin:10px 0;border-left:4px solid #38bdf8;} .error{color:red;font-weight:600;}</style>";
    
    // Kiểm tra số bài viết hiện có
    $currentCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    echo "<p class='info'>📊 Hiện có: <strong>$currentCount bài viết</strong></p>";
    
    // 10 bài viết mới với nội dung chất lượng
    $newPosts = [
        [
            'title' => 'Xu Hướng Vật Liệu Xây Dựng Bền Vững Năm 2025',
            'slug' => 'xu-huong-vat-lieu-xay-dung-ben-vung-2025',
            'excerpt' => 'Khám phá những vật liệu xanh và công nghệ mới đang định hình ngành xây dựng hiện đại.',
            'content' => 'Năm 2025 chứng kiến sự lên ngôi của các vật liệu thân thiện môi trường như bê tông xanh, gỗ tái chế, và vật liệu cách nhiệt hiệu quả. Các công trình không chỉ chú trọng đến tính thẩm mỹ mà còn ưu tiên khả năng tiết kiệm năng lượng và giảm thiểu khí thải carbon. Việc áp dụng các tiêu chuẩn xây dựng xanh đang trở thành xu thế tất yếu, mang lại lợi ích lâu dài cho cả môi trường và người sử dụng.',
            'featured_image' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=800',
            'category' => 'Xu hướng',
            'tags' => 'vật liệu xanh,bền vững,công nghệ xây dựng',
            'status' => 'published'
        ],
        [
            'title' => 'Công Nghệ AI Trong Quản Lý Dự Án Xây Dựng',
            'slug' => 'cong-nghe-ai-quan-ly-du-an-xay-dung',
            'excerpt' => 'Trí tuệ nhân tạo đang cách mạng hóa cách quản lý và thực thi các dự án xây dựng.',
            'content' => 'Trí tuệ nhân tạo (AI) đang dần trở thành công cụ không thể thiếu trong quản lý dự án xây dựng. Từ việc tối ưu hóa lịch trình, dự đoán rủi ro, đến tự động hóa các quy trình kiểm tra chất lượng, AI giúp các nhà quản lý đưa ra quyết định chính xác và hiệu quả hơn. Các hệ thống AI có thể phân tích lượng lớn dữ liệu từ các dự án trước, học hỏi và đưa ra các khuyến nghị giúp giảm chi phí và thời gian thi công.',
            'featured_image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?q=80&w=800',
            'category' => 'Công nghệ',
            'tags' => 'AI,quản lý dự án,xây dựng thông minh',
            'status' => 'published'
        ],
        [
            'title' => 'Giải Pháp Cách Âm Hiệu Quả Cho Chung Cư',
            'slug' => 'giai-phap-cach-am-hieu-qua-chung-cu',
            'excerpt' => 'Tạo không gian sống yên tĩnh và thoải mái trong các tòa nhà cao tầng.',
            'content' => 'Tiếng ồn là một vấn đề lớn trong các khu chung cư hiện đại. Bài viết này sẽ giới thiệu các giải pháp cách âm tiên tiến, từ vật liệu cách âm chuyên dụng như bông khoáng, tấm thạch cao cách âm, đến các kỹ thuật thi công cửa và tường chống ồn. Việc đầu tư vào cách âm không chỉ nâng cao chất lượng cuộc sống mà còn tăng giá trị cho bất động sản.',
            'featured_image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=800',
            'category' => 'Vật liệu',
            'tags' => 'cách âm,chung cư,vật liệu xây dựng',
            'status' => 'published'
        ],
        [
            'title' => 'Thiết Kế Cảnh Quan Xanh Cho Đô Thị Bền Vững',
            'slug' => 'thiet-ke-canh-quan-xanh-do-thi-ben-vung',
            'excerpt' => 'Tạo không gian sống hài hòa với thiên nhiên, cải thiện chất lượng không khí đô thị.',
            'content' => 'Thiết kế cảnh quan xanh không chỉ mang lại vẻ đẹp cho đô thị mà còn đóng vai trò quan trọng trong việc cải thiện môi trường sống. Các giải pháp như vườn trên mái, tường xanh, công viên mini và hệ thống thoát nước tự nhiên giúp giảm nhiệt độ đô thị, tăng cường đa dạng sinh học và tạo ra không gian thư giãn cho cư dân. Đây là một phần không thể thiếu trong quy hoạch đô thị bền vững.',
            'featured_image' => 'https://images.unsplash.com/photo-1558904541-efa843a96f01?q=80&w=800',
            'category' => 'Cảnh quan',
            'tags' => 'cảnh quan,đô thị xanh,bền vững',
            'status' => 'published'
        ],
        [
            'title' => 'Bê Tông Tự Lèn (SCC) - Công Nghệ Tiên Tiến',
            'slug' => 'be-tong-tu-len-cong-nghe-tien-tien',
            'excerpt' => 'Ưu điểm và ứng dụng của bê tông tự lèn trong các công trình phức tạp.',
            'content' => 'Bê tông tự lèn (Self-Compacting Concrete - SCC) là loại bê tông có khả năng tự chảy và lèn chặt dưới trọng lượng của chính nó mà không cần rung đầm. Điều này giúp giảm tiếng ồn, tăng tốc độ thi công và cải thiện chất lượng bề mặt. SCC đặc biệt hữu ích trong các cấu kiện có hình dạng phức tạp hoặc mật độ cốt thép dày đặc, nơi việc đầm thủ công gặp nhiều khó khăn.',
            'featured_image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800',
            'category' => 'Kỹ thuật',
            'tags' => 'bê tông,SCC,kỹ thuật xây dựng',
            'status' => 'published'
        ],
        [
            'title' => 'Vật Liệu Composite Trong Kiến Trúc Hiện Đại',
            'slug' => 'vat-lieu-composite-kien-truc-hien-dai',
            'excerpt' => 'Sự kết hợp hoàn hảo giữa độ bền, nhẹ và tính linh hoạt trong thiết kế.',
            'content' => 'Vật liệu composite đang ngày càng được ưa chuộng trong kiến trúc hiện đại nhờ vào những ưu điểm vượt trội như độ bền cao, trọng lượng nhẹ, khả năng chống ăn mòn và dễ dàng tạo hình. Chúng được ứng dụng rộng rãi trong các cấu trúc mái, mặt dựng, và các chi tiết trang trí phức tạp, mang lại vẻ đẹp độc đáo và bền vững cho công trình.',
            'featured_image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?q=80&w=800',
            'category' => 'Vật liệu',
            'tags' => 'composite,kiến trúc,vật liệu mới',
            'status' => 'published'
        ],
        [
            'title' => 'Tối Ưu Hóa Chi Phí Xây Dựng Hiệu Quả',
            'slug' => 'toi-uu-hoa-chi-phi-xay-dung-hieu-qua',
            'excerpt' => 'Các chiến lược giúp giảm thiểu chi phí mà vẫn đảm bảo chất lượng công trình.',
            'content' => 'Việc quản lý chi phí hiệu quả là yếu tố then chốt cho sự thành công của mọi dự án xây dựng. Bài viết này sẽ đi sâu vào các chiến lược như tối ưu hóa thiết kế, lựa chọn vật liệu phù hợp, áp dụng công nghệ BIM (Building Information Modeling) và quản lý chuỗi cung ứng chặt chẽ để giảm thiểu lãng phí và tối đa hóa lợi nhuận.',
            'featured_image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=800',
            'category' => 'Quản lý',
            'tags' => 'chi phí,BIM,quản lý xây dựng',
            'status' => 'published'
        ],
        [
            'title' => 'An Toàn Lao Động Trên Công Trường',
            'slug' => 'an-toan-lao-dong-tren-cong-truong',
            'excerpt' => 'Những quy tắc vàng đảm bảo môi trường làm việc an toàn cho công nhân.',
            'content' => 'An toàn lao động không chỉ là trách nhiệm pháp lý mà còn là yếu tố quan trọng để bảo vệ sức khỏe và tính mạng của công nhân. Bài viết này tổng hợp các quy tắc vàng về an toàn trên công trường, bao gồm việc sử dụng thiết bị bảo hộ cá nhân, kiểm tra máy móc định kỳ, đào tạo an toàn và thiết lập các quy trình khẩn cấp rõ ràng.',
            'featured_image' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=800',
            'category' => 'An toàn',
            'tags' => 'an toàn,lao động,công trường',
            'status' => 'published'
        ],
        [
            'title' => 'Vật Liệu Hoàn Thiện Cao Cấp Cho Biệt Thự',
            'slug' => 'vat-lieu-hoan-thien-cao-cap-biet-thu',
            'excerpt' => 'Lựa chọn vật liệu đẳng cấp để tạo nên không gian sống sang trọng và tinh tế.',
            'content' => 'Nội thất biệt thự đòi hỏi sự tinh tế và sang trọng trong từng chi tiết. Bài viết này giới thiệu các vật liệu hoàn thiện cao cấp như đá tự nhiên, gỗ óc chó, kính cường lực, và các loại vải bọc nội thất nhập khẩu. Việc kết hợp hài hòa các vật liệu này sẽ tạo nên một không gian sống đẳng cấp, phản ánh phong cách và cá tính của gia chủ.',
            'featured_image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=800',
            'category' => 'Nội thất',
            'tags' => 'nội thất,biệt thự,vật liệu cao cấp',
            'status' => 'published'
        ],
        [
            'title' => 'In 3D Trong Xây Dựng - Tương Lai Đã Đến',
            'slug' => 'in-3d-trong-xay-dung-tuong-lai',
            'excerpt' => 'Công nghệ in 3D đang mở ra kỷ nguyên mới cho việc xây dựng nhà ở.',
            'content' => 'Công nghệ in 3D đang dần trở thành một giải pháp đột phá trong ngành xây dựng, đặc biệt là trong việc xây dựng nhà ở. Với khả năng tạo ra các cấu trúc phức tạp một cách nhanh chóng và chính xác, in 3D giúp giảm thiểu thời gian thi công, tiết kiệm chi phí nhân công và vật liệu. Đây là một công nghệ đầy hứa hẹn cho tương lai của ngành xây dựng.',
            'featured_image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?q=80&w=800',
            'category' => 'Công nghệ',
            'tags' => 'in 3D,nhà ở,công nghệ xây dựng',
            'status' => 'published'
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO posts (title, slug, excerpt, content, featured_image, category, tags, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $added = 0;
    $skipped = 0;
    
    foreach ($newPosts as $post) {
        try {
            $stmt->execute([
                $post['title'],
                $post['slug'],
                $post['excerpt'],
                $post['content'],
                $post['featured_image'],
                $post['category'],
                $post['tags'],
                $post['status']
            ]);
            echo "<p class='success'>✓ Đã thêm: <strong>" . htmlspecialchars($post['title']) . "</strong></p>";
            $added++;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                echo "<p style='color:#f59e0b;'>⚠ Đã tồn tại: " . htmlspecialchars($post['title']) . "</p>";
                $skipped++;
            } else {
                echo "<p class='error'>✗ Lỗi: " . htmlspecialchars($post['title']) . " - " . $e->getMessage() . "</p>";
            }
        }
    }
    
    $newTotal = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    
    echo "<hr>";
    echo "<div class='info' style='background:#dcfce7;border-color:#10b981;'>";
    echo "<h3 style='margin:0 0 10px;color:#059669;'>✅ Hoàn thành!</h3>";
    echo "<p><strong>Đã thêm:</strong> $added bài viết</p>";
    echo "<p><strong>Đã bỏ qua:</strong> $skipped bài (trùng lặp)</p>";
    echo "<p><strong>Tổng cộng:</strong> $newTotal bài viết</p>";
    echo "</div>";
    
    echo "<p style='text-align:center;margin-top:30px;'>";
    echo "<a href='../../news.php' style='display:inline-block;padding:15px 40px;background:#10b981;color:white;text-decoration:none;border-radius:10px;font-weight:bold;font-size:1.1em;margin:10px;'>📰 Xem Trang Tin Tức</a>";
    echo "<a href='../../check_posts.php' style='display:inline-block;padding:15px 40px;background:#38bdf8;color:white;text-decoration:none;border-radius:10px;font-weight:bold;font-size:1.1em;margin:10px;'>🔍 Kiểm Tra Database</a>";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Lỗi nghiêm trọng: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit(1);
}
?>
