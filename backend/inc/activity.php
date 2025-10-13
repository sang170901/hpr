<?php
require_once __DIR__ . '/db.php';

function log_activity($user_id, $action, $model_type = null, $model_id = null, $changes = null){
    $pdo = getPDO();
    $stmt = $pdo->prepare('INSERT INTO activity_logs (user_id, action, model_type, model_id, changes, ip) VALUES (?, ?, ?, ?, ?, ?)');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
    $stmt->execute([$user_id, $action, $model_type, $model_id, $changes, $ip]);
}
