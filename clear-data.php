<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

try {
    $user_id = 1;
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $pdo->exec("DELETE FROM expenses WHERE user_id = $user_id");
    
    $pdo->exec("DELETE FROM categories WHERE user_id = $user_id");
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    $pdo->exec("ALTER TABLE expenses AUTO_INCREMENT = 1");

    $pdo->exec("ALTER TABLE categories AUTO_INCREMENT = 1");
    
    $stmt = $pdo->prepare("UPDATE users SET monthly_budget = 0 WHERE id = ?");
    $stmt->execute([$user_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم مسح جميع البيانات بنجاح'
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>