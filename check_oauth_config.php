<?php
/**
 * Check OAuth Configuration Status
 */

$config = require 'oauth_config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra cấu hình OAuth</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #2563eb;
            margin-bottom: 10px;
        }
        .status-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .status-card h2 {
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .status-icon {
            font-size: 1.5rem;
        }
        .status-ok {
            color: #10b981;
        }
        .status-error {
            color: #ef4444;
        }
        .config-item {
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .config-label {
            font-weight: 600;
            color: #334155;
        }
        .config-value {
            color: #64748b;
            font-family: monospace;
            font-size: 0.9rem;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-success {
            background: #dcfce7;
            color: #166534;
        }
        .badge-error {
            background: #fee2e2;
            color: #991b1b;
        }
        .action-btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }
        .warning-box {
            background: #fef3c7;
            border: 2px solid #fde68a;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }
        .warning-box strong {
            color: #92400e;
        }
        .success-box {
            background: #dcfce7;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }
        .success-box strong {
            color: #166534;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Kiểm tra cấu hình OAuth</h1>
        <p>Trạng thái cấu hình Google & Facebook Login</p>
    </div>

    <!-- Google OAuth Status -->
    <div class="status-card">
        <?php 
        $googleConfigured = $config['google']['client_id'] !== 'YOUR_GOOGLE_CLIENT_ID';
        ?>
        <h2>
            <i class="fab fa-google status-icon <?php echo $googleConfigured ? 'status-ok' : 'status-error'; ?>"></i>
            Google OAuth
            <span class="badge <?php echo $googleConfigured ? 'badge-success' : 'badge-error'; ?>">
                <?php echo $googleConfigured ? 'Đã cấu hình' : 'Chưa cấu hình'; ?>
            </span>
        </h2>
        
        <div class="config-item">
            <span class="config-label">Client ID:</span>
            <span class="config-value">
                <?php echo $googleConfigured ? substr($config['google']['client_id'], 0, 20) . '...' : '❌ Chưa có'; ?>
            </span>
        </div>
        
        <div class="config-item">
            <span class="config-label">Client Secret:</span>
            <span class="config-value">
                <?php echo $googleConfigured ? '✓ Đã có' : '❌ Chưa có'; ?>
            </span>
        </div>
        
        <div class="config-item">
            <span class="config-label">Redirect URI:</span>
            <span class="config-value"><?php echo $config['google']['redirect_uri']; ?></span>
        </div>
    </div>

    <!-- Facebook OAuth Status -->
    <div class="status-card">
        <?php 
        $facebookConfigured = $config['facebook']['app_id'] !== 'YOUR_FACEBOOK_APP_ID';
        ?>
        <h2>
            <i class="fab fa-facebook status-icon <?php echo $facebookConfigured ? 'status-ok' : 'status-error'; ?>"></i>
            Facebook OAuth
            <span class="badge <?php echo $facebookConfigured ? 'badge-success' : 'badge-error'; ?>">
                <?php echo $facebookConfigured ? 'Đã cấu hình' : 'Chưa cấu hình'; ?>
            </span>
        </h2>
        
        <div class="config-item">
            <span class="config-label">App ID:</span>
            <span class="config-value">
                <?php echo $facebookConfigured ? $config['facebook']['app_id'] : '❌ Chưa có'; ?>
            </span>
        </div>
        
        <div class="config-item">
            <span class="config-label">App Secret:</span>
            <span class="config-value">
                <?php echo $facebookConfigured ? '✓ Đã có' : '❌ Chưa có'; ?>
            </span>
        </div>
        
        <div class="config-item">
            <span class="config-label">Redirect URI:</span>
            <span class="config-value"><?php echo $config['facebook']['redirect_uri']; ?></span>
        </div>
    </div>

    <!-- Overall Status -->
    <?php if ($googleConfigured && $facebookConfigured): ?>
        <div class="success-box">
            <strong>✅ Tuyệt vời!</strong> OAuth đã được cấu hình đầy đủ. Bạn có thể sử dụng đăng nhập bằng Google và Facebook.
        </div>
        <a href="login.php" class="action-btn">
            <i class="fas fa-sign-in-alt"></i> Thử đăng nhập ngay
        </a>
    <?php else: ?>
        <div class="warning-box">
            <strong>⚠️ Cần cấu hình OAuth!</strong><br>
            Để sử dụng đăng nhập bằng Google/Facebook, bạn cần:
            <ol>
                <li>Đọc hướng dẫn trong file <code>OAUTH-SETUP-GUIDE.md</code></li>
                <li>Tạo ứng dụng trên Google Cloud Console và Facebook Developers</li>
                <li>Cập nhật Client ID/Secret vào file <code>oauth_config.php</code></li>
            </ol>
        </div>
        <a href="OAUTH-SETUP-GUIDE.md" target="_blank" class="action-btn" style="background: #f59e0b;">
            <i class="fas fa-book"></i> Xem hướng dẫn cấu hình
        </a>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 40px;">
        <a href="login.php" style="color: #64748b; text-decoration: none;">
            ← Quay lại trang đăng nhập
        </a>
    </div>
</body>
</html>

