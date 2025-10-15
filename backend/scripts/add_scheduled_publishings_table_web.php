<?php
header('Content-Type: text/plain; charset=utf-8');
try {
    require __DIR__ . '/../../backend/inc/db.php';
    $pdo = getPDO();
    $cols = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='scheduled_publishings'")->fetchAll(PDO::FETCH_ASSOC);
    if ($cols) { echo "scheduled_publishings table already exists.\n"; exit; }
    $pdo->exec("CREATE TABLE scheduled_publishings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        model_type TEXT,
        model_id INTEGER,
        publish_at DATETIME,
        status TEXT DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Created scheduled_publishings table.\n";
} catch (Exception $e) { echo 'Error: '.$e->getMessage(); }
