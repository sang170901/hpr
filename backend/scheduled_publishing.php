<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/activity.php';
$pdo = getPDO();

// Create scheduled_publishings table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS scheduled_publishings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    model_type TEXT,
    model_id INTEGER,
    publish_at DATETIME,
    status TEXT DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flash = ['type'=>'','message'=>''];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['save_sched'])) {
        $model_type = trim($_POST['model_type'] ?? '');
        $model_id = (int)($_POST['model_id'] ?? 0);
        $publish_at = trim($_POST['publish_at'] ?? null);
        try {
            if ($id) {
                $pdo->prepare('UPDATE scheduled_publishings SET model_type=?,model_id=?,publish_at=? WHERE id=?')
                    ->execute([$model_type,$model_id,$publish_at,$id]);
                log_activity($_SESSION['user']['id'] ?? null,'update_sched','scheduled',$id,null);
            } else {
                $pdo->prepare('INSERT INTO scheduled_publishings (model_type,model_id,publish_at) VALUES (?,?,?)')
                    ->execute([$model_type,$model_id,$publish_at]);
                $newId = $pdo->lastInsertId();
                log_activity($_SESSION['user']['id'] ?? null,'create_sched','scheduled',$newId,null);
            }
            header('Location: scheduled_publishing.php?msg=' . urlencode('Đã lưu thành công') . '&t=success'); exit;
        } catch (Exception $e) { $flash['type']='error'; $flash['message']='Lỗi: '.$e->getMessage(); }
    }
}
if ($action==='delete' && $id) { $pdo->prepare('DELETE FROM scheduled_publishings WHERE id=?')->execute([$id]); header('Location: scheduled_publishing.php?msg=' . urlencode('Đã xóa thành công') . '&t=success'); exit; }

require __DIR__ . '/inc/header.php';
$schedules = $pdo->query('SELECT * FROM scheduled_publishings ORDER BY publish_at ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card"><h2>Lịch xuất bản</h2>
<?php if(!empty($flash['message'])): ?><div class="flash <?php echo $flash['type']==='success'?'success':'error' ?>"><?php echo htmlspecialchars($flash['message']) ?></div><?php endif; ?>
<a class="small-btn primary" href="scheduled_publishing.php?action=add">+ Thêm lịch xuất bản</a>
<table class="table"><thead><tr><th>ID</th><th>Loại</th><th>ID Mục</th><th>Thời gian xuất bản</th><th>Trạng thái</th><th>Hành động</th></tr></thead><tbody>
<?php foreach($schedules as $r): ?><tr>
<td><?php echo $r['id'] ?></td>
<td><?php echo htmlspecialchars($r['model_type']) ?></td>
<td><?php echo htmlspecialchars($r['model_id']) ?></td>
<td><?php echo htmlspecialchars($r['publish_at']) ?></td>
<td><?php echo htmlspecialchars($r['status'] ?? 'pending') ?></td>
<td class="btn-row"><a class="small-btn" href="scheduled_publishing.php?action=edit&id=<?php echo $r['id'] ?>">Sửa</a> <a class="small-btn warn" href="scheduled_publishing.php?action=delete&id=<?php echo $r['id'] ?>" onclick="return confirm('Xóa lịch này?')">Xóa</a></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php if($action==='add' || $action==='edit'): ?>
<div class="card"><h3><?php echo $action==='edit'?'Sửa lịch xuất bản':'Thêm lịch xuất bản' ?></h3>
<form method="post">
<label>Loại nội dung<input type="text" name="model_type" placeholder="VD: product, article" value="<?php echo $action==='edit'?htmlspecialchars($r['model_type'] ?? ''):'' ?>"></label>
<label>ID nội dung<input type="number" name="model_id" placeholder="ID của nội dung cần xuất bản" value="<?php echo $action==='edit'?htmlspecialchars($r['model_id'] ?? ''):'' ?>"></label>
<label>Thời gian xuất bản<input type="datetime-local" name="publish_at" value="<?php echo $action==='edit'?htmlspecialchars($r['publish_at'] ?? ''):'' ?>"></label>
<div style="margin-top:12px"><button class="primary" type="submit" name="save_sched">Lưu</button> <a class="small-btn" href="scheduled_publishing.php">Hủy</a></div>
</form></div>
<?php endif; ?>
<?php require __DIR__ . '/inc/footer.php'; ?>
