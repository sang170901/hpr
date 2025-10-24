# 📸 Hướng Dẫn Quản Lý Slider Trang Chủ

## 🎯 Tổng quan

Hệ thống quản lý slider cho phép bạn kiểm soát các banner/slider hiển thị trên **trang chủ (index.php)** của website VNMaterial.

## 🔗 Truy cập

- **Backend Admin**: `backend/sliders.php`
- **Frontend Hiển thị**: `index.php` (trang chủ)

## ✨ Tính năng

### 1. Quản lý Slider

- ✅ **Thêm slider mới** - Tạo banner với tiêu đề, phụ đề, mô tả và hình ảnh
- ✏️ **Sửa slider** - Cập nhật thông tin slider hiện có
- 🗑️ **Xóa slider** - Xóa slider không cần thiết
- 👁️ **Xem trước** - Click vào ảnh để xem kích thước đầy đủ
- 🔄 **Sắp xếp thứ tự** - Điều chỉnh thứ tự hiển thị

### 2. Trạng thái Hiển thị

Slider sẽ **chỉ hiển thị trên trang chủ** khi:

1. ✅ **Trạng thái**: Được bật (status = "Hoạt động")
2. 📅 **Thời gian**: Nằm trong khoảng thời gian hiển thị (nếu có cài đặt)

#### Các trường hợp KHÔNG hiển thị:

- ❌ **Đã tắt**: Trạng thái = "Tạm dừng"
- ⏳ **Chưa đến ngày**: Ngày hiện tại < Ngày bắt đầu
- 📆 **Đã hết hạn**: Ngày hiện tại > Ngày kết thúc

### 3. Dashboard Trực quan

#### Bảng thống kê:
- 🟢 **Đang hiển thị**: Số slider đang active trên trang chủ
- 📊 **Tổng số**: Tổng số slider trong hệ thống

#### Cột "🏠 Trang chủ":
- 🟢 **Đang hiển thị** - Slider đang hiển thị trên trang chủ (highlight màu xanh)
- 🔴 **Không hiển thị** - Slider không hiển thị (kèm lý do)

## 📝 Hướng dẫn sử dụng

### Thêm Slider Mới

1. Click nút **"+ Thêm Slider"**
2. Điền thông tin:
   - **Tiêu đề** *(bắt buộc)*: Tên chính của slider
   - **Phụ đề**: Mô tả ngắn
   - **Mô tả**: Nội dung chi tiết
   - **URL Hình ảnh** *(bắt buộc)*: Đường dẫn tới ảnh (VD: `assets/images/slider-1.jpg`)
   - **Link đích**: URL khi click vào slider
   - **Text nút Link**: Text hiển thị trên nút (VD: "Khám phá ngay")
   - **Thứ tự**: Số thứ tự hiển thị (số nhỏ xuất hiện trước)
   - **Ngày bắt đầu/kết thúc**: Thời gian hiển thị (có thể để trống = luôn hiển thị)
   - **Kích hoạt**: Checkbox để bật/tắt

3. Click **"💾 Thêm slider"**

### Sửa Slider

1. Click nút **"✏️ Sửa"** ở slider cần chỉnh
2. Cập nhật thông tin
3. Click **"💾 Lưu thay đổi"**

### Xóa Slider

1. Click nút **"🗑️ Xóa"** ở slider cần xóa
2. Xác nhận xóa

### Xem Trang Chủ

- Click nút **"👁️ Xem trang chủ"** để mở trang chủ trong tab mới

## 🎨 Giao diện Slider trên Trang Chủ

- **Kích thước**: 500px cao (responsive trên mobile)
- **Hiệu ứng**: Tự động chuyển slide sau 6 giây
- **Điều khiển**: 
  - Nút Previous/Next
  - Dots navigation
  - Hover để tạm dừng
- **Nội dung hiển thị**:
  - Tiêu đề (lớn, màu trắng)
  - Phụ đề (nếu có)
  - Mô tả (nếu có)
  - Nút CTA (nếu có link)

## 📊 Cấu trúc Database

Bảng: `sliders`

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | INT | ID slider |
| title | VARCHAR(255) | Tiêu đề |
| subtitle | VARCHAR(255) | Phụ đề |
| description | TEXT | Mô tả |
| image | VARCHAR(500) | Đường dẫn ảnh |
| link | VARCHAR(500) | Link đích |
| link_text | VARCHAR(100) | Text nút link |
| start_date | DATE | Ngày bắt đầu hiển thị |
| end_date | DATE | Ngày kết thúc hiển thị |
| status | TINYINT(1) | Trạng thái (1=hoạt động, 0=tạm dừng) |
| display_order | INT | Thứ tự hiển thị |
| created_at | TIMESTAMP | Ngày tạo |

## 🔧 File liên quan

- **Backend**: `backend/sliders.php` - Trang quản lý
- **Frontend**: `inc/slider.php` - Component slider
- **Hiển thị**: `index.php` - Include slider
- **Database**: `backend/inc/init_db.php` - Schema
- **Migration**: `backend/scripts/update_sliders_table.php`

## 💡 Tips

1. **Hình ảnh tối ưu**: 
   - Kích thước khuyến nghị: 1920x500px
   - Format: JPG/PNG/WebP
   - Dung lượng: < 500KB

2. **Thứ tự hiển thị**:
   - Bắt đầu từ 1, 2, 3...
   - Slider có số nhỏ hiển thị trước

3. **Thời gian hiển thị**:
   - Để trống cả 2 = luôn hiển thị
   - Chỉ đặt ngày bắt đầu = hiển thị từ ngày đó trở đi
   - Chỉ đặt ngày kết thúc = hiển thị đến ngày đó

4. **Text hiển thị**:
   - Tiêu đề ngắn gọn (< 50 ký tự)
   - Mô tả rõ ràng (< 150 ký tự)

## ❓ Troubleshooting

### Slider không hiển thị trên trang chủ?

Kiểm tra:
1. ✅ Trạng thái = "Hoạt động"
2. 📅 Trong khoảng thời gian hiển thị
3. 🖼️ Đường dẫn ảnh đúng
4. 🔄 Refresh trang (Ctrl+F5)

### Ảnh không hiển thị?

- Kiểm tra đường dẫn ảnh
- Đảm bảo file tồn tại
- Kiểm tra quyền truy cập file

---

**Phát triển bởi**: VNMaterial Team  
**Cập nhật**: <?php echo date('d/m/Y'); ?>

