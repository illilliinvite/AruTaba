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
    http_response_code(400);
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

    $my_id = (string)$_SESSION['user_id'];

    // 相手ID取得
    $stmt = $pdo->prepare("
        SELECT user_id
        FROM profile
        WHERE mail_address = :mail
    ");
    $stmt->execute([':mail' => $mail]);

    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$friend) {
        echo json_encode([]);
        exit;
    }

    $friend_id = (string)$friend['user_id'];

    // ★既読更新はここではやらない（重要）

    // メッセージ取得
    $sql = "
    SELECT
        id AS message_id,
        user_id,
        receiver_id,
        message_type,
        chat_history,
        file_path,
        file_name,
        sent_at,
        is_read
    FROM friend_chat
    WHERE id > :last_id
      AND (
            (user_id = :my_id AND receiver_id = :friend_id)
            OR
            (user_id = :friend_id2 AND receiver_id = :my_id2)
      )
    ORDER BY id ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':last_id' => $last_id,
        ':my_id' => $my_id,
        ':friend_id' => $friend_id,
        ':friend_id2' => $friend_id,
        ':my_id2' => $my_id
    ]);

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC),
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
?>