<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['budget'])) {
    echo json_encode([
        'success' => false,
        'error' => 'الميزانية مطلوبة'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = 1;
$budget = $data['budget'];

if ($budget <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'الميزانية يجب أن تكون أكبر من صفر'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "UPDATE users SET monthly_budget = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$budget, $user_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم تحديث الميزانية بنجاح',
        'budget' => $budget
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>