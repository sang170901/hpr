# VNMaterial - Force Push to GitHub (Replace All)

# ⚠️  CẢNH BÁO: Script này sẽ XÓA HẾT dữ liệu cũ trên GitHub!

# Mở Command Prompt (cmd.exe) as Administrator và chạy:

cd /d "c:\xampp\htdocs\vnmt"

# 1. Kiểm tra trạng thái hiện tại
git status

# 2. Add tất cả files mới
git add .

# 3. Commit với message
git commit -m "feat: Complete VNMaterial monorepo restructure

BREAKING CHANGE: Replace entire repository with new structure

- Remove all old files and history
- Add frontend/ directory with complete HTML/CSS/JS website
- Add backend/ directory with Laravel 8 + Filament admin
- Add comprehensive documentation and setup guides
- Eco-friendly theme with responsive design
- Complete database schema with 7 models
- Admin panel ready for development"

# 4. Force push (XÓA HẾT dữ liệu cũ trên GitHub)
git push --force --set-upstream origin main

# 5. Xác nhận
git status

# ========================================
# SAU KHI CHẠY XONG:
# ========================================
# - Repository: https://github.com/sang170901/vnm
# - Toàn bộ dữ liệu cũ đã bị xóa
# - Cấu trúc monorepo mới đã được upload
# ========================================