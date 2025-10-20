<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=vnmt_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('DESCRIBE suppliers');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Cột trong bảng suppliers:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>