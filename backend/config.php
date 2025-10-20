<?php
// Simple config for backend
return [
    // MySQL database settings for XAMPP
    'db_host' => 'localhost',
    'db_name' => 'vnmt_db',
    'db_user' => 'root',
    'db_password' => '',
    
    // Legacy SQLite path (not used anymore)
    'db_path' => __DIR__ . '/database.sqlite',
    
    // Admin settings
    'admin_email' => 'admin@vnmt.com',
    'admin_password' => 'admin123', // plaintext for prototype only
];