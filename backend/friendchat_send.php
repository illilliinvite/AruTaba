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
            'success' => false,
            'error' => '相手が存在しません'
        ]);
        exit;
    }

    $friend_user_id = (string)$friend['user_id'];

    $stmt = $pdo->prepare("
        INSERT INTO friend_chat (user_id, message_id, chat_history)
        VALUES (:user_id, :friend_user_id, :chat_history)
    ");
    $stmt->bindValue(':user_id', $my_user_id, PDO::PARAM_STR);
    $stmt->bindValue(':friend_user_id', $friend_user_id, PDO::PARAM_STR);
    $stmt->bindValue(':chat_history', $message, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log('friendchat_send.php error: ' . $e->getMessage());
    echo $e;
}
