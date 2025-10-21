<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/auth.php';
$pdo = getPDO();
require __DIR__ . '/inc/activity.php';

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flash = ['type' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_user'])) {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        try {
            if ($id && !empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, password=?, role=? WHERE id=?');
                $stmt->execute([$name, $email, $hash, $role, $id]);
                log_activity($_SESSION['user']['id'] ?? null, 'update_user', 'user', $id, json_encode(['name'=>$name,'email'=>$email]));
            } elseif ($id) {
                $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, role=? WHERE id=?');
                $stmt->execute([$name, $email, $role, $id]);
                log_activity($_SESSION['user']['id'] ?? null, 'update_user', 'user', $id, json_encode(['name'=>$name,'email'=>$email]));
            } else {
                $hash = password_hash($password ?: 'changeme', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 1)');
                $stmt->execute([$name, $email, $hash, $role]);
                $newId = $pdo->lastInsertId();
                log_activity($_SESSION['user']['id'] ?? null, 'create_user', 'user', $newId, json_encode(['name'=>$name,'email'=>$email]));
            }
            $flash['type'] = 'success';
            $flash['message'] = 'Saved successfully.';
            // refresh to show list & message
            header('Location: users.php?msg=' . urlencode($flash['message']) . '&t=success');
            exit;
        } catch (PDOException $e) {
            // handle unique constraint (email) for SQLite: SQLSTATE[23000]
            if ($e->getCode() === '23000' || strpos($e->getMessage(), 'UNIQUE') !== false) {
                $flash['type'] = 'error';
                $flash['message'] = 'Email đã tồn tại.';
            } else {
                $flash['type'] = 'error';
                $flash['message'] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
            }
        }
    }
}

// Search
$search = trim($_GET['q'] ?? '');

if ($action === 'delete' && $id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        // log activity
        log_activity($_SESSION['user']['id'] ?? null, 'delete_user', 'user', $id, null);
        header('Location: users.php?msg=' . urlencode('Deleted') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể xóa: ' . $e->getMessage();
    }
}

// Toggle active/inactive
if ($action === 'toggle' && $id) {
    try {
        $stmt = $pdo->prepare('UPDATE users SET status = 1 - status WHERE id = ?');
        $stmt->execute([$id]);
        log_activity($_SESSION['user']['id'] ?? null, 'toggle_user_status', 'user', $id, null);
        header('Location: users.php?msg=' . urlencode('Updated') . '&t=success');
        exit;
    } catch (PDOException $e) {
        $flash['type'] = 'error';
        $flash['message'] = 'Không thể thay đổi trạng thái: ' . $e->getMessage();
    }
}

require __DIR__ . '/inc/header.php';

// Show flash message from redirect
if (isset($_GET['msg'])) {
    $flash['message'] = urldecode($_GET['msg']);
    $flash['type'] = $_GET['t'] ?? 'success';
}

if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch users (with optional search)
if (!empty($search)) {
    $stmt = $pdo->prepare('SELECT id, name, email, role, status, created_at FROM users WHERE email LIKE ? OR name LIKE ? ORDER BY id DESC');
    $like = "%$search%";
    $stmt->execute([$like, $like]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $users = $pdo->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
}

?>
<div class="card">
    <h2 class="page-main-title">Users</h2>
    <?php if (!empty($flash['message'])): ?>
        <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error' ?>"><?php echo htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
        <a class="small-btn primary" href="users.php?action=add">+ Add User</a>
        <form method="get" action="users.php" style="margin:0">
            <input type="text" name="q" placeholder="Tìm email hoặc tên" value="<?php echo htmlspecialchars($search ?? '') ?>" style="padding:8px;border-radius:6px;border:1px solid #e6e9ef">
            <button class="small-btn" type="submit">Tìm</button>
        </form>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['id'] ?></td>
                <td><?php echo htmlspecialchars($u['name']) ?></td>
                <td><?php echo htmlspecialchars($u['email']) ?></td>
                <td><?php echo $u['role'] ?></td>
                <td><?php echo $u['status'] ? 'Active' : 'Inactive' ?> <a class="small-btn" href="users.php?action=toggle&id=<?php echo $u['id'] ?>">Toggle</a></td>
                <td><?php echo $u['created_at'] ?></td>
                <td class="btn-row">
                    <a class="small-btn" href="users.php?action=edit&id=<?php echo $u['id'] ?>">Edit</a>
                    <a class="small-btn warn" href="users.php?action=delete&id=<?php echo $u['id'] ?>" onclick="return confirm('Delete user?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <h3 style="margin-top:0"><?php echo $action === 'edit' ? 'Edit User' : 'Add User' ?></h3>
    <?php if (!empty($flash['message']) && $flash['type'] === 'error'): ?>
        <div class="flash error"><?php echo htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Name
            <input type="text" name="name" required value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : '' ?>">
        </label>
        <label>Email
            <input type="email" name="email" required value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>">
        </label>
        <label>Role
            <select name="role">
                <option value="user" <?php echo (isset($user['role']) && $user['role'] === 'user') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?php echo (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
        </label>
        <label>Password (leave blank to keep)
            <input type="password" name="password">
        </label>
        <div style="margin-top:12px">
            <button class="primary" type="submit" name="save_user">Save</button>
            <a class="small-btn" href="users.php" style="margin-left:12px">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/inc/footer.php'; ?>
