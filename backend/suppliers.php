<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/activity.php';
$pdo = getPDO();

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flash = ['type' => '', 'message' => ''];

// Handle POST save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_supplier'])) {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $logo = trim($_POST['logo'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $status = isset($_POST['status']) ? 1 : 0;

        try {
            if ($id) {
                $stmt = $pdo->prepare('UPDATE suppliers SET name=?, slug=?, email=?, phone=?, address=?, logo=?, description=?, category_id=?, status=? WHERE id=?');
                $stmt->execute([$name, $slug, $email, $phone, $address, $logo, $description, $category_id, $status, $id]);
                log_activity($_SESSION['user']['id'] ?? null, 'update_supplier', 'supplier', $id, json_encode(['name'=>$name]));
            } else {
                $stmt = $pdo->prepare('INSERT INTO suppliers (name, slug, email, phone, address, logo, description, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $slug, $email, $phone, $address, $logo, $description, $category_id]);
                $newId = $pdo->lastInsertId();
                log_activity($_SESSION['user']['id'] ?? null, 'create_supplier', 'supplier', $newId, json_encode(['name'=>$name]));
            }
            header('Location: suppliers.php?msg=' . urlencode('Saved') . '&t=success');
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
        $stmt = $pdo->prepare('DELETE FROM suppliers WHERE id = ?');
        $stmt->execute([$id]);
        log_activity($_SESSION['user']['id'] ?? null, 'delete_supplier', 'supplier', $id, null);
        header('Location: suppliers.php?msg=' . urlencode('Deleted') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể xóa: ' . $e->getMessage();
    }
}

// Toggle status
if ($action === 'toggle' && $id) {
    try {
        $stmt = $pdo->prepare('UPDATE suppliers SET status = 1 - status WHERE id = ?');
        $stmt->execute([$id]);
        log_activity($_SESSION['user']['id'] ?? null, 'toggle_supplier_status', 'supplier', $id, null);
        header('Location: suppliers.php?msg=' . urlencode('Updated') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể thay đổi trạng thái: ' . $e->getMessage();
    }
}

// Get supplier data for AJAX (JSON)
if ($action === 'get' && $id) {
    header('Content-Type: application/json');
    try {
        $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE id = ?');
        $stmt->execute([$id]);
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($supplier) {
            echo json_encode(['success' => true, 'supplier' => $supplier]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy nhà cung cấp']);
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

// Load supplier for edit
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE id = ?');
    $stmt->execute([$id]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Load supplier categories
$supplierCategories = $pdo->query('SELECT id, name, slug FROM supplier_categories WHERE status = 1 ORDER BY order_index, name')->fetchAll(PDO::FETCH_ASSOC);

// Search and filter
$search = trim($_GET['q'] ?? '');
$category_filter = trim($_GET['category_id'] ?? '');

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(s.name LIKE ? OR s.slug LIKE ?)';
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
}

if ($category_filter !== '') {
    $where[] = 's.category_id = ?';
    $params[] = (int)$category_filter;
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$sql = "SELECT s.*, sc.name as category_name 
        FROM suppliers s 
        LEFT JOIN supplier_categories sc ON s.category_id = sc.id 
        $whereSql 
        ORDER BY sc.order_index, s.name";

if (!empty($params)) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $suppliers = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

?>
<div class="card">
    <h2 class="page-main-title">Nhà cung cấp</h2>
    <?php if (!empty($flash['message'])): ?>
        <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?php echo htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
        <button class="small-btn primary" onclick="openAddModal()">+ Thêm nhà cung cấp</button>
        <form method="get" action="suppliers.php" style="display:flex;gap:10px;align-items:center;flex-wrap:nowrap;margin:0">
            <select name="category_id" class="compact-select" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($supplierCategories as $cat): ?>
                    <option value="<?php echo $cat['id'] ?>" <?php echo ($category_filter == $cat['id']) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="q" placeholder="Tìm theo tên hoặc slug" value="<?php echo htmlspecialchars($search) ?>" style="padding:8px;border-radius:6px;border:1px solid #e6e9ef">
            <button class="small-btn" type="submit">Tìm</button>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Địa chỉ</th>
                <th>Danh mục</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($suppliers as $s): ?>
            <tr>
                <td><?php echo $s['id'] ?></td>
                <td><?php echo htmlspecialchars($s['name']) ?></td>
                <td><?php echo htmlspecialchars($s['email']) ?></td>
                <td><?php echo htmlspecialchars($s['phone']) ?></td>
                <td><?php echo htmlspecialchars($s['address']) ?></td>
                <td><?php echo htmlspecialchars($s['category_name'] ?? '') ?></td>
                <td><?php echo ($s['status'] ?? 1) ? 'Hoạt động' : 'Không hoạt động' ?> <a class="small-btn" href="suppliers.php?action=toggle&id=<?php echo $s['id'] ?>">Bật/Tắt</a></td>
                <td><?php echo $s['created_at'] ?? '' ?></td>
                <td class="btn-row">
                    <button class="small-btn" onclick="openEditModal(<?php echo $s['id'] ?>)">Sửa</button>
                    <a class="small-btn warn" href="suppliers.php?action=delete&id=<?php echo $s['id'] ?>" onclick="return confirm('Xóa nhà cung cấp?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm Nhà Cung Cấp -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="margin:0">Thêm Nhà Cung Cấp Mới</h3>
            <span class="modal-close" onclick="closeAddModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form method="post" id="addSupplierForm">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label>Tên <span style="color:red">*</span>
                            <input type="text" name="name" id="add_name" required style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Slug
                            <input type="text" name="slug" id="add_slug" style="width:100%">
                        </label>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
                    <div>
                        <label>Email
                            <input type="email" name="email" id="add_email" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Điện thoại
                            <input type="text" name="phone" id="add_phone" style="width:100%">
                        </label>
                    </div>
                </div>

                <div style="margin-top:16px">
                    <label>Địa chỉ
                        <input type="text" name="address" id="add_address" style="width:100%">
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label>Logo (URL)
                        <input type="text" name="logo" id="add_logo" style="width:100%">
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label>Mô tả
                        <textarea name="description" id="add_description" rows="4" style="width:100%"></textarea>
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label>Danh mục nhà cung cấp
                        <select name="category_id" id="add_category_id" style="width:100%">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($supplierCategories as $cat): ?>
                                <option value="<?php echo $cat['id'] ?>">
                                    <?php echo htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="status" id="add_status" checked>
                        <span>Hoạt động</span>
                    </label>
                </div>

                <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end">
                    <button type="button" class="small-btn" onclick="closeAddModal()">Hủy</button>
                    <button type="submit" class="small-btn primary" name="save_supplier">💾 Thêm nhà cung cấp</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Nhà Cung Cấp -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="margin:0">Sửa Nhà Cung Cấp</h3>
            <span class="modal-close" onclick="closeEditModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form method="post" id="editSupplierForm" action="suppliers.php?action=edit&id=">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label>Tên <span style="color:red">*</span>
                            <input type="text" name="name" id="edit_name" required style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Slug
                            <input type="text" name="slug" id="edit_slug" style="width:100%">
                        </label>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
                    <div>
                        <label>Email
                            <input type="email" name="email" id="edit_email" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Điện thoại
                            <input type="text" name="phone" id="edit_phone" style="width:100%">
                        </label>
                    </div>
                </div>

                <div style="margin-top:16px">
                    <label>Địa chỉ
                        <input type="text" name="address" id="edit_address" style="width:100%">
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label>Logo (URL)
                        <input type="text" name="logo" id="edit_logo" style="width:100%">
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label>Mô tả
                        <textarea name="description" id="edit_description" rows="4" style="width:100%"></textarea>
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label>Danh mục nhà cung cấp
                        <select name="category_id" id="edit_category_id" style="width:100%">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($supplierCategories as $cat): ?>
                                <option value="<?php echo $cat['id'] ?>">
                                    <?php echo htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div style="margin-top:16px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="status" id="edit_status" value="1">
                        <span>Hoạt động</span>
                    </label>
                </div>

                <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end">
                    <button type="button" class="small-btn" onclick="closeEditModal()">Hủy</button>
                    <button type="submit" class="small-btn primary" name="save_supplier">💾 Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Open Add Modal
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Reset form
    document.getElementById('addSupplierForm').reset();
    document.getElementById('add_status').checked = true;
}

// Close Add Modal
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Open Edit Modal
function openEditModal(supplierId) {
    // Show modal
    document.getElementById('editModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Set form action
    document.getElementById('editSupplierForm').action = 'suppliers.php?action=edit&id=' + supplierId;
    
    // Fetch supplier data
    fetch('suppliers.php?action=get&id=' + supplierId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const s = data.supplier;
                document.getElementById('edit_name').value = s.name || '';
                document.getElementById('edit_slug').value = s.slug || '';
                document.getElementById('edit_email').value = s.email || '';
                document.getElementById('edit_phone').value = s.phone || '';
                document.getElementById('edit_address').value = s.address || '';
                document.getElementById('edit_logo').value = s.logo || '';
                document.getElementById('edit_description').value = s.description || '';
                document.getElementById('edit_category_id').value = s.category_id || '';
                document.getElementById('edit_status').checked = s.status == 1;
            } else {
                alert('Không thể tải thông tin nhà cung cấp!');
                closeEditModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi khi tải dữ liệu!');
            closeEditModal();
        });
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    
    if (event.target == addModal) {
        closeAddModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
}

// Close modals with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>
