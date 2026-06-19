<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

if (empty($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$message_id = $data['message_id'] ?? null;

if (!$message_id) {
    echo json_encode([]);
    exit;
}

try {

    $pdo = new PDO(
        'mysql:host=localhost;dbname=arutaba;charset=utf8mb4',
        'root',
        'arutaba',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $my_id = (string)$_SESSION['user_id'];

    // 自分宛のメッセージだけ既読化
    $stmt = $pdo->prepare("
        UPDATE friend_chat
        SET is_read = 1
        WHERE id = :id
          AND receiver_id = :my_id
    ");

    $stmt->execute([
        ':id' => $message_id,
        ':my_id' => $my_id
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false]);
}