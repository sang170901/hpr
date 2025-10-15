VNMaterial - Simple PHP Admin Backend

This is a lightweight admin backend for VNMaterial built with plain PHP and SQLite (for local development / XAMPP).
It provides basic management pages for:
- Dashboard with statistics
- Users (block/unblock)
- Products (add/edit/delete)
- Suppliers
- Vouchers
- Sliders (image upload + scheduling)
- Activity logs
- Scheduled publishing

Quick start (on Windows/XAMPP):
1. Ensure PHP is available (XAMPP Apache + PHP). Place this project under your htdocs (already here).
2. From command line or browser, run: `php backend/create_db.php` to create the SQLite database and seed an admin user.
3. Open http://localhost/vnmt/backend/login.php and login with:
   - Email: admin@vnmt.com
   - Password: admin123
4. Use the admin UI to manage content.

Notes:
- This is a simple prototype to let you manage content without installing Composer / Laravel.
- For production or complex features, migrate to a full Laravel + Filament stack.

Files:
- create_db.php : creates SQLite DB and sample data
- config.php : configuration values
- inc/db.php : PDO helper
- login.php / logout.php : auth
- index.php : dashboard
- users.php, products.php, suppliers.php, vouchers.php, sliders.php, activity_logs.php, schedule.php : admin pages

Security: This is intentionally simple. Don't use in production as-is. Replace with proper auth and permission checks when migrating to Laravel.