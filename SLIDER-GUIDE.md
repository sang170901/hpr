# 📏 Hướng Dẫn Kích Thước Ảnh Slider - VNMaterial

## 🎯 **Kích Thước Chuẩn Cho Slider Images**

### 📐 **Kích thước khuyến nghị:**
- **Width**: `1920px` (chiều rộng)
- **Height**: `500px` (chiều cao)
- **Aspect Ratio**: `3.84:1` (gần như 4:1)
- **Format**: SVG, PNG, JPG, WebP
- **File Size**: < 500KB mỗi ảnh

### 🖥️ **Kích thước hiển thị thực tế:**

#### **Desktop (>1200px):**
- Container: `1920px × 500px` (max)
- Display: Full width × 500px height

#### **Tablet (768px - 1199px):**
- Container: `768px - 1199px × 350px`
- Display: Full width × 350px height

#### **Mobile (<768px):**
- Container: `<768px × 250px`
- Display: Full width × 250px height

### 🎨 **Thiết kế Guidelines:**

#### **Safe Zone (Vùng an toàn):**
- **Text content**: Đặt trong vùng 60% giữa ảnh
- **Important elements**: Tránh đặt ở 20% hai bên
- **Logo/branding**: Góc trên phải hoặc trái

#### **Typography trong ảnh:**
- **Title**: Font size ≥ 36px, font weight bold
- **Description**: Font size ≥ 18px, readable contrast
- **Call-to-action**: Rõ ràng, nổi bật

### 📱 **Responsive Considerations:**

#### **Mobile-first approach:**
- Text phải đọc được ở kích thước 250px height
- Buttons/CTA phải đủ lớn để touch (≥44px)
- Contrast ratio ≥ 4.5:1 cho text

### 🛠️ **Tools Recommended:**

#### **Design:**
- **Figma**: Template 1920×500px
- **Photoshop**: Canvas 1920×500px, 72 DPI
- **Canva**: Custom size 1920×500px

#### **Optimization:**
- **TinyPNG**: Compress PNG/JPG
- **SVGO**: Optimize SVG files
- **WebP**: Convert for better performance

### 📂 **File Naming Convention:**
```
slide-[number]-[product-name].svg/jpg/png
Examples:
- slide-1-ceramic-tiles.svg
- slide-2-eco-paint.jpg
- slide-3-smart-cement.png
- slide-4-steel-construction.webp
```

### 🎨 **Color Palette Integration:**
Sử dụng color scheme của website:
- **Primary**: #0891b2 (Cyan 600)
- **Accent**: #10b981 (Emerald 500)
- **Secondary**: #f59e0b (Amber 500)
- **Background**: #f0f9ff, #e0f2fe gradients

### ✅ **Quality Checklist:**

#### **Before Upload:**
- [ ] Kích thước: 1920×500px
- [ ] File size: <500KB
- [ ] Text readable on mobile
- [ ] High contrast ratio
- [ ] Consistent brand colors
- [ ] Call-to-action visible
- [ ] Product clearly displayed

#### **Testing:**
- [ ] Test trên desktop (1920px)
- [ ] Test trên tablet (768px)
- [ ] Test trên mobile (375px)
- [ ] Check loading speed
- [ ] Verify aspect ratio maintained

### 🚀 **Implementation:**

#### **Add new slide:**
1. Tạo ảnh với kích thước 1920×500px
2. Optimize file size
3. Upload vào `/assets/images/slider/`
4. Update HTML với data-link attribute
5. Test responsive display

#### **Replace existing slide:**
1. Giữ nguyên tên file hoặc update HTML
2. Đảm bảo kích thước chuẩn
3. Test trên các breakpoints

---

**Note**: Aspect ratio 3.84:1 được tối ưu cho hiển thị đa thiết bị và đảm bảo nội dung không bị crop.