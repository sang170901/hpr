<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/auth.php';
$pdo = getPDO();
require __DIR__ . '/inc/header.php';

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 50; $offset = ($page-1)*$per;
if ($q) {
    $stmt = $pdo->prepare('SELECT * FROM activity_logs WHERE action LIKE ? OR changes LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?');
    $like = "%$q%"; $stmt->execute([$like,$like,$per,$offset]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare('SELECT * FROM activity_logs ORDER BY id DESC LIMIT ? OFFSET ?'); $stmt->execute([$per,$offset]); $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="card"><h2 class="page-main-title">Activity Logs</h2>
<form method="get"><input type="text" name="q" placeholder="Search action or changes" value="<?php echo htmlspecialchars($q) ?>"><button class="small-btn">Tìm</button></form>
<table class="table"><thead><tr><th>ID</th><th>User</th><th>Action</th><th>Model</th><th>Changes</th><th>IP</th><th>Time</th></tr></thead><tbody>
<?php foreach($logs as $l): ?><tr>
<td><?php echo $l['id'] ?></td>
<td><?php echo htmlspecialchars($l['user_id']) ?></td>
<td><?php echo htmlspecialchars($l['action']) ?></td>
<td><?php echo htmlspecialchars($l['model_type']).' #'.htmlspecialchars($l['model_id']) ?></td>
<td style="max-width:400px;overflow:auto"><?php echo htmlspecialchars($l['changes']) ?></td>
<td><?php echo htmlspecialchars($l['ip']) ?></td>
<td><?php echo htmlspecialchars($l['created_at']) ?></td>
</tr><?php endforeach; ?></tbody></table>
<?php require __DIR__ . '/inc/footer.php'; ?>
