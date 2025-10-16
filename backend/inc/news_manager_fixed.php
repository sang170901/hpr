<?php
/*
NewsManager - Quản lý tin tức cho trang web
*/

class NewsManager {
    
    // Constructor
    public function __construct() {
        // Initialize if needed
    }
    
    // Lấy tất cả tin tức với filter
    public function getNews($category = '', $search = '') {
        $news = $this->getSampleNews();
        
        // Filter by category
        if (!empty($category)) {
            $news = array_filter($news, function($item) use ($category) {
                return $item['category'] === $category;
            });
        }
        
        // Filter by search
        if (!empty($search)) {
            $news = array_filter($news, function($item) use ($search) {
                return stripos($item['title'], $search) !== false || 
                       stripos($item['content'], $search) !== false ||
                       stripos($item['excerpt'], $search) !== false;
            });
        }
        
        return array_values($news);
    }
    
    // Lấy danh sách categories
    public function getCategories() {
        $news = $this->getSampleNews();
        $categories = array_unique(array_column($news, 'category'));
        return array_values($categories);
    }
    
    // Lấy tin tức theo slug
    public function getNewsBySlug($slug) {
        $news = $this->getSampleNews();
        foreach ($news as $item) {
            if ($item['slug'] === $slug) {
                return $item;
            }
        }
        return null;
    }
    
    // Lấy tin tức nổi bật
    public function getFeaturedNews() {
        $news = $this->getSampleNews();
        return array_filter($news, function($item) {
            return $item['featured'] === true;
        });
    }
    
