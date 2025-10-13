<?php
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/activity.php';
$pdo = getPDO();

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flash = ['type'=>'','message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_slider'])) {
        $title = trim($_POST['title'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $start_date = trim($_POST['start_date'] ?? null);
        $end_date = trim($_POST['end_date'] ?? null);
        $status = isset($_POST['status']) ? 1 : 0;
        $order = (int)($_POST['display_order'] ?? 0);
        try {
            if ($id) {
                $pdo->prepare('UPDATE sliders SET title=?,image=?,link=?,start_date=?,end_date=?,status=?,display_order=? WHERE id=?')
                    ->execute([$title,$image,$link,$start_date,$end_date,$status,$order,$id]);
                log_activity($_SESSION['user']['id'] ?? null,'update_slider','slider',$id,null);
            } else {
                $pdo->prepare('INSERT INTO sliders (title,image,link,start_date,end_date,status,display_order) VALUES (?,?,?,?,?,?,?)')
                    ->execute([$title,$image,$link,$start_date,$end_date,$status,$order]);
                $newId = $pdo->lastInsertId();
                log_activity($_SESSION['user']['id'] ?? null,'create_slider','slider',$newId,null);
            }
            header('Location: sliders.php?msg=' . urlencode('Saved') . '&t=success'); exit;
        } catch (Exception $e) { $flash['type']='error'; $flash['message']='Lỗi: '.$e->getMessage(); }
    }
}
if ($action==='delete' && $id) { $pdo->prepare('DELETE FROM sliders WHERE id=?')->execute([$id]); header('Location: sliders.php?msg=' . urlencode('Deleted') . '&t=success'); exit; }

require __DIR__ . '/inc/header.php';
$sliders = $pdo->query('SELECT * FROM sliders ORDER BY display_order ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card"><h2>Manage Sliders</h2>
<?php if(!empty($flash['message'])): ?><div class="flash <?php echo $flash['type']==='success'?'success':'error' ?>"><?php echo htmlspecialchars($flash['message']) ?></div><?php endif; ?>
<a class="small-btn primary" href="sliders.php?action=add">+ Add Slider</a>
<table class="table"><thead><tr><th>ID</th><th>Title</th><th>Image</th><th>Link</th><th>Order</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach($sliders as $s): ?><tr>
<td><?php echo $s['id'] ?></td>
<td><?php echo htmlspecialchars($s['title']) ?></td>
<td><?php echo $s['image'] ? '<img src="'.htmlspecialchars($s['image']).'" style="height:40px">':'' ?></td>
<td><?php echo htmlspecialchars($s['link']) ?></td>
<td><?php echo $s['display_order'] ?></td>
<td><?php echo $s['status'] ? 'Active':'Inactive' ?></td>
<td class="btn-row"><a class="small-btn" href="sliders.php?action=edit&id=<?php echo $s['id'] ?>">Edit</a> <a class="small-btn warn" href="sliders.php?action=delete&id=<?php echo $s['id'] ?>" onclick="return confirm('Delete slider?')">Delete</a></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php if($action==='add' || $action==='edit'): ?>
<div class="card"><h3><?php echo $action==='edit'?'Edit Slider':'Add Slider' ?></h3>
<form method="post">
<label>Title<input type="text" name="title" value="<?php echo $action==='edit'?htmlspecialchars($s['title'] ?? ''):'' ?>"></label>
<label>Image URL<input type="text" name="image" value="<?php echo $action==='edit'?htmlspecialchars($s['image'] ?? ''):'' ?>"></label>
<label>Link<input type="text" name="link" value="<?php echo $action==='edit'?htmlspecialchars($s['link'] ?? ''):'' ?>"></label>
<label>Order<input type="number" name="display_order" value="<?php echo $action==='edit'?htmlspecialchars($s['display_order'] ?? 0):0 ?>"></label>
<label><input type="checkbox" name="status" <?php echo ($s['status'] ?? 1)?'checked':'' ?>> Active</label>
<div style="margin-top:12px"><button class="primary" type="submit" name="save_slider">Save</button> <a class="small-btn" href="sliders.php">Cancel</a></div>
</form></div>
<?php endif; ?>
<?php require __DIR__ . '/inc/footer.php'; ?>
