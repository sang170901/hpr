<?php 
include 'inc/header-new.php';
require_once 'inc/db_frontend.php';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getFrontendPDO();
        
        // Validate required fields
        $required_fields = ['company_name', 'email', 'phone', 'address', 'description'];
        $errors = [];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "Vui lòng điền đầy đủ thông tin " . $field;
            }
        }
        
        if (empty($errors)) {
            // Generate slug from company name
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['company_name'])));
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
            
            // Ensure slug is unique
            $originalSlug = $slug;
            $counter = 1;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM suppliers WHERE slug = ?");
            while (true) {
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn() == 0) break;
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            // Handle file uploads
            $logoPath = '';
            if (!empty($_FILES['logo']['name'])) {
                $uploadDir = 'assets/images/suppliers/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $logoPath = $uploadDir . uniqid() . '_' . $_FILES['logo']['name'];
                move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath);
            }
            
            // Prepare data for insertion
            $data = [
                'name' => $_POST['company_name'],
                'slug' => $slug,
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'website' => $_POST['website'] ?? '',
                'address' => $_POST['address'],
                'city' => $_POST['city'] ?? '',
                'province' => $_POST['province'] ?? '',
                'description' => $_POST['description'],
                'category_id' => $_POST['category_id'] ?? null,
                'specialties' => $_POST['specialties'] ?? '',
                'logo' => $logoPath,
                'status' => 0, // Pending approval
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert into database
            $fields = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            $stmt = $pdo->prepare("INSERT INTO suppliers ($fields) VALUES ($placeholders)");
            $stmt->execute($data);
            
            $message = 'Đăng ký thành công! Thông tin của bạn đang được xem xét và sẽ được duyệt trong 24-48 giờ.';
            $messageType = 'success';
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Có lỗi xảy ra: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get categories for dropdown - use product categories
try {
    $pdo = getFrontendPDO();
    $categoriesStmt = $pdo->query("SELECT DISTINCT category as name, category as id FROM products WHERE category IS NOT NULL ORDER BY category ASC");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [
        ['id' => 'vật liệu', 'name' => 'Vật liệu'],
        ['id' => 'thiết bị', 'name' => 'Thiết bị'],
        ['id' => 'công nghệ', 'name' => 'Công nghệ'],
        ['id' => 'cảnh quan', 'name' => 'Cảnh quan']
    ];
}
?>

<style>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700;800&display=swap');

/* Registration Page Styles */
.registration-page {
    min-height: 100vh;
    background: #f8fcff;
    padding-top: 10px;
}

.registration-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-header {
    background: linear-gradient(135deg, #f8faff 0%, #e8f4fd 100%);
    color: #2d3748;
    padding: 60px 0 40px;
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="1" fill="%23e2e8f0" opacity="0.4"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
    opacity: 0.6;
}

.page-title {
    font-family: 'Nunito Sans', 'Open Sans', sans-serif;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 700;
    margin: 0 0 16px 0;
    text-align: center;
    letter-spacing: -0.02em;
    position: relative;
    z-index: 2;
}

.page-subtitle {
    font-size: clamp(0.95rem, 1.5vw, 1.125rem);
    font-weight: 400;
    opacity: 0.85;
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
    position: relative;
    z-index: 2;
}

.breadcrumb {
    margin-bottom: 32px;
    font-size: 14px;
    color: #64748b;
}

.breadcrumb-item {
    display: inline-block;
    position: relative;
}

.breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin: 0 8px;
    color: #cbd5e1;
}

.breadcrumb-item a {
    color: #4da6ff;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #2f93f8;
}

.breadcrumb-item.active {
    color: #374151;
    font-weight: 500;
}

.registration-form {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    margin-bottom: 40px;
}

.message {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 500;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.form-section {
    margin-bottom: 32px;
}

.form-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e2e8f0;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
    font-size: 14px;
}

.form-label.required::after {
    content: ' *';
    color: #e53e3e;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    transition: border-color 0.2s ease;
    background: #fff;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #4da6ff;
    box-shadow: 0 0 0 3px rgba(77, 166, 255, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.file-upload {
    position: relative;
    display: inline-block;
    width: 100%;
}

.file-upload input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-upload-label {
    display: block;
    padding: 12px 16px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8fafc;
}

.file-upload-label:hover {
    border-color: #4da6ff;
    background: #f0f9ff;
}

.submit-btn {
    background: linear-gradient(135deg, #4da6ff 0%, #2f93f8 100%);
    color: white;
    border: none;
    padding: 16px 32px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 20px;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(77, 166, 255, 0.4);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .registration-form {
        padding: 24px;
    }
    
    .registration-container {
        padding: 0 16px;
    }
}
</style>

<div class="registration-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="registration-container">
            <h1 class="page-title">ĐĂNG KÝ NHÀ CUNG CẤP</h1>
            <p class="page-subtitle">
                Tham gia cùng chúng tôi để mở rộng thị trường và kết nối với nhiều khách hàng hơn.<br>
                Đăng ký thông tin doanh nghiệp để trở thành đối tác chiến lược.
            </p>
        </div>
    </div>

    <div class="registration-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <span class="breadcrumb-item"><a href="/vnmt/">Trang chủ</a></span>
            <span class="breadcrumb-item"><a href="/vnmt/suppliers.php">Nhà cung cấp</a></span>
            <span class="breadcrumb-item active">Đăng ký</span>
        </nav>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form class="registration-form" method="POST" enctype="multipart/form-data">
            <!-- Company Information -->
            <div class="form-section">
                <h3><i class="fas fa-building"></i> Thông tin doanh nghiệp</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Tên công ty</label>
                        <input type="text" name="company_name" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-select">
                            <option value="">Chọn danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Mô tả doanh nghiệp</label>
                    <textarea name="description" class="form-textarea" required 
                              placeholder="Giới thiệu về công ty, lĩnh vực hoạt động, thế mạnh..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Chuyên môn</label>
                        <input type="text" name="specialties" class="form-input" 
                               placeholder="VD: Sàn gỗ, Vật liệu chống thấm..."
                               value="<?php echo htmlspecialchars($_POST['specialties'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-input" 
                               placeholder="https://..."
                               value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3><i class="fas fa-phone"></i> Thông tin liên hệ</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-input" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Số điện thoại</label>
                        <input type="tel" name="phone" class="form-input" required
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="form-section">
                <h3><i class="fas fa-map-marker-alt"></i> Địa chỉ</h3>
                
                <div class="form-group">
                    <label class="form-label required">Địa chỉ</label>
                    <textarea name="address" class="form-textarea" required 
                              placeholder="Số nhà, tên đường, phường/xã"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Thành phố</label>
                        <input type="text" name="city" class="form-input" 
                               placeholder="VD: Hồ Chí Minh"
                               value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tỉnh/Thành phố</label>
                        <input type="text" name="province" class="form-input" 
                               placeholder="VD: TP. Hồ Chí Minh"
                               value="<?php echo htmlspecialchars($_POST['province'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="form-section">
                <h3><i class="fas fa-image"></i> Logo công ty</h3>
                
                <div class="form-group">
                    <label class="form-label">Logo (JPG, PNG tối đa 2MB)</label>
                    <div class="file-upload">
                        <input type="file" name="logo" accept="image/*">
                        <label class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i><br>
                            Chọn file logo hoặc kéo thả vào đây
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Gửi đăng ký
            </button>
        </form>
    </div>
</div>

<?php include 'inc/footer-new.php'; ?>