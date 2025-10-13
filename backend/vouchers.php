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
    if (isset($_POST['save_voucher'])) {
        $code = trim($_POST['code'] ?? '');
        $discount_type = trim($_POST['discount_type'] ?? 'fixed');
        $discount_value = (float)($_POST['discount_value'] ?? 0);
        $min_purchase = (float)($_POST['min_purchase'] ?? 0);
        $max_uses = (int)($_POST['max_uses'] ?? 0);
        $start_date = trim($_POST['start_date'] ?? null);
        $end_date = trim($_POST['end_date'] ?? null);
        $status = isset($_POST['status']) ? 1 : 0;

        try {
            if ($id) {
                $stmt = $pdo->prepare('UPDATE vouchers SET code=?, discount_type=?, discount_value=?, min_purchase=?, max_uses=?, start_date=?, end_date=?, status=? WHERE id=?');
                $stmt->execute([$code, $discount_type, $discount_value, $min_purchase, $max_uses, $start_date, $end_date, $status, $id]);
                log_activity($_SESSION['user']['id'] ?? null, 'update_voucher', 'voucher', $id, json_encode(['code'=>$code]));
            } else {
                $stmt = $pdo->prepare('INSERT INTO vouchers (code, discount_type, discount_value, min_purchase, max_uses, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$code, $discount_type, $discount_value, $min_purchase, $max_uses, $start_date, $end_date, $status]);
                $newId = $pdo->lastInsertId();
                log_activity($_SESSION['user']['id'] ?? null, 'create_voucher', 'voucher', $newId, json_encode(['code'=>$code]));
            }
            header('Location: vouchers.php?msg=' . urlencode('Saved') . '&t=success');
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
        $stmt = $pdo->prepare('DELETE FROM vouchers WHERE id = ?');
        $stmt->execute([$id]);
        log_activity($_SESSION['user']['id'] ?? null, 'delete_voucher', 'voucher', $id, null);
        header('Location: vouchers.php?msg=' . urlencode('Deleted') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể xóa: ' . $e->getMessage();
    }
}

// Toggle status
if ($action === 'toggle' && $id) {
    try {
        $stmt = $pdo->prepare('UPDATE vouchers SET status = 1 - status WHERE id = ?');
        $stmt->execute([$id]);
        log_activity($_SESSION['user']['id'] ?? null, 'toggle_voucher_status', 'voucher', $id, null);
        header('Location: vouchers.php?msg=' . urlencode('Updated') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể thay đổi trạng thái: ' . $e->getMessage();
    }
}

require __DIR__ . '/inc/header.php';

// Suppliers for voucher assignment
$suppliers = $pdo->query('SELECT id,name FROM suppliers ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

// Show flash from redirect
if (isset($_GET['msg'])) {
    $flash['message'] = urldecode($_GET['msg']);
    $flash['type'] = $_GET['t'] ?? 'success';
}

// Load voucher for edit
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM vouchers WHERE id = ?');
    $stmt->execute([$id]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Search
$search = trim($_GET['q'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare('SELECT * FROM vouchers WHERE code LIKE ? ORDER BY id DESC');
    $like = "%$search%";
    $stmt->execute([$like]);
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $vouchers = $pdo->query('SELECT * FROM vouchers ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
}

// Translate flash messages
$flash['message'] = $flash['message'] === 'Saved' ? 'Đã lưu thành công' : $flash['message'];
$flash['message'] = $flash['message'] === 'Deleted' ? 'Đã xóa thành công' : $flash['message'];

?>
<div class="card">
    <h2 style="margin-top:0">Quản lý voucher</h2>
    <?php if (!empty($flash['message'])): ?>
        <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error' ?>"><?php echo htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <?php
    // Stats: number of vouchers per supplier and total used_count per supplier
    $stats = $pdo->query("
        SELECT 
            COALESCE(s.id, 0) as supplier_id, 
            COALESCE(s.name, '(Tất cả)') as supplier_name, 
            COUNT(v.id) as voucher_count, 
            SUM(COALESCE(v.used_count, 0)) as total_used 
        FROM vouchers v 
        LEFT JOIN suppliers s ON v.supplier_id = s.id 
        GROUP BY COALESCE(s.id, 0), COALESCE(s.name, '(Tất cả)')
        ORDER BY voucher_count DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    if ($stats): ?>
        <div style="margin-bottom:12px;display:flex;gap:12px;flex-wrap:wrap">
            <?php foreach ($stats as $st): ?>
                <div style="background:#fff;padding:10px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.06);min-width:180px">
                    <div style="font-size:14px;color:#666"><?php echo htmlspecialchars($st['supplier_name']) ?></div>
                    <div style="font-weight:700;font-size:18px"><?php echo $st['voucher_count'] ?> vouchers</div>
                    <div style="font-size:12px;color:#888">Đã dùng: <?php echo $st['total_used'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
        <a class="small-btn primary" href="vouchers.php?action=add">+ Thêm voucher</a>
        <form method="get" action="vouchers.php" style="margin:0">
            <input type="text" name="q" placeholder="Tìm theo mã" value="<?php echo htmlspecialchars($search) ?>" style="padding:8px;border-radius:6px;border:1px solid #e6e9ef">
            <button class="small-btn" type="submit">Tìm</button>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Điều kiện</th>
                <th>Nhà cung cấp</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($vouchers as $v): ?>
            <tr>
                <td><?php echo $v['id'] ?></td>
                <td><?php echo htmlspecialchars($v['code']) ?></td>
                <td><?php echo htmlspecialchars($v['discount_type']) ?></td>
                <td><?php echo htmlspecialchars($v['discount_value']) ?></td>
                <td><?php echo htmlspecialchars($v['min_purchase']) ?></td>
                <td><?php
                    if (!empty($v['supplier_id'])) {
                        $s = $pdo->prepare('SELECT name FROM suppliers WHERE id = ? LIMIT 1'); $s->execute([$v['supplier_id']]); $sr = $s->fetch(PDO::FETCH_ASSOC);
                        echo htmlspecialchars($sr['name'] ?? '');
                    } else { echo '-'; }
                ?></td>
                <td><?php echo ($v['status'] ?? 1) ? 'Hoạt động' : 'Không hoạt động' ?> <a class="small-btn" href="vouchers.php?action=toggle&id=<?php echo $v['id'] ?>">Bật/Tắt</a></td>
                <td class="btn-row">
                    <a class="small-btn" href="vouchers.php?action=edit&id=<?php echo $v['id'] ?>">Sửa</a>
                    <a class="small-btn warn" href="vouchers.php?action=delete&id=<?php echo $v['id'] ?>" onclick="return confirm('Xóa voucher?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <h3 style="margin-top:0"><?php echo $action === 'edit' ? 'Sửa voucher' : 'Thêm voucher' ?></h3>
    <?php if (!empty($flash['message']) && $flash['type'] === 'error'): ?>
        <div class="flash error"><?php echo htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="code">Mã giảm giá</label>
            <input type="text" name="code" id="code" placeholder="Nhập mã giảm giá" value="<?php echo isset($voucher['code']) ? htmlspecialchars($voucher['code']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="discount_type">Loại giảm giá</label>
            <select name="discount_type" id="discount_type">
                <option value="fixed" <?php echo (isset($voucher['discount_type']) && $voucher['discount_type']=='fixed') ? 'selected' : '' ?>>Cố định</option>
                <option value="percent" <?php echo (isset($voucher['discount_type']) && $voucher['discount_type']=='percent') ? 'selected' : '' ?>>Theo phần trăm</option>
            </select>
        </div>

        <div class="form-group">
            <label for="discount_value">Giá trị giảm</label>
            <input type="number" name="discount_value" id="discount_value" placeholder="Nhập giá trị giảm" value="<?php echo isset($voucher['discount_value']) ? htmlspecialchars($voucher['discount_value']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="min_purchase">Giá trị tối thiểu</label>
            <input type="number" name="min_purchase" id="min_purchase" placeholder="Nhập giá trị tối thiểu" value="<?php echo isset($voucher['min_purchase']) ? htmlspecialchars($voucher['min_purchase']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="supplier_id">Nhà cung cấp (tuỳ chọn)</label>
            <select name="supplier_id" id="supplier_id">
                <option value="">-- Tất cả / Không --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?php echo $s['id'] ?>" <?php echo (isset($voucher['supplier_id']) && $voucher['supplier_id'] == $s['id']) ? 'selected' : '' ?>><?php echo htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="max_uses">Số lần tối đa</label>
            <input type="number" name="max_uses" id="max_uses" value="<?php echo isset($voucher['max_uses']) ? htmlspecialchars($voucher['max_uses']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="start_date">Ngày bắt đầu</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo isset($voucher['start_date']) ? htmlspecialchars($voucher['start_date']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="end_date">Ngày kết thúc</label>
            <input type="date" name="end_date" id="end_date" value="<?php echo isset($voucher['end_date']) ? htmlspecialchars($voucher['end_date']) : '' ?>">
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="status" <?php echo (isset($voucher['status']) && $voucher['status']) ? 'checked' : '' ?>> Hoạt động
            </label>
        </div>

        <div style="margin-top:12px">
            <button class="primary" type="submit" name="save_voucher">Lưu</button>
            <a class="small-btn" href="vouchers.php" style="margin-left:12px">Hủy</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/inc/footer.php'; ?>
