# VNMaterial - Website Vật Liệu Xây Dựng

Đây là website tương tự VNBuilding.vn được tạo bằng HTML5, CSS3 và JavaScript vanilla.

## 🚀 Tính năng chính

- ✅ Responsive design cho tất cả thiết bị
- ✅ Header với navigation menu
- ✅ Hero section với slogan và mô tả
- ✅ Statistics section với 3 chỉ số chính
- ✅ Mission & Values với 6 giá trị cốt lõi
- ✅ Search section với các từ khóa phổ biến
- ✅ Blog/News section với navigation
- ✅ Partners section
- ✅ Footer với thông tin chi tiết
- ✅ Mobile menu với hamburger animation
- ✅ Smooth scrolling
- ✅ Scroll animations
- ✅ Statistics counter animation
- ✅ Interactive elements

## 📁 Cấu trúc thư mục

```
vnm/
├── index.html              # Trang chính
├── assets/
│   ├── css/
│   │   └── styles.css      # CSS responsive
│   ├── js/
│   │   └── main.js         # JavaScript interactivity
│   └── images/             # Hình ảnh (cần thêm)
└── README.md
```

## 🎨 **Thiết kế & Màu sắc mới:**

### 🌊 **Color Palette - Eco-Friendly Light Blue Theme:**
- **Primary**: `#0891b2` (Cyan 600) - Màu xanh dương chính, thể hiện sự sạch sẽ và mát mẻ
- **Primary Light**: `#06b6d4` (Cyan 500) - Màu nhạt hơn cho gradients
- **Accent**: `#10b981` (Emerald 500) - Màu xanh lá thân thiện môi trường
- **Secondary Accent**: `#f59e0b` (Amber 500) - Màu điểm nhấn vàng
- **Background**: Gradient combinations với `#f0f9ff`, `#e0f2fe`, `#ecfdf5`

### ✨ **Hiệu ứng đặc biệt:**
- **Gradient backgrounds** cho hero section và about section
- **Smooth hover animations** với transform và shadow effects
- **Glassmorphism effect** cho mission cards
- **Animated buttons** với shine effect và lift animation
- **Color-coded statistics** với gradient text
- **Floating elements** với subtle parallax
- **Backdrop blur effects** cho modern look

### 🖼️ **Hình ảnh đã tạo:**
- **Logo SVG**: Logo VNMaterial với design hiện đại
- **Statistics Icons**: 3 icons cho vật tư, nhà cung cấp, danh mục
- **Mission Icons**: 6 icons cho các giá trị cốt lõi
- **News Images**: 2 placeholder images cho blog section  
- **Partner Logos**: 7 SVG logos cho các đối tác

Tất cả hình ảnh được tạo dưới dạng **SVG vector** để đảm bảo:
- ✅ Chất lượng cao ở mọi độ phân giải
- ✅ File size nhỏ, tải nhanh
- ✅ Scalable và responsive
- ✅ Màu sắc nhất quán với theme

## 🚀 Cách chạy website

### Với XAMPP:
1. Đảm bảo XAMPP đang chạy
2. Mở trình duyệt và truy cập: `http://localhost/vnm/`

### Với Live Server (VS Code):
1. Cài đặt extension "Live Server" trong VS Code
2. Right-click vào file `index.html`
3. Chọn "Open with Live Server"

### Với Python Server:
```bash
cd c:\xampp\htdocs\vnm
python -m http.server 8000
# Truy cập http://localhost:8000
```

## 🎨 Customization

### Thay đổi màu sắc:
Chỉnh sửa CSS variables trong file `assets/css/styles.css`:
```css
:root {
    --primary-color: #2563eb;    /* Màu chính */
    --secondary-color: #64748b;  /* Màu phụ */
    --accent-color: #f59e0b;     /* Màu nhấn */
    /* ... */
}
```

### Thay đổi nội dung:
Chỉnh sửa file `index.html` để thay đổi:
- Tên website và slogan
- Thông tin liên hệ
- Nội dung các section
- Menu navigation

### Thêm tính năng:
Chỉnh sửa file `assets/js/main.js` để thêm:
- Form validation
- AJAX requests
- More animations
- Search functionality

## 📱 Responsive Breakpoints

- Desktop: >= 1024px
- Tablet: 768px - 1023px  
- Mobile: <= 767px
- Small mobile: <= 480px

## 🔧 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- IE 11+ (limited support)

## 🚀 Performance Tips

1. **Optimize Images**: Sử dụng WebP format cho hình ảnh
2. **Minify CSS/JS**: Minify code cho production
3. **Enable Gzip**: Kích hoạt compression trên server
4. **Use CDN**: Sử dụng CDN cho fonts và icons

## 📈 SEO Features

- ✅ Semantic HTML5 structure
- ✅ Meta tags optimized
- ✅ Alt text for images
- ✅ Clean URL structure
- ✅ Mobile-friendly design
- ✅ Fast loading times

## 🛠️ Development Tools

- **HTML5**: Semantic markup
- **CSS3**: Grid, Flexbox, Custom Properties
- **JavaScript ES6+**: Modern vanilla JS
- **Google Fonts**: Inter font family
- **Font Awesome**: Icons

## 📝 License

Dự án này được tạo ra để học tập và tham khảo. Bạn có thể sử dụng và chỉnh sửa theo nhu cầu.

## 🤝 Contributing

1. Fork dự án
2. Tạo feature branch
3. Commit changes
4. Push to branch  
5. Create Pull Request

## 📞 Support

Nếu có thắc mắc hoặc cần hỗ trợ, vui lòng tạo issue trên GitHub hoặc liên hệ qua email.

---

**Tạo bởi GitHub Copilot** - Phiên bản tương tự VNBuilding.vn