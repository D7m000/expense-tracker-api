<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

$uploadDir = '../uploads/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt'])) {
    $file = $_FILES['receipt'];
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'error' => 'Only images are allowed'
        ]);
        exit;
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'error' => 'File too large (max 5MB)'
        ]);
        exit;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('receipt_') . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode([
            'success' => true,
            'filename' => $filename,
            'url' => '/expense-tracker/uploads/' . $filename
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Upload failed'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No file uploaded'
    ]);
}
?>