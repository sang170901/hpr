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
            header('Location: sliders.php?msg=' . urlencode('Lưu thành công') . '&t=success'); 
            exit;
        } catch (Exception $e) { 
            $flash['type']='error'; 
            $flash['message']='Lỗi: '.$e->getMessage(); 
        }
    }
}

if ($action==='delete' && $id) { 
    $pdo->prepare('DELETE FROM sliders WHERE id=?')->execute([$id]); 
    log_activity($_SESSION['user']['id'] ?? null,'delete_slider','slider',$id,null);
    header('Location: sliders.php?msg=' . urlencode('Đã xóa') . '&t=success'); 
    exit; 
}

// Get slider data for AJAX (JSON)
if ($action === 'get' && $id) {
    header('Content-Type: application/json');
    try {
        $stmt = $pdo->prepare('SELECT * FROM sliders WHERE id = ?');
        $stmt->execute([$id]);
        $slider = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($slider) {
            echo json_encode(['success' => true, 'slider' => $slider]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy slider']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

require __DIR__ . '/inc/header.php';

// Get flash message from URL
if (isset($_GET['msg'])) {
    $flash['type'] = $_GET['t'] ?? 'success';
    $flash['message'] = $_GET['msg'];
}

$sliders = $pdo->query('SELECT * FROM sliders ORDER BY display_order ASC')->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2 class="page-main-title">Quản lý Banner/Slider</h2>
    
    <?php if(!empty($flash['message'])): ?>
        <div class="flash <?php echo $flash['type']==='success'?'success':'error' ?>">
            <?php echo htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>
    
    <button class="small-btn primary" onclick="openAddModal()">+ Thêm Slider</button>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Hình ảnh</th>
                <th>Link</th>
                <th>Thứ tự</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($sliders as $s): ?>
            <tr>
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
                <td class="btn-row">
                    <button class="small-btn" onclick="openEditModal(<?php echo $s['id'] ?>)">Sửa</button>
                    <a class="small-btn warn" href="sliders.php?action=delete&id=<?php echo $s['id'] ?>" onclick="return confirm('Xóa slider này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if(empty($sliders)): ?>
            <tr>
                <td colspan="8" style="text-align:center; color:#999; padding:40px;">
                    Chưa có slider nào. Nhấn "Thêm Slider" để bắt đầu.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm Slider -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 style="margin:0">Thêm Slider Mới</h3>
            <span class="modal-close" onclick="closeAddModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form method="post" id="addSliderForm">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label>Tiêu đề <span style="color:red">*</span>
                            <input type="text" name="title" id="add_title" required style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Phụ đề
                            <input type="text" name="subtitle" id="add_subtitle" style="width:100%">
                        </label>
                    </div>
                </div>

                <label style="margin-top:16px;">Mô tả ngắn
                    <textarea name="description" id="add_description" rows="3" style="width:100%"></textarea>
                </label>

                <label style="margin-top:16px;">URL Hình ảnh <span style="color:red">*</span>
                    <input type="text" name="image" id="add_image" required style="width:100%">
                </label>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
                    <div>
                        <label>Link đích
                            <input type="text" name="link" id="add_link" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Text nút Link
                            <input type="text" name="link_text" id="add_link_text" style="width:100%">
                        </label>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:16px">
                    <div>
                        <label>Thứ tự hiển thị
                            <input type="number" name="display_order" id="add_display_order" value="0" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Ngày bắt đầu
                            <input type="date" name="start_date" id="add_start_date" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Ngày kết thúc
                            <input type="date" name="end_date" id="add_end_date" style="width:100%">
                        </label>
                    </div>
                </div>

                <label style="margin-top:16px;display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="checkbox" name="status" id="add_status" checked>
                    <span>Kích hoạt</span>
                </label>

                <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end">
                    <button type="button" class="small-btn" onclick="closeAddModal()">Hủy</button>
                    <button type="submit" class="small-btn primary" name="save_slider">💾 Thêm slider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Slider -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 style="margin:0">Chỉnh Sửa Slider</h3>
            <span class="modal-close" onclick="closeEditModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form method="post" id="editSliderForm" action="sliders.php?action=edit&id=">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label>Tiêu đề <span style="color:red">*</span>
                            <input type="text" name="title" id="edit_title" required style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Phụ đề
                            <input type="text" name="subtitle" id="edit_subtitle" style="width:100%">
                        </label>
                    </div>
                </div>

                <label style="margin-top:16px;">Mô tả ngắn
                    <textarea name="description" id="edit_description" rows="3" style="width:100%"></textarea>
                </label>

                <label style="margin-top:16px;">URL Hình ảnh <span style="color:red">*</span>
                    <input type="text" name="image" id="edit_image" required style="width:100%">
                </label>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
                    <div>
                        <label>Link đích
                            <input type="text" name="link" id="edit_link" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Text nút Link
                            <input type="text" name="link_text" id="edit_link_text" style="width:100%">
                        </label>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:16px">
                    <div>
                        <label>Thứ tự hiển thị
                            <input type="number" name="display_order" id="edit_display_order" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Ngày bắt đầu
                            <input type="date" name="start_date" id="edit_start_date" style="width:100%">
                        </label>
                    </div>
                    <div>
                        <label>Ngày kết thúc
                            <input type="date" name="end_date" id="edit_end_date" style="width:100%">
                        </label>
                    </div>
                </div>

                <label style="margin-top:16px;display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="checkbox" name="status" id="edit_status">
                    <span>Kích hoạt</span>
                </label>

                <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end">
                    <button type="button" class="small-btn" onclick="closeEditModal()">Hủy</button>
                    <button type="submit" class="small-btn primary" name="save_slider">💾 Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    document.getElementById('addSliderForm').reset();
    document.getElementById('add_status').checked = true;
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openEditModal(sliderId) {
    document.getElementById('editModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    document.getElementById('editSliderForm').action = 'sliders.php?action=edit&id=' + sliderId;
    
    fetch('sliders.php?action=get&id=' + sliderId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const s = data.slider;
                document.getElementById('edit_title').value = s.title || '';
                document.getElementById('edit_subtitle').value = s.subtitle || '';
                document.getElementById('edit_description').value = s.description || '';
                document.getElementById('edit_image').value = s.image || '';
                document.getElementById('edit_link').value = s.link || '';
                document.getElementById('edit_link_text').value = s.link_text || '';
                document.getElementById('edit_display_order').value = s.display_order || 0;
                document.getElementById('edit_start_date').value = s.start_date || '';
                document.getElementById('edit_end_date').value = s.end_date || '';
                document.getElementById('edit_status').checked = s.status == 1;
            } else {
                alert('Không thể tải thông tin slider!');
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

window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    
    if (event.target == addModal) closeAddModal();
    if (event.target == editModal) closeEditModal();
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>
