<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

try {
    $sql = "SELECT 
                e.id,
                e.amount,
                e.description,
                e.receipt_image,
                DATE_FORMAT(e.expense_date, '%Y-%m-%d') as expense_date,
                c.name as category_name,
                c.color as category_color,
                c.icon as category_icon
            FROM expenses e
            JOIN categories c ON e.category_id = c.id
            ORDER BY e.expense_date DESC, e.id DESC";
    
    $stmt = $pdo->query($sql);
    $expenses = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $expenses,
        'count' => count($expenses)
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>