<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'يجب استخدام PUT أو POST method'
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
    $check_sql = "SELECT id FROM expenses WHERE id = ? AND user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$expense_id, $user_id]);
    
    if ($check_stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'المصروف غير موجود'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'خطأ في التحقق: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$updates = [];
$params = [];

if (isset($data['category_id'])) {
    $updates[] = "category_id = ?";
    $params[] = $data['category_id'];
}

if (isset($data['amount'])) {
    if ($data['amount'] <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'المبلغ يجب أن يكون أكبر من صفر'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $updates[] = "amount = ?";
    $params[] = $data['amount'];
}

if (isset($data['description'])) {
    $updates[] = "description = ?";
    $params[] = $data['description'];
}

if (isset($data['expense_date'])) {
    $updates[] = "expense_date = ?";
    $params[] = $data['expense_date'];
}

if (empty($updates)) {
    echo json_encode([
        'success' => false,
        'error' => 'لا توجد بيانات للتحديث'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$params[] = $expense_id;
$params[] = $user_id;

try {
    $sql = "UPDATE expenses SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'تم تحديث المصروف بنجاح'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'لم يتم تحديث أي بيانات'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>