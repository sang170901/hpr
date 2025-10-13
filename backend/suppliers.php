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
        $status = isset($_POST['status']) ? 1 : 0;

        try {
            if ($id) {
                $stmt = $pdo->prepare('UPDATE suppliers SET name=?, slug=?, email=?, phone=?, address=?, logo=?, description=?, status=? WHERE id=?');
                $stmt->execute([$name, $slug, $email, $phone, $address, $logo, $description, $status, $id]);
                log_activity($_SESSION['user']['id'] ?? null, 'update_supplier', 'supplier', $id, json_encode(['name'=>$name]));
            } else {
                $stmt = $pdo->prepare('INSERT INTO suppliers (name, slug, email, phone, address, logo, description) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $slug, $email, $phone, $address, $logo, $description]);
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

// Search
$search = trim($_GET['q'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE name LIKE ? OR slug LIKE ? ORDER BY id DESC');
    $like = "%$search%";
    $stmt->execute([$like, $like]);
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $suppliers = $pdo->query('SELECT * FROM suppliers ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
}

?>
<div class="card">
    <h2 style="margin-top:0">Nhà cung cấp</h2>
    <?php if (!empty($flash['message'])): ?>
        <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error' ?>"><?php echo htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
        <a class="small-btn primary" href="suppliers.php?action=add">+ Thêm nhà cung cấp</a>
        <form method="get" action="suppliers.php" style="margin:0">
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
                <td><?php echo ($s['status'] ?? 1) ? 'Hoạt động' : 'Không hoạt động' ?> <a class="small-btn" href="suppliers.php?action=toggle&id=<?php echo $s['id'] ?>">Bật/Tắt</a></td>
                <td><?php echo $s['created_at'] ?? '' ?></td>
                <td class="btn-row">
                    <a class="small-btn" href="suppliers.php?action=edit&id=<?php echo $s['id'] ?>">Sửa</a>
                    <a class="small-btn warn" href="suppliers.php?action=delete&id=<?php echo $s['id'] ?>" onclick="return confirm('Xóa nhà cung cấp?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <h3 style="margin-top:0"><?php echo $action === 'edit' ? 'Sửa nhà cung cấp' : 'Thêm nhà cung cấp' ?></h3>
    <?php if (!empty($flash['message']) && $flash['type'] === 'error'): ?>
        <div class="flash error"><?php echo htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Tên
            <input type="text" name="name" required value="<?php echo isset($supplier['name']) ? htmlspecialchars($supplier['name']) : '' ?>">
        </label>
        <label>Slug
            <input type="text" name="slug" value="<?php echo isset($supplier['slug']) ? htmlspecialchars($supplier['slug']) : '' ?>">
        </label>
        <label>Email
            <input type="email" name="email" value="<?php echo isset($supplier['email']) ? htmlspecialchars($supplier['email']) : '' ?>">
        </label>
        <label>Điện thoại
            <input type="text" name="phone" value="<?php echo isset($supplier['phone']) ? htmlspecialchars($supplier['phone']) : '' ?>">
        </label>
        <label>Địa chỉ
            <input type="text" name="address" value="<?php echo isset($supplier['address']) ? htmlspecialchars($supplier['address']) : '' ?>">
        </label>
        <label>Logo (URL)
            <input type="text" name="logo" value="<?php echo isset($supplier['logo']) ? htmlspecialchars($supplier['logo']) : '' ?>">
        </label>
        <label>Mô tả
            <textarea name="description"><?php echo isset($supplier['description']) ? htmlspecialchars($supplier['description']) : '' ?></textarea>
        </label>
        <label>
            <input type="checkbox" name="status" <?php echo (isset($supplier['status']) && $supplier['status']) ? 'checked' : '' ?>> Hoạt động
        </label>
        <div style="margin-top:12px">
            <button class="primary" type="submit" name="save_supplier">Save</button>
            <a class="small-btn" href="suppliers.php" style="margin-left:12px">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/inc/footer.php'; ?>
