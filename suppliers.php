<?php include 'inc/header-new.php'; ?>
<?php
require_once 'backend/inc/db.php';

// Lấy danh sách nhà cung cấp từ cơ sở dữ liệu
try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT slug, logo, name, description, location FROM suppliers WHERE status = 1 ORDER BY name ASC");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $suppliers = [];
    error_log("Lỗi khi truy xuất nhà cung cấp: " . $e->getMessage());
}
?>

<section style="padding: 60px 0;">
    <div class="container">
        <h1 class="page-title" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 20px; color: #1e293b; text-align: center;">
            Nhà cung cấp
        </h1>
        
        <p class="page-description" style="text-align: center; color: #475569; margin-bottom: 40px; font-size: 1.1rem;">
            VNBuilding là nguồn kiến thức xây dựng đáng tin cậy, chuyên cung cấp thông tin chính xác và hiểu biết sâu sắc về vật liệu xây dựng, kỹ thuật và thực hành bền vững.
        </p>

        <div class="suppliers-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 0; list-style: none;">
            <?php foreach ($suppliers as $s): ?>
            <div class="supplier-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="supplier-card-header" style="background: #f8fafc; padding: 20px; text-align: center;">
                    <img src="<?php echo htmlspecialchars($s['logo']) ?>" alt="<?php echo htmlspecialchars($s['name']) ?>" class="supplier-logo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #e5e7eb;">
                </div>
                <div class="supplier-card-body" style="padding: 20px; text-align: center;">
                    <h3 class="supplier-name" style="font-size: 1.5rem; font-weight: 600; color: #1e293b; margin-bottom: 10px;"><?php echo htmlspecialchars($s['name']) ?></h3>
                    <p class="supplier-description" style="font-size: 1rem; color: #64748b; margin-bottom: 10px;"><?php echo htmlspecialchars($s['description']) ?></p>
                    <p class="supplier-location" style="font-size: 0.9rem; color: #475569;">📍 <?php echo htmlspecialchars($s['location']) ?></p>
                </div>
                <div class="supplier-card-footer" style="background: #f1f5f9; padding: 15px; text-align: center;">
                    <a href="supplier.php?slug=<?php echo urlencode($s['slug']) ?>" class="supplier-btn" style="background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: background 0.3s ease;">
                        XEM CHI TIẾT
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <style>
        /* Cải thiện giao diện tổng thể */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
            color: #1e293b;
        }

        .page-description {
            text-align: center;
            color: #475569;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }

        /* Lưới nhà cung cấp */
        .suppliers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 0;
            list-style: none;
        }

        .supplier-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .supplier-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .supplier-card-header {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
        }

        .supplier-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e5e7eb;
        }

        .supplier-card-body {
            padding: 20px;
            text-align: center;
        }

        .supplier-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .supplier-description {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 10px;
        }

        .supplier-location {
            font-size: 0.9rem;
            color: #475569;
        }

        .supplier-card-footer {
            background: #f1f5f9;
            padding: 15px;
            text-align: center;
        }

        .supplier-btn {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .supplier-btn:hover {
            background: #1e40af;
        }

        /* Hiệu ứng khi tải trang */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .container {
            animation: fadeIn 0.5s ease-in-out;
        }
        </style>
        
        <div style="text-align: center; margin-top: 3rem;">
            <p style="color: #64748b; margin-bottom: 1rem;">Bạn là nhà cung cấp vật liệu xây dựng?</p>
            <button style="background: #2563eb; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                Đăng ký hợp tác
            </button>
        </div>
    </div>
</section>

<?php include 'inc/footer-new.php'; ?>