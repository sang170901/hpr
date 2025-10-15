<?php
/**
 * Initialize the SQLite database schema and seed admin user.
 * This file provides init_db(PDO $pdo, array $config) and does NOT echo by default.
 */
function init_db(PDO $pdo, array $config){
    // Enable foreign keys (if needed)
    $pdo->exec('PRAGMA foreign_keys = ON');

    // Create tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT UNIQUE,
        password TEXT,
        role TEXT DEFAULT 'user',
        status INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS suppliers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        slug TEXT,
        email TEXT,
        phone TEXT,
        address TEXT,
        logo TEXT,
        description TEXT,
        status INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        slug TEXT,
        description TEXT,
        price REAL,
        status INTEGER DEFAULT 1,
        featured INTEGER DEFAULT 0,
        images TEXT,
        supplier_id INTEGER,
        classification TEXT, -- New column for product classification
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS vouchers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT,
        discount_type TEXT,
        discount_value REAL,
        min_purchase REAL,
        max_uses INTEGER,
        used_count INTEGER DEFAULT 0,
        start_date DATETIME,
        end_date DATETIME,
        supplier_id INTEGER,
        status INTEGER DEFAULT 1
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS sliders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT,
        image TEXT,
        link TEXT,
        start_date DATETIME,
        end_date DATETIME,
        status INTEGER DEFAULT 1,
        display_order INTEGER DEFAULT 0
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS scheduled_publishings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        model_type TEXT,
        model_id INTEGER,
        publish_at DATETIME,
        status TEXT DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        action TEXT,
        model_type TEXT,
        model_id INTEGER,
        changes TEXT,
        ip TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed admin user
    $hash = password_hash($config['admin_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 1)");
    $stmt->execute(['Admin', $config['admin_email'], $hash]);

    // Update existing products to include classifications
    $pdo->exec("UPDATE products SET classification = 'Vật liệu' WHERE id IN (1, 2)");
    $pdo->exec("UPDATE products SET classification = 'Thiết Bị' WHERE id = 3");
    $pdo->exec("UPDATE products SET classification = 'Công nghệ' WHERE id = 4");
    $pdo->exec("UPDATE products SET classification = 'Cảnh quan' WHERE id = 5");
}
