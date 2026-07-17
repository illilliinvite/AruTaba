<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$mail = isset($_GET['mail']) ? trim($_GET['mail']) : '';

if ($mail === '') {
    echo json_encode([]);
    exit;
}

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
    $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
    $stmt->execute();
    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$friend) {
        echo json_encode([]);
        exit;
    }

    $my_user_id = $_SESSION['user_id'];
    $friend_user_id = (string)$friend['user_id'];

    $stmt = $pdo->prepare("
        SELECT
            id,
            user_id,
            receiver_id,
            message_id,
            message_type,
            chat_history,
            file_path,
            file_name,
            sent_at
        FROM friend_chat
        WHERE id > :last_id
        AND (
                (user_id = :my_user_id1 AND receiver_id = :friend_user_id1)
            OR (user_id = :friend_user_id2 AND receiver_id = :my_user_id2)
        )
        ORDER BY id ASC
    ");

    $stmt->bindValue(':last_id', $last_id, PDO::PARAM_INT);

    $stmt->bindValue(':my_user_id1', $my_user_id, PDO::PARAM_STR);
    $stmt->bindValue(':friend_user_id1', $friend_user_id, PDO::PARAM_STR);

    $stmt->bindValue(':friend_user_id2', $friend_user_id, PDO::PARAM_STR);
    $stmt->bindValue(':my_user_id2', $my_user_id, PDO::PARAM_STR);
    $stmt->execute();

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log('friendchat_load.php PDOException: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
