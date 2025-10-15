<?php include 'inc/header-new.php'; ?>

<section style="padding: 60px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: 700; color: #1e40af; margin-bottom: 1rem;">
            Danh mục vật liệu
        </h1>
        <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 2rem;">
            Trang đang chuyển hướng...
        </p>
        <p>
            <a href="materials.php" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                → Xem danh sách vật liệu
            </a>
        </p>
    </div>
</section>

<script>
// Auto redirect after 2 seconds
setTimeout(function() {
    window.location.href = 'materials.php';
}, 2000);
</script>

<?php include 'inc/footer-new.php'; ?>