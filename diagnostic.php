<?php
/**
 * Diagnostic tool for VNMT project connections
 */

echo "🔍 VNMT PROJECT CONNECTION DIAGNOSTIC\n";
echo str_repeat("=", 50) . "\n\n";

// 1. PHP Environment
echo "1. 🔧 PHP ENVIRONMENT\n";
echo "   Version: " . PHP_VERSION . "\n";
echo "   SAPI: " . PHP_SAPI . "\n";
echo "   OS: " . PHP_OS . "\n\n";

// 2. PHP Configuration
echo "2. ⚙️ PHP CONFIGURATION\n";
$iniFile = php_ini_loaded_file();
echo "   Config file: " . ($iniFile ?: "None") . "\n";
echo "   Memory limit: " . ini_get('memory_limit') . "\n";
echo "   Max execution time: " . ini_get('max_execution_time') . "s\n\n";

// 3. Available Extensions
echo "3. 📦 AVAILABLE EXTENSIONS\n";
$extensions = get_loaded_extensions();
$requiredExtensions = ['PDO', 'pdo_sqlite', 'sqlite3', 'pdo_mysql', 'json', 'session'];

foreach ($requiredExtensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "   {$status} {$ext}\n";
}

echo "\n   📋 All loaded extensions (" . count($extensions) . "):\n";
echo "   " . implode(', ', $extensions) . "\n\n";

// 4. PDO Drivers
echo "4. 🗄️ PDO DRIVERS\n";
if (extension_loaded('PDO')) {
    $drivers = PDO::getAvailableDrivers();
    echo "   Available drivers: " . implode(', ', $drivers) . "\n";
    
    $requiredDrivers = ['sqlite', 'mysql'];
    foreach ($requiredDrivers as $driver) {
        $status = in_array($driver, $drivers) ? "✅" : "❌";
        echo "   {$status} {$driver}\n";
    }
} else {
    echo "   ❌ PDO extension not loaded\n";
}
echo "\n";

// 5. Project Structure
echo "5. 📁 PROJECT STRUCTURE\n";
$projectRoot = __DIR__;
$importantFiles = [
    'backend/config.php',
    'backend/inc/db.php',
    'backend/inc/init_db.php',
    'backend/database.sqlite',
    'index.php',
    'backend/index.php'
];

foreach ($importantFiles as $file) {
    $fullPath = $projectRoot . '/' . $file;
    $status = file_exists($fullPath) ? "✅" : "❌";
    $size = file_exists($fullPath) ? " (" . filesize($fullPath) . " bytes)" : "";
    echo "   {$status} {$file}{$size}\n";
}
echo "\n";

// 6. Database Connection Test
echo "6. 🔌 DATABASE CONNECTION TEST\n";
try {
    if (file_exists('backend/inc/db.php')) {
        require_once 'backend/inc/db.php';
        
        if (function_exists('getPDO')) {
            $pdo = getPDO();
            echo "   ✅ Database connection successful\n";
            
            // Check tables
            $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
            echo "   📊 Tables found: " . count($tables) . "\n";
            
            if (!empty($tables)) {
                echo "   📋 Table list: " . implode(', ', $tables) . "\n";
                
                // Count records
                foreach ($tables as $table) {
                    try {
                        $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
                        echo "     - {$table}: {$count} records\n";
                    } catch (Exception $e) {
                        echo "     - {$table}: Error counting records\n";
                    }
                }
            }
            
        } else {
            echo "   ❌ getPDO function not found\n";
        }
    } else {
        echo "   ❌ Database connection file not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Web Server Test
echo "7. 🌐 WEB SERVER STATUS\n";
$serverVars = ['SERVER_SOFTWARE', 'DOCUMENT_ROOT', 'SERVER_NAME', 'REQUEST_URI'];
foreach ($serverVars as $var) {
    $value = $_SERVER[$var] ?? 'Not set';
    echo "   {$var}: {$value}\n";
}
echo "\n";

// 8. Recommendations
echo "8. 💡 RECOMMENDATIONS\n";
if (!extension_loaded('pdo_sqlite')) {
    echo "   🔧 Enable SQLite in php.ini:\n";
    echo "      - Uncomment: extension=pdo_sqlite\n";
    echo "      - Uncomment: extension=sqlite3\n";
    echo "      - Restart web server\n";
}

if (!file_exists('backend/database.sqlite')) {
    echo "   🗄️ Database file will be created on first connection\n";
}

echo "   📖 Check CONNECTION-STATUS-REPORT.md for detailed analysis\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 DIAGNOSTIC COMPLETE\n";
?>