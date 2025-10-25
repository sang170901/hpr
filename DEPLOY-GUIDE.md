# 🚀 Hướng dẫn Deploy lên Production

## 📋 Chuẩn bị trước khi deploy

### 1. Kiểm tra các file quan trọng
- ✅ Database đã có đầy đủ bảng (users, posts, comments)
- ✅ Có file `.gitignore` (không push database credentials)
- ✅ Config files đã được chuẩn bị

### 2. Tạo file .gitignore (nếu chưa có)
```
backend/database.sqlite
backend/inc/db_credentials.php
oauth_config.php
node_modules/
.env
*.log
```

---

## 🌐 Phương án 1: Deploy lên Hosting có cPanel

### Bước 1: Export Database
```bash
# Nếu dùng MySQL/MariaDB
mysqldump -u root vnmt_db > vnmt_backup.sql
```

### Bước 2: Upload files
1. Nén thư mục `vnmt` thành file ZIP
2. Truy cập cPanel của hosting
3. Vào **File Manager** → Upload file ZIP
4. Extract file ZIP vào thư mục `public_html`

### Bước 3: Import Database
1. Vào **phpMyAdmin** trên cPanel
2. Tạo database mới (ví dụ: `vnmt_db`)
3. Import file `vnmt_backup.sql`

### Bước 4: Cấu hình Database
Sửa file `backend/inc/db.php`:
```php
$host = 'localhost';
$dbname = 'username_vnmt'; // Tên database trên hosting
$username = 'username_dbuser'; // Username database
$password = 'your_password'; // Password database
```

### Bước 5: Cập nhật URLs
Sửa các file có đường dẫn:
- `inc/header-new.php`: Thay `/vnmt/` → `/`
- `oauth_config.php`: Cập nhật redirect URIs với domain thật

---

## 🐙 Phương án 2: Deploy qua GitHub (Khuyến nghị)

### Bước 1: Commit code
```bash
cd C:\xampp\htdocs\vnmt

# Add tất cả files
git add .

# Commit với message
git commit -m "Add user authentication and OAuth system"

# Push lên GitHub
git push origin master
```

### Bước 2: Deploy từ GitHub

#### Nếu dùng Vercel/Netlify:
1. Kết nối GitHub repo với Vercel/Netlify
2. Cấu hình build settings
3. Deploy tự động

#### Nếu dùng VPS/Server:
```bash
# SSH vào server
ssh user@your-server.com

# Clone repo
git clone https://github.com/yourusername/vnmt.git
cd vnmt

# Cài đặt dependencies (nếu có)
composer install  # Nếu dùng Composer
npm install       # Nếu có Node packages

# Cấu hình database
cp backend/inc/db.php.example backend/inc/db.php
nano backend/inc/db.php  # Sửa thông tin database

# Import database
mysql -u username -p vnmt_db < vnmt_backup.sql
```

---

## ⚙️ Cấu hình sau khi deploy

### 1. Cập nhật OAuth URLs
Sửa file `oauth_config.php`:
```php
'redirect_uri' => 'https://yourdomain.com/oauth_callback.php?provider=google',
```

Cập nhật trên Google Cloud Console và Facebook Developers:
- Authorized redirect URIs
- Authorized domains

### 2. Cấu hình HTTPS (Bắt buộc cho production)
- Cài SSL certificate (Let's Encrypt miễn phí)
- Force HTTPS trong `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 3. Bảo mật
Tạo file `.htaccess` trong thư mục `backend`:
```apache
# Chặn truy cập trực tiếp vào backend
Deny from all
<Files "index.php">
    Allow from all
</Files>
```

### 4. Performance
Bật caching trong `.htaccess`:
```apache
# Enable caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## 🧪 Test sau khi deploy

1. **Truy cập website**: `https://yourdomain.com`
2. **Test đăng ký**: Tạo tài khoản mới
3. **Test đăng nhập**: Đăng nhập với tài khoản vừa tạo
4. **Test bình luận**: Vào bài viết và bình luận
5. **Test OAuth**: Đăng nhập bằng Google/Facebook (nếu đã cấu hình)

---

## 🔥 Deploy nhanh với Git (Nếu đã setup)

```bash
# Commit tất cả thay đổi
git add .
git commit -m "Update: Authentication system with OAuth"
git push origin master

# Nếu có remote server với git hook
ssh user@server.com "cd /var/www/vnmt && git pull origin master"
```

---

## 📝 Checklist Deploy

- [ ] Backup database hiện tại
- [ ] Commit và push code lên Git
- [ ] Upload files lên hosting
- [ ] Import database
- [ ] Cập nhật database config
- [ ] Cập nhật OAuth redirect URIs
- [ ] Test đăng ký/đăng nhập
- [ ] Test bình luận
- [ ] Cấu hình HTTPS
- [ ] Test trên mobile
- [ ] Setup backup tự động

---

## 🆘 Troubleshooting

### Lỗi kết nối database
- Kiểm tra thông tin trong `backend/inc/db.php`
- Kiểm tra user database có quyền truy cập không

### Lỗi 404 Not Found
- Kiểm tra `.htaccess` có được upload không
- Bật `mod_rewrite` trên Apache

### CSS/JS không load
- Kiểm tra đường dẫn trong HTML
- Xóa cache trình duyệt (Ctrl + F5)

### OAuth không hoạt động
- Cập nhật redirect URIs với domain mới
- Kiểm tra HTTPS đã bật chưa
- Xem logs lỗi trong `oauth_callback.php`

---

## 📞 Hosting khuyến nghị

### Miễn phí:
- **000webhost** - PHP, MySQL miễn phí
- **InfinityFree** - Không giới hạn bandwidth
- **Vercel** - Tốt cho static sites

### Trả phí (Tốt):
- **DigitalOcean** - $5/tháng, VPS linh hoạt
- **Hostinger** - $2-3/tháng, shared hosting tốt
- **Vultr** - VPS giá rẻ, hiệu năng tốt

---

## ✅ Hoàn tất!

Sau khi deploy xong, website của bạn sẽ có:
- ✅ Hệ thống đăng nhập/đăng ký
- ✅ Quản lý tài khoản
- ✅ Hệ thống bình luận
- ✅ Đăng nhập bằng Google/Facebook (nếu cấu hình)
- ✅ Backend quản trị đầy đủ

🎉 **Chúc mừng! Website của bạn đã live!**

