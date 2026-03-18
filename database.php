<?php
$host = 'shuttle.proxy.rlwy.net';
$dbname = 'railway';
$username = 'root';
$password = 'jidKdJCZUveXJEemSJlYOxzmVlypmLPB';
$port = 56851;

// PHP Data Object (PDO)
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed'
    ]);
    exit;
}
?>