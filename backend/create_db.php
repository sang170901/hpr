<?php
require __DIR__ . '/inc/db.php';
$config = require __DIR__ . '/config.php';
$pdo = getPDO();

echo "Database ensured and admin user seeded (if missing).\n";
echo "Login: {$config['admin_email']} / {$config['admin_password']}\n";