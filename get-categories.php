<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

try {
    $user_id = 1;
    
    $sql = "SELECT id, name, color, icon, is_default 
            FROM categories 
            WHERE user_id = ? 
            ORDER BY is_default DESC, name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    
    $categories = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $categories,
        'count' => count($categories)
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>