<?php
require_once 'backend/inc/db.php';

try {
    $pdo = getPDO();

    // Đếm số vật tư
    $stmtMaterials = $pdo->query("SELECT COUNT(*) AS total_materials FROM products");
    $totalMaterials = $stmtMaterials->fetch(PDO::FETCH_ASSOC)['total_materials'];

    // Đếm số nhà cung cấp
    $stmtSuppliers = $pdo->query("SELECT COUNT(*) AS total_suppliers FROM suppliers WHERE status = 1");
    $totalSuppliers = $stmtSuppliers->fetch(PDO::FETCH_ASSOC)['total_suppliers'];

    // Đếm số danh mục vật tư
    $stmtCategories = $pdo->query("SELECT COUNT(*) AS total_categories FROM categories");
    $totalCategories = $stmtCategories->fetch(PDO::FETCH_ASSOC)['total_categories'];

    echo "Materials: $totalMaterials\n";
    echo "Suppliers: $totalSuppliers\n";
    echo "Categories: $totalCategories\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>