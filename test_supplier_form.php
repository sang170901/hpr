<?php 
include 'inc/header-new.php';
require_once 'inc/db_frontend.php';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>POST data received:\n";
    print_r($_POST);
    echo "</pre>";
    
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
                'status' => 0 // Pending approval
            ];
            
            echo "<pre>Data to insert:\n";
            print_r($data);
            echo "</pre>";
            
            // Insert into database
            $fields = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            echo "<pre>SQL Query:\n";
            echo "INSERT INTO suppliers ($fields) VALUES ($placeholders)\n";
            echo "</pre>";
            
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
        echo "<pre>Exception:\n";
        echo $e->getMessage() . "\n";
        echo $e->getTraceAsString();
        echo "</pre>";
    }
}

echo "<h3>Debug Form Test</h3>";
echo "<p>Để test form, hãy truy cập: <a href='supplier-register.php'>supplier-register.php</a></p>";

include 'inc/footer-new.php';
?>