<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'يجب استخدام DELETE أو POST method'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'رقم المصروف مطلوب'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$expense_id = $data['id'];
$user_id = 1;

try {
    $sql = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$expense_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'تم حذف المصروف بنجاح'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'المصروف غير موجود أو تم حذفه مسبقاً'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>