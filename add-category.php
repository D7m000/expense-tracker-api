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

if (!isset($data['name']) || !isset($data['color'])) {
    echo json_encode([
        'success' => false,
        'error' => 'البيانات المطلوبة ناقصة'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = 1; 
$name = trim($data['name']);
$color = $data['color'];
$icon = $data['icon'] ?? 'circle';

try {
    $sql = "INSERT INTO categories (user_id, name, color, icon, is_default) 
            VALUES (?, ?, ?, ?, 0)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $name, $color, $icon]);
    
    $new_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'تم إضافة الفئة بنجاح',
        'category_id' => $new_id,
        'category' => [
            'id' => $new_id,
            'name' => $name,
            'color' => $color,
            'icon' => $icon,
            'is_default' => 0
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>