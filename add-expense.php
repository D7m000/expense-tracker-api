<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'يجب استخدام POST method'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['category_id']) || !isset($data['amount']) || !isset($data['expense_date'])) {
    echo json_encode([
        'success' => false,
        'error' => 'البيانات المطلوبة ناقصة'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = 1;
$category_id = $data['category_id'];
$amount = $data['amount'];
$description = $data['description'] ?? '';
$expense_date = $data['expense_date'];
$receipt_image = $data['receipt_image'] ?? null;

if ($amount <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'المبلغ يجب أن يكون أكبر من صفر'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "INSERT INTO expenses (user_id, category_id, amount, description, expense_date, receipt_image) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $category_id, $amount, $description, $expense_date, $receipt_image]);
    
    $new_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'تم إضافة المصروف بنجاح',
        'expense_id' => $new_id
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>