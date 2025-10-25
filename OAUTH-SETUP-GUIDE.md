# Hướng dẫn cài đặt OAuth (Google & Facebook Login)

## 🔐 Tổng quan
Để sử dụng đăng nhập bằng Google và Facebook, bạn cần:
1. Tạo ứng dụng trên Google Cloud Console
2. Tạo ứng dụng trên Facebook Developers
3. Cập nhật thông tin OAuth credentials vào file `oauth_config.php`

---

## 📱 Google OAuth Setup

### Bước 1: Tạo Google Cloud Project
1. Truy cập: https://console.cloud.google.com/
2. Tạo project mới hoặc chọn project có sẵn
3. Vào menu **APIs & Services** → **Credentials**

### Bước 2: Tạo OAuth 2.0 Client ID
1. Click **Create Credentials** → **OAuth client ID**
2. Chọn **Application type**: Web application
3. Nhập tên: `VNMT Website`
4. **Authorized JavaScript origins**:
   ```
   http://localhost:8080
   ```
5. **Authorized redirect URIs**:
   ```
   http://localhost:8080/vnmt/oauth_callback.php?provider=google
   ```
6. Click **Create**

### Bước 3: Lấy Client ID và Client Secret
- Copy **Client ID** và **Client Secret**
- Lưu lại để cập nhật vào `oauth_config.php`

### Bước 4: Enable Google+ API
1. Vào **APIs & Services** → **Library**
2. Tìm "Google+ API" và enable nó

---

## 📘 Facebook OAuth Setup

### Bước 1: Tạo Facebook App
1. Truy cập: https://developers.facebook.com/
2. Click **My Apps** → **Create App**
3. Chọn **Use case**: Other → Next
4. Chọn **App type**: Consumer → Next
5. Nhập tên app: `VNMT Website`

### Bước 2: Cấu hình Facebook Login
1. Vào **Dashboard** của app vừa tạo
2. Click **Add Product** → Chọn **Facebook Login** → Set up
3. Chọn **Web** platform
4. Nhập **Site URL**: 
   ```
   http://localhost:8080/vnmt/
   ```

### Bước 3: Cấu hình OAuth Redirect URIs
1. Vào **Facebook Login** → **Settings**
2. Thêm vào **Valid OAuth Redirect URIs**:
   ```
   http://localhost:8080/vnmt/oauth_callback.php?provider=facebook
   ```
3. Save changes

### Bước 4: Lấy App ID và App Secret
1. Vào **Settings** → **Basic**
2. Copy **App ID** và **App Secret**
3. Lưu lại để cập nhật vào `oauth_config.php`

### Bước 5: Chuyển App sang Live Mode
1. Vào **Settings** → **Basic**
2. Chuyển toggle từ **Development** sang **Live**
3. Thêm **Privacy Policy URL** (bắt buộc):
   ```
   http://localhost:8080/vnmt/privacy.php
   ```

---

## ⚙️ Cập nhật OAuth Config

Mở file `oauth_config.php` và cập nhật thông tin:

```php
return [
    'google' => [
        'client_id' => 'YOUR_GOOGLE_CLIENT_ID_HERE',
        'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET_HERE',
        // ... các thông tin khác giữ nguyên
    ],
    'facebook' => [
        'app_id' => 'YOUR_FACEBOOK_APP_ID_HERE',
        'app_secret' => 'YOUR_FACEBOOK_APP_SECRET_HERE',
        // ... các thông tin khác giữ nguyên
    ]
];
```

---

## 🧪 Test OAuth

1. Mở trang đăng nhập: `http://localhost:8080/vnmt/login.php`
2. Click nút **Google** hoặc **Facebook**
3. Đăng nhập bằng tài khoản Google/Facebook
4. Sau khi đăng nhập thành công, bạn sẽ được redirect về trang chủ

---

## 🚨 Lưu ý quan trọng

1. **Localhost testing**:
   - Google và Facebook cho phép test với `localhost`
   - Không cần HTTPS cho localhost

2. **Production deployment**:
   - Khi deploy lên server thật, cần:
     - Thay đổi URLs trong OAuth config
     - Thêm domain vào Authorized domains
     - Sử dụng HTTPS (bắt buộc)

3. **Email permission**:
   - App cần quyền truy cập email
   - Nếu user không cấp quyền email, đăng nhập sẽ thất bại

4. **Facebook App Review**:
   - Trong development mode, chỉ admin/developer/tester có thể login
   - Để public user sử dụng, cần submit app review

---

## 🛠️ Troubleshooting

### Lỗi: "redirect_uri_mismatch"
- Kiểm tra lại redirect URI trong OAuth config
- Đảm bảo URL khớp chính xác (có/không có trailing slash)

### Lỗi: "invalid_client"
- Kiểm tra lại Client ID/Secret
- Đảm bảo không có khoảng trắng thừa

### Facebook: "App Not Set Up"
- Đảm bảo app đã được chuyển sang Live mode
- Kiểm tra Privacy Policy URL

---

## 📞 Support

Nếu gặp vấn đề, tham khảo:
- Google: https://developers.google.com/identity/protocols/oauth2
- Facebook: https://developers.facebook.com/docs/facebook-login/web

