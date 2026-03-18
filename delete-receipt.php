<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['filename'])) {
    echo json_encode([
        'success' => false,
        'error' => 'اسم الملف مطلوب'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$filename = $data['filename'];
$filepath = '../uploads/' . basename($filename);

if (file_exists($filepath)) {
    if (unlink($filepath)) {
        echo json_encode([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'فشل حذف الصورة'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'الملف غير موجود'
    ], JSON_UNESCAPED_UNICODE);
}
?>