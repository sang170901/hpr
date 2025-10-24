<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/auth.php';
$require_helpers = true;
require __DIR__ . '/inc/activity.php';
require_once __DIR__ . '/inc/helpers.php';
$pdo = getPDO();

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flash = ['type' => '', 'message' => ''];

// Add classifications array
$productClassifications = [
    'Vật liệu',
    'Thiết Bị',
    'Công nghệ',
    'Cảnh quan'
];

// Handle POST save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_product'])) {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $status = isset($_POST['status']) ? 1 : 0;
        $featured = isset($_POST['featured']) ? 1 : 0;
    $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
        $images = trim($_POST['images'] ?? '');
        // New fields
        $manufacturer = trim($_POST['manufacturer'] ?? '');
        $origin = trim($_POST['origin'] ?? '');
        $material_type = trim($_POST['material_type'] ?? '');
        $application = trim($_POST['application'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $featured_image = trim($_POST['featured_image'] ?? '');
        $product_function = trim($_POST['product_function'] ?? '');
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $thickness = trim($_POST['thickness'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $warranty = trim($_POST['warranty'] ?? '');
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : null;
        $classification = isset($_POST['classification']) ? implode(',', $_POST['classification']) : ''; // Handle multiple selections
        $brand = trim($_POST['brand'] ?? ''); // New brand field

        try {
            // If supplier_id empty, try to find based on slug/manufacturer
            if (empty($supplier_id)) {
                $auto = find_supplier_id($pdo, $slug, $manufacturer);
                if ($auto) { $supplier_id = $auto; }
            }
            if ($id) {
                $stmt = $pdo->prepare('UPDATE products SET name=?, slug=?, description=?, price=?, status=?, featured=?, images=?, supplier_id=?, manufacturer=?, origin=?, material_type=?, application=?, website=?, featured_image=?, product_function=?, category_id=?, thickness=?, color=?, warranty=?, stock=?, classification=?, brand=? WHERE id=?');
                $stmt->execute([$name, $slug, $description, $price, $status, $featured, $images, $supplier_id, $manufacturer, $origin, $material_type, $application, $website, $featured_image, $product_function, $category_id, $thickness, $color, $warranty, $stock, $classification, $brand, $id]);
                log_activity($_SESSION['user']['id'] ?? null, 'update_product', 'product', $id, json_encode(['name'=>$name,'price'=>$price]));
            } else {
                $stmt = $pdo->prepare('INSERT INTO products (name, slug, description, price, status, featured, images, supplier_id, manufacturer, origin, material_type, application, website, featured_image, product_function, category_id, thickness, color, warranty, stock, classification, brand) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $slug, $description, $price, $status, $featured, $images, $supplier_id, $manufacturer, $origin, $material_type, $application, $website, $featured_image, $product_function, $category_id, $thickness, $color, $warranty, $stock, $classification, $brand]);
                $newId = $pdo->lastInsertId();
                log_activity($_SESSION['user']['id'] ?? null, 'create_product', 'product', $newId, json_encode(['name'=>$name,'price'=>$price]));
            }
            header('Location: products.php?msg=' . urlencode('Đã lưu thành công') . '&t=success');
            exit;
        } catch (PDOException $e) {
            $flash['type'] = 'error';
            $flash['message'] = 'Lỗi DB: ' . $e->getMessage();
        }
    }
}

// Delete
if ($action === 'delete' && $id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        log_activity($_SESSION['user']['id'] ?? null, 'delete_product', 'product', $id, null);
        header('Location: products.php?msg=' . urlencode('Đã xóa thành công') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể xóa sản phẩm: ' . $e->getMessage();
    }
}

// Toggle status
if ($action === 'toggle' && $id) {
    try {
        $stmt = $pdo->prepare('UPDATE products SET status = 1 - status WHERE id = ?');
        $stmt->execute([$id]);
        log_activity($_SESSION['user']['id'] ?? null, 'toggle_product_status', 'product', $id, null);
        header('Location: products.php?msg=' . urlencode('Đã cập nhật thành công') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể thay đổi trạng thái: ' . $e->getMessage();
    }
}

// Get product data for AJAX (JSON)
if ($action === 'get' && $id) {
    header('Content-Type: application/json');
    try {
        $stmt = $pdo->prepare('SELECT p.*, s.name as supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.id WHERE p.id = ?');
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // Convert classification string to array
            if (!empty($product['classification'])) {
                $product['classification_array'] = explode(',', $product['classification']);
            } else {
                $product['classification_array'] = [];
            }
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

require __DIR__ . '/inc/header.php';

// Show flash from redirect
if (isset($_GET['msg'])) {
    $flash['message'] = urldecode($_GET['msg']);
    $flash['type'] = $_GET['t'] ?? 'success';
}

// Load product for edit
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Suppliers for select (include logo and slug for UI enhancements)
$suppliers = $pdo->query('SELECT id,name,logo,slug FROM suppliers ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

// Product categories for filter (main categories + subcategories)
$mainCategories = $pdo->query('SELECT id,name,slug FROM product_categories WHERE parent_id IS NULL ORDER BY order_index')->fetchAll(PDO::FETCH_ASSOC);
$subCategories = $pdo->query('SELECT id,name,slug,parent_id FROM product_categories WHERE parent_id IS NOT NULL ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

// Group subcategories by parent
$categoriesGrouped = [];
foreach ($mainCategories as $main) {
    $categoriesGrouped[$main['id']] = [
        'main' => $main,
        'subs' => []
    ];
}
foreach ($subCategories as $sub) {
    if (isset($categoriesGrouped[$sub['parent_id']])) {
        $categoriesGrouped[$sub['parent_id']]['subs'][] = $sub;
    }
}

// Search and filters
$search = trim($_GET['q'] ?? '');
$category_filter = trim($_GET['category_id'] ?? '');
$supplier_filter = trim($_GET['supplier'] ?? '');
$status_filter = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(p.name LIKE ? OR p.slug LIKE ?)';
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
}

if ($category_filter !== '') {
    $where[] = '(p.category_id = ? OR p.category_id IN (SELECT id FROM product_categories WHERE parent_id = ?))';
    $params[] = (int)$category_filter;
    $params[] = (int)$category_filter;
}

if ($supplier_filter !== '') {
    $where[] = 'p.supplier_id = ?';
    $params[] = (int)$supplier_filter;
}

if ($status_filter !== null) {
    $where[] = 'p.status = ?';
    $params[] = (int)$status_filter;
}

if ($min_price !== null) {
    $where[] = 'p.price >= ?';
    $params[] = $min_price;
}

if ($max_price !== null) {
    $where[] = 'p.price <= ?';
    $params[] = $max_price;
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$sql = "SELECT p.id,p.name,p.slug,p.price,p.status,p.featured,p.created_at,pc.name as category_name,s.name as supplier_name 
        FROM products p 
        LEFT JOIN suppliers s ON p.supplier_id = s.id 
        LEFT JOIN product_categories pc ON p.category_id = pc.id 
        $whereSql 
        ORDER BY p.id DESC";
if (!empty($params)) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Update placeholders, labels, and messages to Vietnamese
$flash['message'] = $flash['message'] === 'Saved' ? 'Đã lưu thành công' : $flash['message'];
$flash['message'] = $flash['message'] === 'Deleted' ? 'Đã xóa thành công' : $flash['message'];
$flash['message'] = $flash['message'] === 'Updated' ? 'Đã cập nhật thành công' : $flash['message'];
$flash['message'] = $flash['message'] === 'Không thể xóa' ? 'Không thể xóa sản phẩm' : $flash['message'];
?>
<div class="card">
    <h2 class="page-main-title">Sản phẩm</h2>
    <?php if (!empty($flash['message'])): ?>
        <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error' ?>"><?php echo htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
        <button class="small-btn primary" onclick="openAddModal()">+ Thêm sản phẩm</button>
        <!-- Combined filters + search form -->
        <form method="get" action="products.php" style="display:flex;gap:10px;align-items:center;flex-wrap:nowrap;margin:0">
            <select name="category_id" class="compact-select" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categoriesGrouped as $group): ?>
                    <option value="<?php echo $group['main']['id'] ?>" <?php echo ($category_filter == $group['main']['id']) ? 'selected' : '' ?>>
                        📁 <?php echo htmlspecialchars($group['main']['name']) ?>
                    </option>
                    <?php foreach ($group['subs'] as $sub): ?>
                        <option value="<?php echo $sub['id'] ?>" <?php echo ($category_filter == $sub['id']) ? 'selected' : '' ?>>
                            &nbsp;&nbsp;&nbsp;└── <?php echo htmlspecialchars($sub['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>

            <select name="supplier" class="compact-select" onchange="this.form.submit()">
                <option value="">Tất cả NCC</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?php echo $s['id'] ?>" <?php echo ($supplier_filter == $s['id']) ? 'selected' : '' ?>><?php echo htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="compact-select" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="1" <?php echo ($status_filter === '1') ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?php echo ($status_filter === '0') ? 'selected' : '' ?>>Tạm ngưng</option>
            </select>

            <input type="text" name="min_price" class="compact-input" placeholder="Giá từ" value="<?php echo htmlspecialchars($min_price ?? '') ?>">
            <input type="text" name="max_price" class="compact-input" placeholder="đến" value="<?php echo htmlspecialchars($max_price ?? '') ?>">

            <input type="text" name="q" placeholder="Tìm theo tên hoặc slug" value="<?php echo htmlspecialchars($search) ?>" style="padding:8px;border-radius:6px;border:1px solid #e6e9ef">
            <button class="small-btn" type="submit">Tìm</button>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Hình</th>
                <th>Giá</th>
                <th>Nhà cung cấp</th>
                <th>Phân loại</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo $p['id'] ?></td>
                <td><?php echo htmlspecialchars($p['name']) ?></td>
                <td><?php if (!empty($p['featured_image'])): ?><img src="<?php echo htmlspecialchars($p['featured_image']) ?>" alt="" style="height:40px"><?php else: ?>-<?php endif; ?></td>
                <td><?php echo number_format($p['price'], 2) ?></td>
                <td><?php echo htmlspecialchars($p['supplier_name'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($p['category_name'] ?? '') ?></td>
                <td><?php echo $p['status'] ? 'Hoạt động' : 'Không hoạt động' ?> <a class="small-btn" href="products.php?action=toggle&id=<?php echo $p['id'] ?>">Bật/Tắt</a></td>
                <td><?php echo $p['created_at'] ?></td>
                <td class="btn-row">
                    <a class="small-btn" href="../product-detail.php?id=<?php echo $p['id'] ?>" target="_blank" title="Xem chi tiết">
                        <i class="fas fa-eye"></i> Xem
                    </a>
                    <button class="small-btn" onclick="openEditModal(<?php echo $p['id'] ?>)">Sửa</button>
                    <a class="small-btn warn" href="products.php?action=delete&id=<?php echo $p['id'] ?>" onclick="return confirm('Xóa sản phẩm?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Note: Do products có quá nhiều trường (20+ fields), modal sẽ dùng scroll -->

<!-- Modal Thêm Sản Phẩm -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh;">
        <div class="modal-header">
            <h3 style="margin:0">Thêm Sản Phẩm Mới</h3>
            <span class="modal-close" onclick="closeAddModal()">&times;</span>
        </div>
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            <form method="post" id="addProductForm">
        <div class="form-group">
            <label for="name">Tên sản phẩm</label>
            <input type="text" name="name" id="name" placeholder="Nhập tên sản phẩm" value="<?php echo htmlspecialchars($product['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" name="slug" id="slug" placeholder="Nhập slug sản phẩm" value="<?php echo htmlspecialchars($product['slug'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description" id="description" placeholder="Nhập mô tả sản phẩm"><?php echo htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" name="price" id="price" placeholder="Nhập giá sản phẩm" value="<?php echo htmlspecialchars($product['price'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="supplier">Nhà cung cấp</label>
            <!-- Hidden actual supplier_id stored here -->
            <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo isset($product['supplier_id']) ? htmlspecialchars($product['supplier_id']) : '' ?>">
            <!-- Searchable supplier input backed by datalist -->
            <input list="supplier_list" id="supplier_search" placeholder="Tìm hoặc gõ tên nhà cung cấp" value="<?php echo isset($product['supplier_name']) ? htmlspecialchars($product['supplier_name']) : '' ?>">
            <datalist id="supplier_list">
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?php echo htmlspecialchars($s['name']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <img id="supplier_logo" src="" alt="" style="height:40px;margin-left:8px;display:none;vertical-align:middle">
        </div>
        <div class="form-group">
            <label for="images">Hình (URL, nhiều cái cách nhau bằng dấu phẩy)</label>
            <input type="text" name="images" id="images" value="<?php echo isset($product['images']) ? htmlspecialchars($product['images']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="featured_image">Hình đại diện (URL)</label>
            <input type="text" name="featured_image" id="featured_image" value="<?php echo isset($product['featured_image']) ? htmlspecialchars($product['featured_image']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="manufacturer">Nhà sản xuất</label>
            <input type="text" name="manufacturer" id="manufacturer" value="<?php echo isset($product['manufacturer']) ? htmlspecialchars($product['manufacturer']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="origin">Nơi sản xuất</label>
            <input type="text" name="origin" id="origin" value="<?php echo isset($product['origin']) ? htmlspecialchars($product['origin']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="material_type">Loại vật tư</label>
            <input type="text" name="material_type" id="material_type" value="<?php echo isset($product['material_type']) ? htmlspecialchars($product['material_type']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="application">Ứng dụng</label>
            <input type="text" name="application" id="application" value="<?php echo isset($product['application']) ? htmlspecialchars($product['application']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="product_function">Chức năng</label>
            <input type="text" name="product_function" id="product_function" value="<?php echo isset($product['product_function']) ? htmlspecialchars($product['product_function']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="category_id">Danh mục sản phẩm</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categoriesGrouped as $group): ?>
                    <optgroup label="📁 <?php echo htmlspecialchars($group['main']['name']) ?>">
                        <option value="<?php echo $group['main']['id'] ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $group['main']['id']) ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($group['main']['name']) ?>
                        </option>
                        <?php foreach ($group['subs'] as $sub): ?>
                            <option value="<?php echo $sub['id'] ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $sub['id']) ? 'selected' : '' ?>>
                                &nbsp;&nbsp;&nbsp;└── <?php echo htmlspecialchars($sub['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="thickness">Độ dày</label>
            <input type="text" name="thickness" id="thickness" value="<?php echo isset($product['thickness']) ? htmlspecialchars($product['thickness']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="color">Màu sắc</label>
            <input type="text" name="color" id="color" value="<?php echo isset($product['color']) ? htmlspecialchars($product['color']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="warranty">Bảo hành</label>
            <input type="text" name="warranty" id="warranty" value="<?php echo isset($product['warranty']) ? htmlspecialchars($product['warranty']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="stock">Tồn kho</label>
            <input type="number" name="stock" id="stock" value="<?php echo isset($product['stock']) ? htmlspecialchars($product['stock']) : '' ?>">
        </div>
        <div class="form-group">
            <label for="classification">Phân loại sản phẩm</label>
            <select name="classification[]" id="classification" class="form-control" multiple>
                <?php foreach ($productClassifications as $classification): ?>
                    <option value="<?php echo htmlspecialchars($classification); ?>" <?php echo in_array($classification, explode(',', $product['classification'] ?? '')) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($classification); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="brand">Thương hiệu</label>
            <input type="text" name="brand" id="brand" value="<?php echo isset($product['brand']) ? htmlspecialchars($product['brand']) : '' ?>">
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="status" <?php echo (isset($product['status']) && $product['status']) ? 'checked' : '' ?>> Hoạt động
                &nbsp;&nbsp;
                <input type="checkbox" name="featured" <?php echo (isset($product['featured']) && $product['featured']) ? 'checked' : '' ?>> Nổi bật
            </label>
        </div>
        <div style="margin-top:12px">
            <button class="primary" type="submit" name="save_product">Lưu</button>
            <a class="small-btn" href="products.php" style="margin-left:12px">Hủy</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/inc/footer.php'; ?>

<script>
// Map supplier names to id and logo
const suppliers = {
<?php foreach ($suppliers as $s): ?>
    "<?php echo addslashes($s['name']) ?>": { id: "<?php echo $s['id'] ?>", logo: "<?php echo addslashes($s['logo'] ?? '') ?>", slug: "<?php echo addslashes($s['slug'] ?? '') ?>" },
<?php endforeach; ?>
};

const supplierSearch = document.getElementById('supplier_search');
const supplierIdInput = document.getElementById('supplier_id');
const supplierLogo = document.getElementById('supplier_logo');

function updateSupplierFields() {
    const name = supplierSearch.value.trim();
    if (suppliers[name]) {
        supplierIdInput.value = suppliers[name].id;
        if (suppliers[name].logo) { supplierLogo.src = suppliers[name].logo; supplierLogo.style.display = 'inline-block'; }
        else { supplierLogo.style.display = 'none'; }
    } else {
        supplierIdInput.value = '';
        supplierLogo.style.display = 'none';
    }
}

supplierSearch && supplierSearch.addEventListener('change', updateSupplierFields);
supplierSearch && supplierSearch.addEventListener('keyup', updateSupplierFields);
</script>
