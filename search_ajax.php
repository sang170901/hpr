<?php
header('Content-Type: application/json');
require_once 'backend/inc/db.php';

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode([]);
    exit;
}

$query = trim($_GET['q']);

try {
    $pdo = getPDO();
    
    // Tìm kiếm sản phẩm theo tên
    $stmt = $pdo->prepare("
        SELECT id, name, category 
        FROM products 
        WHERE name LIKE :query 
        ORDER BY 
            CASE 
                WHEN name LIKE :exact_query THEN 1
                WHEN name LIKE :start_query THEN 2
                ELSE 3
            END,
            name ASC
        LIMIT 8
    ");
    
    $exactQuery = $query;
    $startQuery = $query . '%';
    $likeQuery = '%' . $query . '%';
    
    $stmt->bindParam(':query', $likeQuery);
    $stmt->bindParam(':exact_query', $exactQuery);
    $stmt->bindParam(':start_query', $startQuery);
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
    
} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    echo json_encode([]);
}
?>