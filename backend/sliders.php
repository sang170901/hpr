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

// Toggle status (Bật/Tắt nhanh)
if ($action === 'toggle' && $id) {
    try {
        $stmt = $pdo->prepare('SELECT status FROM sliders WHERE id=?');
        $stmt->execute([$id]);
        $slider = $stmt->fetch();
        if ($slider) {
            $newStatus = $slider['status'] ? 0 : 1;
            $pdo->prepare('UPDATE sliders SET status=? WHERE id=?')->execute([$newStatus, $id]);
            log_activity($_SESSION['user']['id'] ?? null, 'toggle_slider', 'slider', $id, null);
            $msg = $newStatus ? 'Đã bật slider' : 'Đã tắt slider';
            header('Location: sliders.php?msg=' . urlencode($msg) . '&t=success'); 
        } else {
            header('Location: sliders.php?msg=' . urlencode('Không tìm thấy slider') . '&t=error'); 
        }
        exit;
    } catch (Exception $e) {
        header('Location: sliders.php?msg=' . urlencode('Lỗi: ' . $e->getMessage()) . '&t=error'); 
        exit;
    }
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

// Tính toán trạng thái hiển thị thực tế
$today = date('Y-m-d');
foreach ($sliders as &$slider) {
    $isActive = $slider['status'] == 1;
    $isInDateRange = true;
    
    if (!empty($slider['start_date']) && $slider['start_date'] > $today) {
        $isInDateRange = false;
    }
    if (!empty($slider['end_date']) && $slider['end_date'] < $today) {
        $isInDateRange = false;
    }
    
    $slider['is_displaying'] = $isActive && $isInDateRange;
}
?>

<div class="card">
    <h2 class="page-main-title">Quản lý Banner/Slider Trang Chủ</h2>
    
    <?php if(!empty($flash['message'])): ?>
        <div class="flash <?php echo $flash['type']==='success'?'success':'error' ?>">
            <?php echo htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>
    
    <div style="background: #e0f2fe; border-left: 4px solid #38bdf8; padding: 16px; margin-bottom: 20px; border-radius: 8px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <i class="fas fa-info-circle" style="color: #0284c7; font-size: 20px;"></i>
            <strong style="color: #0284c7; font-size: 16px;">Slider hiển thị trên trang chủ (index.php)</strong>
        </div>
        <p style="margin: 0; color: #0369a1; line-height: 1.6;">
            • Chỉ những slider có trạng thái "<span style="color: green; font-weight: 600;">✓ Hoạt động</span>" và trong khoảng thời gian hiển thị mới xuất hiện trên trang chủ<br>
            • Slider sẽ tự động chuyển đổi theo thứ tự đã cài đặt<br>
            • Nếu không cài thời gian, slider sẽ luôn hiển thị
        </p>
    </div>
    
    <?php
    $totalSliders = count($sliders);
    $activeSliders = array_filter($sliders, function($s) { return $s['is_displaying']; });
    $activeCount = count($activeSliders);
    ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; gap: 12px;">
            <button class="small-btn primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Thêm Slider
            </button>
            <a href="../index.php" target="_blank" class="small-btn" style="text-decoration: none;">
                <i class="fas fa-eye"></i> Xem trang chủ
            </a>
        </div>
        
        <div style="display: flex; gap: 16px; align-items: center;">
            <div style="padding: 12px 20px; background: #f0fdf4; border-radius: 12px; border: 2px solid #86efac;">
                <div style="font-size: 12px; color: #059669; margin-bottom: 4px;">Đang hiển thị</div>
                <div style="font-size: 24px; font-weight: 800; color: #059669;"><?php echo $activeCount; ?></div>
            </div>
            <div style="padding: 12px 20px; background: #f8fafc; border-radius: 12px; border: 2px solid #cbd5e1;">
                <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Tổng số</div>
                <div style="font-size: 24px; font-weight: 800; color: #475569;"><?php echo $totalSliders; ?></div>
            </div>
        </div>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Hình ảnh</th>
                <th>Thứ tự</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th style="background: #f0f9ff;">🏠 Trang chủ</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($sliders as $s): ?>
            <tr style="<?php echo $s['is_displaying'] ? 'background: #f0fdf4;' : '' ?>">
<td><strong><?php echo $s['id'] ?></strong></td>
<td>
    <strong style="<?php echo $s['is_displaying'] ? 'color: #059669;' : '' ?>">
        <?php echo htmlspecialchars($s['title']) ?>
    </strong>
    <?php if(!empty($s['subtitle'])): ?>
        <br><small style="color: #64748b;"><?php echo htmlspecialchars($s['subtitle']) ?></small>
    <?php endif; ?>
    <?php if(!empty($s['link'])): ?>
        <br><a href="<?php echo htmlspecialchars($s['link']) ?>" target="_blank" style="font-size: 12px; color: #38bdf8;">
            <i class="fas fa-external-link-alt"></i> <?php echo htmlspecialchars($s['link_text'] ?? 'Link') ?>
        </a>
    <?php endif; ?>
</td>
<td>
    <?php if($s['image']): ?>
        <img src="../<?php echo htmlspecialchars($s['image']) ?>" 
             style="height: 60px; width: 100px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer;" 
             onerror="this.style.display='none'"
             onclick="window.open('../<?php echo htmlspecialchars($s['image']) ?>', '_blank')"
             title="Click để xem ảnh lớn">
    <?php else: ?>
        <span style="color: #999; font-size: 12px;">Chưa có ảnh</span>
    <?php endif; ?>
</td>
<td><span style="font-weight: 600; color: #38bdf8; font-size: 16px;"><?php echo $s['display_order'] ?></span></td>
<td style="font-size: 13px;">
    <?php if($s['start_date'] || $s['end_date']): ?>
        <div style="line-height: 1.6;">
            <div><strong>Từ:</strong> <?php echo $s['start_date'] ? date('d/m/Y', strtotime($s['start_date'])) : '<span style="color:#10b981;">∞ Không giới hạn</span>' ?></div>
            <div><strong>Đến:</strong> <?php echo $s['end_date'] ? date('d/m/Y', strtotime($s['end_date'])) : '<span style="color:#10b981;">∞ Không giới hạn</span>' ?></div>
        </div>
    <?php else: ?>
        <span style="color:#10b981; font-weight: 600;">∞ Luôn hiển thị</span>
    <?php endif; ?>
</td>
<td>
    <?php if($s['status']): ?>
        <span style="color:green; font-weight: 600; padding: 6px 12px; background: #f0fdf4; border-radius: 6px; border: 2px solid #86efac; display: inline-block;">
            ✓ Hoạt động
        </span>
    <?php else: ?>
        <span style="color:red; font-weight: 600; padding: 6px 12px; background: #fef2f2; border-radius: 6px; border: 2px solid #fca5a5; display: inline-block;">
            ✗ Tạm dừng
        </span>
    <?php endif; ?>
</td>
<td style="text-align: center; background: <?php echo $s['is_displaying'] ? '#dcfce7' : '#fef2f2' ?>;">
    <?php if($s['is_displaying']): ?>
        <div style="display: inline-flex; align-items: center; gap: 6px; background: #059669; color: white; padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 13px;">
            <i class="fas fa-check-circle"></i>
            <span>Đang hiển thị</span>
        </div>
    <?php else: ?>
        <div style="display: inline-flex; align-items: center; gap: 6px; background: #dc2626; color: white; padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 13px;">
            <i class="fas fa-times-circle"></i>
            <span>Không hiển thị</span>
        </div>
        <?php if($s['status'] == 0): ?>
            <div style="font-size: 11px; color: #dc2626; margin-top: 4px;">Đã tắt</div>
        <?php elseif(!empty($s['start_date']) && $s['start_date'] > $today): ?>
            <div style="font-size: 11px; color: #f59e0b; margin-top: 4px;">Chưa đến ngày</div>
        <?php elseif(!empty($s['end_date']) && $s['end_date'] < $today): ?>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Đã hết hạn</div>
        <?php endif; ?>
    <?php endif; ?>
</td>
                <td class="btn-row">
                    <button class="small-btn" onclick="openEditModal(<?php echo $s['id'] ?>)">
                        <i class="fas fa-edit"></i> Sửa
                    </button>
                    <a class="small-btn warn" href="sliders.php?action=delete&id=<?php echo $s['id'] ?>" onclick="return confirm('Xóa slider này?')">
                        <i class="fas fa-trash"></i> Xóa
                    </a>
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