    // Template dữ liệu mẫu cho tin tức
    public function getSampleNews() {
        return [
            [
                'id' => 1,
                'title' => 'Công Nghệ AI Trong Sản Xuất Vật Liệu Xây Dựng - Tương Lai Đã Đến',
                'slug' => 'cong-nghe-ai-trong-san-xuat-vat-lieu',
                'excerpt' => 'Khám phá cách trí tuệ nhân tạo đang cách mạng hóa ngành sản xuất vật liệu xây dựng, từ tối ưu hóa quy trình sản xuất đến phát triển vật liệu thông minh mới. Công nghệ này không chỉ giúp cải thiện chất lượng sản phẩm mà còn giảm thiểu tác động môi trường và chi phí sản xuất.',
                'content' => 'Nội dung chi tiết của bài viết về AI trong xây dựng...',
                'category' => 'Công Nghệ',
                'author' => 'Nguyễn Đức Anh',
                'featured_image' => 'assets/images/news/ai-construction.jpg',
                'tags' => ['AI', 'Trí tuệ nhân tạo', 'Vật liệu xây dựng', 'Công nghệ 4.0'],
                'published_date' => '2025-10-15',
                'reading_time' => 5,
                'views' => 1234,
                'status' => 'published',
                'featured' => true
            ],
            [
                'id' => 2,
                'title' => 'Gạch Sinh Thái Từ Phế Thải Nông Nghiệp - Giải Pháp Bền Vững',
                'slug' => 'gach-sinh-thai-tu-phe-thai-nong-nghiep',
                'excerpt' => 'Công nghệ mới cho phép sản xuất gạch xây dựng từ rơm rạ và phế thải nông nghiệp, không chỉ giải quyết vấn đề môi trường mà còn tạo ra sản phẩm bền vững với chất lượng cao. Đây là bước đột phá trong ngành vật liệu xây dựng thân thiện môi trường.',
                'content' => 'Nội dung chi tiết về gạch sinh thái...',
                'category' => 'Vật Liệu',
                'author' => 'Trần Minh Châu',
                'featured_image' => 'assets/images/news/eco-brick.jpg',
                'tags' => ['Sinh thái', 'Bền vững', 'Gạch', 'Môi trường'],
                'published_date' => '2025-10-12',
                'reading_time' => 4,
                'views' => 856,
                'status' => 'published',
                'featured' => false
            ],
            [
                'id' => 3,
                'title' => 'Biến Động Giá Vật Liệu Xây Dựng Quý 4/2025',
                'slug' => 'bien-dong-gia-vat-lieu-quy-4-2025',
                'excerpt' => 'Tổng quan về biến động giá cả các loại vật liệu xây dựng chính trong quý 4, xu hướng và dự báo cho những tháng tới. Những yếu tố ảnh hưởng đến thị trường bao gồm giá nguyên liệu thô, chi phí vận chuyển và chính sách của chính phủ.',
                'content' => 'Phân tích chi tiết về thị trường vật liệu...',
                'category' => 'Thị Trường',
                'author' => 'Lê Văn Phúc',
                'featured_image' => 'assets/images/news/market-price.jpg',
                'tags' => ['Giá cả', 'Thị trường', 'Vật liệu', 'Dự báo'],
                'published_date' => '2025-10-10',
                'reading_time' => 6,
                'views' => 1450,
                'status' => 'published',
                'featured' => false
            ],
            [
                'id' => 4,
                'title' => 'Hướng Dẫn Chọn Xi Măng Phù Hợp Cho Từng Công Trình',
                'slug' => 'huong-dan-chon-xi-mang-phu-hop',
                'excerpt' => 'Hướng dẫn chi tiết cách lựa chọn loại xi măng phù hợp cho từng loại công trình, từ nhà ở dân dụng đến các dự án hạ tầng lớn. Những tiêu chí quan trọng cần lưu ý bao gồm cường độ, thời gian đông kết và khả năng chịu các tác động môi trường.',
                'content' => 'Hướng dẫn chi tiết về xi măng...',
                'category' => 'Hướng Dẫn',
                'author' => 'Phạm Thị Mai',
                'featured_image' => 'assets/images/news/cement-guide.jpg',
                'tags' => ['Xi măng', 'Hướng dẫn', 'Lựa chọn', 'Công trình'],
                'published_date' => '2025-10-08',
                'reading_time' => 7,
                'views' => 2100,
                'status' => 'published',
                'featured' => false
            ],
            [
                'id' => 5,
                'title' => 'Xu Hướng Thiết Kế Bền Vững 2025 - Tương Lai Xanh',
                'slug' => 'xu-huong-thiet-ke-ben-vung-2025',
                'excerpt' => 'Những xu hướng thiết kế và vật liệu bền vững đang định hình ngành xây dựng năm 2025. Từ vật liệu tái chế đến công nghệ xanh, khám phá tương lai của kiến trúc thân thiện môi trường và các giải pháp sáng tạo cho xây dựng bền vững.',
                'content' => 'Phân tích xu hướng thiết kế bền vững...',
                'category' => 'Xu Hướng',
                'author' => 'Hoàng Minh Tuấn',
                'featured_image' => 'assets/images/news/sustainable-design.jpg',
                'tags' => ['Bền vững', 'Thiết kế', 'Xu hướng', 'Môi trường'],
                'published_date' => '2025-10-05',
                'reading_time' => 8,
                'views' => 1876,
                'status' => 'published',
                'featured' => false
            ],
            [
                'id' => 6,
                'title' => 'In 3D Trong Xây Dựng - Công Nghệ Hiện Thực Hóa Ước Mơ',
                'slug' => 'in-3d-trong-xay-dung',
                'excerpt' => 'Công nghệ in 3D đang mở ra những khả năng mới trong xây dựng, từ in nhà hoàn chỉnh đến sản xuất các chi tiết kiến trúc phức tạp. Tìm hiểu về tiềm năng và thách thức của công nghệ này trong việc cách mạng hóa ngành xây dựng.',
                'content' => 'Nội dung về công nghệ in 3D...',
                'category' => 'Công Nghệ',
                'author' => 'Ngô Thanh Sơn',
                'featured_image' => 'assets/images/news/3d-printing.jpg',
                'tags' => ['In 3D', 'Công nghệ', 'Xây dựng', 'Đổi mới'],
                'published_date' => '2025-10-03',
                'reading_time' => 6,
                'views' => 1120,
                'status' => 'published',
                'featured' => false
            ]
        ];
    }
    
    // Utility function to format date
    public function formatDate($date) {
        return date('d/m/Y', strtotime($date));
    }
    
    // Get related articles
    public function getRelatedNews($currentSlug, $limit = 3) {
        $allNews = $this->getSampleNews();
        $related = array_filter($allNews, function($item) use ($currentSlug) {
            return $item['slug'] !== $currentSlug;
        });
        return array_slice($related, 0, $limit);
    }
}
?>