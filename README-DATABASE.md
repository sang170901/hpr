# 📊 Hướng dẫn sử dụng Database

## 📁 File Database

File `vnmt_database.sql` chứa toàn bộ cấu trúc và dữ liệu của database.

## 🗃️ Các bảng trong database:

### 1. **users** - Quản lý người dùng
- Thông tin tài khoản (username, email, password)
- Hỗ trợ OAuth (Google, Facebook)
- Avatar và thông tin cá nhân

### 2. **posts** - Quản lý bài viết
- Tiêu đề, nội dung, excerpt
- Featured image
- Category, status (draft/published)
- Lượt xem, ngày xuất bản

### 3. **comments** - Bình luận bài viết
- Link với posts và users
- Trạng thái (pending/approved/spam)
- Thông tin người bình luận

### 4. **sliders** - Banner/Slider trang chủ
- Tiêu đề, mô tả, hình ảnh
- Link và text link
- Thứ tự hiển thị, thời gian active

### 5. Các bảng khác
- `partners` - Đối tác
- `suppliers` - Nhà cung cấp
- `products` - Sản phẩm
- `categories` - Danh mục
- `vouchers` - Mã giảm giá

---

## 🚀 Import Database

### Trên localhost (XAMPP):
```bash
# Cách 1: Dùng phpMyAdmin
1. Mở http://localhost/phpmyadmin
2. Tạo database mới (hoặc chọn database có sẵn)
3. Click "Import" → Chọn file "vnmt_database.sql"
4. Click "Go"

# Cách 2: Dùng command line
mysql -u root -p vnmt_db < vnmt_database.sql
```

### Trên hosting (cPanel):
```bash
1. Vào phpMyAdmin trên cPanel
2. Tạo database mới
3. Import file vnmt_database.sql
4. Cập nhật thông tin database trong backend/inc/db.php
```

### Trên VPS/Server:
```bash
# Upload file SQL lên server
scp vnmt_database.sql user@server:/tmp/

# SSH vào server và import
ssh user@server
mysql -u username -p database_name < /tmp/vnmt_database.sql
```

---

## ⚙️ Cấu hình kết nối

Sau khi import, cập nhật file `backend/inc/db.php`:

```php
<?php
function getPDO() {
    $host = 'localhost';           // Hoặc IP server database
    $dbname = 'vnmt_db';           // Tên database của bạn
    $username = 'root';            // Username database
    $password = '';                // Password database (production phải có)
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
```

---

## 👥 Tài khoản test có sẵn

Database đã có sẵn các tài khoản test (password: **123456**):

| Username | Email | Role |
|----------|-------|------|
| admin | admin@vnmt.local | admin |
| nguyenvana | nguyenvana@example.com | user |
| tranthib | tranthib@example.com | user |
| leminhhung | leminhhung@example.com | user |

---

## 🔄 Cập nhật Database (Update Schema)

Nếu cần thêm bảng hoặc cột mới:

```sql
-- Ví dụ: Thêm cột mới vào bảng users
ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email;

-- Tạo bảng mới
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 💾 Backup Database

### Tự động backup trên server:

Tạo cron job (chạy hàng ngày lúc 2:00 AM):
```bash
0 2 * * * mysqldump -u username -p'password' vnmt_db > /backup/vnmt_$(date +\%Y\%m\%d).sql
```

### Manual backup:
```bash
# Export toàn bộ database
mysqldump -u root -p vnmt_db > backup_$(date +%Y%m%d).sql

# Chỉ export cấu trúc (không có data)
mysqldump -u root -p --no-data vnmt_db > structure_only.sql

# Chỉ export data (không có cấu trúc)
mysqldump -u root -p --no-create-info vnmt_db > data_only.sql
```

---

## 🔒 Bảo mật

**⚠️ LƯU Ý QUAN TRỌNG:**

1. **Production**: Đổi tất cả mật khẩu mặc định
2. **Không commit** `backend/inc/db.php` với password thật vào Git
3. **Sử dụng .env** file cho production
4. **Giới hạn quyền** database user (không dùng root)
5. **Backup thường xuyên** và lưu ở nơi an toàn

---

## 📈 Thống kê Database

- **Tổng số bảng**: ~15 bảng
- **Dữ liệu mẫu**: 
  - 4 users
  - 20 posts
  - 12 comments
  - 5 sliders
  - 50+ products
  - 30+ suppliers

---

## 🆘 Troubleshooting

### Lỗi "Access denied"
```bash
# Kiểm tra user và password
mysql -u root -p

# Tạo user mới và cấp quyền
CREATE USER 'vnmt_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON vnmt_db.* TO 'vnmt_user'@'localhost';
FLUSH PRIVILEGES;
```

### Lỗi "Table doesn't exist"
```bash
# Kiểm tra các bảng hiện có
SHOW TABLES;

# Import lại database
mysql -u root -p vnmt_db < vnmt_database.sql
```

### Lỗi encoding (tiếng Việt bị lỗi)
```sql
-- Đổi charset của database
ALTER DATABASE vnmt_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Đổi charset của từng bảng
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 📞 Hỗ trợ

Nếu gặp vấn đề với database, kiểm tra:
1. File log lỗi của MySQL/MariaDB
2. Quyền truy cập của user database
3. Phiên bản MySQL/MariaDB (khuyến nghị >= 5.7)

