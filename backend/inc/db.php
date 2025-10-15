<?php
$config = require __DIR__ . '/../config.php';
require_once __DIR__ . '/init_db.php';

function getPDO(){
    global $config;
    $path = $config['db_path'];
    // Ensure directory exists
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // If users table missing, initialize DB schema
    try {
        $res = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetchColumn();
        if (!$res) {
            init_db($pdo, $config);
        }
    } catch (Exception $e) {
        // If something goes wrong, rethrow so callers can see it
        throw $e;
    }

    return $pdo;
}
