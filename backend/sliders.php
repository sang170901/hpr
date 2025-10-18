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
        $subtitle = trim($_POST['subtitle'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $link_text = trim($_POST['link_text'] ?? '');
        $start_date = trim($_POST['start_date'] ?? null);
        $end_date = trim($_POST['end_date'] ?? null);
        $status = isset($_POST['status']) ? 1 : 0;
        $order = (int)($_POST['display_order'] ?? 0);
        try {
            if ($id) {
                $pdo->prepare('UPDATE sliders SET title=?,subtitle=?,description=?,image=?,link=?,link_text=?,start_date=?,end_date=?,status=?,display_order=? WHERE id=?')
                    ->execute([$title,$subtitle,$description,$image,$link,$link_text,$start_date,$end_date,$status,$order,$id]);
                log_activity($_SESSION['user']['id'] ?? null,'update_slider','slider',$id,null);
            } else {
                $pdo->prepare('INSERT INTO sliders (title,subtitle,description,image,link,link_text,start_date,end_date,status,display_order) VALUES (?,?,?,?,?,?,?,?,?,?)')
                    ->execute([$title,$subtitle,$description,$image,$link,$link_text,$start_date,$end_date,$status,$order]);
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
<table class="table"><thead><tr><th>ID</th><th>Tiêu đề</th><th>Hình ảnh</th><th>Link</th><th>Thứ tự</th><th>Thời gian</th><th>Trạng thái</th><th>Hành động</th></tr></thead><tbody>
<?php foreach($sliders as $s): ?><tr>
<td><?php echo $s['id'] ?></td>
<td>
    <strong><?php echo htmlspecialchars($s['title']) ?></strong>
    <?php if(!empty($s['subtitle'])): ?><br><small><?php echo htmlspecialchars($s['subtitle']) ?></small><?php endif; ?>
</td>
<td><?php echo $s['image'] ? '<img src="../'.htmlspecialchars($s['image']).'" style="height:50px;border-radius:4px;" onerror="this.style.display=\'none\'">':'' ?></td>
<td>
    <?php if(!empty($s['link'])): ?>
        <a href="<?php echo htmlspecialchars($s['link']) ?>" target="_blank"><?php echo htmlspecialchars($s['link_text'] ?? 'Link') ?></a>
    <?php else: ?>
        <span style="color:#999;">Không có</span>
    <?php endif; ?>
</td>
<td><?php echo $s['display_order'] ?></td>
<td>
    <?php if($s['start_date'] || $s['end_date']): ?>
        <?php echo $s['start_date'] ? date('d/m/Y', strtotime($s['start_date'])) : '∞' ?>
        -
        <?php echo $s['end_date'] ? date('d/m/Y', strtotime($s['end_date'])) : '∞' ?>
    <?php else: ?>
        <span style="color:#999;">Luôn hiển thị</span>
    <?php endif; ?>
</td>
<td><?php echo $s['status'] ? '<span style="color:green;">✓ Hoạt động</span>':'<span style="color:red;">✗ Tạm dừng</span>' ?></td>
<td class="btn-row"><a class="small-btn" href="sliders.php?action=edit&id=<?php echo $s['id'] ?>">Edit</a> <a class="small-btn warn" href="sliders.php?action=delete&id=<?php echo $s['id'] ?>" onclick="return confirm('Delete slider?')">Delete</a></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php if($action==='add' || $action==='edit'): 
if($action==='edit' && $id) {
    $editSlider = $pdo->prepare('SELECT * FROM sliders WHERE id=?');
    $editSlider->execute([$id]);
    $s = $editSlider->fetch(PDO::FETCH_ASSOC);
}
?>
<div class="card"><h3><?php echo $action==='edit'?'Edit Slider':'Add Slider' ?></h3>
<form method="post">
<label>Tiêu đề *<input type="text" name="title" value="<?php echo $action==='edit'?htmlspecialchars($s['title'] ?? ''):'' ?>" required></label>
<label>Phụ đề<input type="text" name="subtitle" value="<?php echo $action==='edit'?htmlspecialchars($s['subtitle'] ?? ''):'' ?>"></label>
<label>Mô tả ngắn<textarea name="description" rows="3"><?php echo $action==='edit'?htmlspecialchars($s['description'] ?? ''):'' ?></textarea></label>
<label>URL Hình ảnh *<input type="text" name="image" value="<?php echo $action==='edit'?htmlspecialchars($s['image'] ?? ''):'' ?>" required></label>
<label>Link đích<input type="text" name="link" value="<?php echo $action==='edit'?htmlspecialchars($s['link'] ?? ''):'' ?>"></label>
<label>Text nút Link<input type="text" name="link_text" value="<?php echo $action==='edit'?htmlspecialchars($s['link_text'] ?? ''):'' ?>"></label>
<label>Thứ tự hiển thị<input type="number" name="display_order" value="<?php echo $action==='edit'?htmlspecialchars($s['display_order'] ?? 0):0 ?>"></label>
<label>Ngày bắt đầu<input type="date" name="start_date" value="<?php echo $action==='edit'?htmlspecialchars($s['start_date'] ?? ''):'' ?>"></label>
<label>Ngày kết thúc<input type="date" name="end_date" value="<?php echo $action==='edit'?htmlspecialchars($s['end_date'] ?? ''):'' ?>"></label>
<label><input type="checkbox" name="status" <?php echo ($s['status'] ?? 1)?'checked':'' ?>> Kích hoạt</label>
<div style="margin-top:12px"><button class="primary" type="submit" name="save_slider">Lưu</button> <a class="small-btn" href="sliders.php">Hủy</a></div>
</form></div>
<?php endif; ?>
<?php require __DIR__ . '/inc/footer.php'; ?>
