<?php
session_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ログイン情報がありません'
    ]);
    exit;
}

if (empty($data['message'])) {
    echo json_encode([
        'success' => false,
        'error' => 'メッセージが空です'
    ]);
    exit;
}

if (empty($data['mail'])) {
    echo json_encode([
        'success' => false,
        'error' => 'mail がありません'
    ]);
    exit;
}

$message = trim($data['message']);
$friend_mail = trim($data['mail']);
$my_user_id = (string)$_SESSION['user_id'];

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=arutaba;charset=utf8mb4',
        'root',
        'arutaba',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // 相手の user_id を取得
    $stmt = $pdo->prepare("SELECT user_id FROM profile WHERE mail_address = :mail");
    $stmt->bindValue(':mail', $friend_mail, PDO::PARAM_STR);
    $stmt->execute();
    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$friend) {
        echo json_encode([
            'friend_mail' => $friend_mail,
    'friend' => $friend,
    'friend_user_id' => $friend['user_id'] ?? null
        ]);
        exit;
    }
    $friend_user_id = $friend['user_id'];

    $message_id = uniqid('msg_', true);

    $stmt = $pdo->prepare("
        INSERT INTO friend_chat
        (user_id, receiver_id, message_id, message_type, chat_history, is_read)
        VALUES
        (:user_id, :receiver_id, :message_id, 'text', :chat_history, 0)
    ");

    $stmt->execute([
        ':user_id' => $my_user_id,
        ':receiver_id' => $friend_user_id,
        ':message_id' => $message_id,
        ':chat_history' => $message
    ]);

    echo json_encode([
        'success' => true,
        'message_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}