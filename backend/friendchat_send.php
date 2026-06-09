<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');



// リクエストボディ取得
$data = json_decode(file_get_contents('php://input'), true);

// バリデーション
if (empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'メッセージが空です']);
    exit;
}

$message = trim($data['message']);

// 100文字を超える場合はエラー（DBのvarchar(100)に合わせる）
if (strlen($message) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'メッセージが長すぎます（100文字以内）']);
    exit;
}

try {

    // DB接続
    $pdo = new PDO(
        'mysql:host=localhost;dbname=arutaba;charset=utf8mb4',
        'root',
        'arutaba',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    // 次のmessage_idを取得（トランザクションで排他制御）
    $pdo->beginTransaction();

    $stmt = $pdo->query('SELECT IFNULL(MAX(message_id), 0) + 1 AS next_id FROM friend_chat FOR UPDATE');
    $next_id = (int)$stmt->fetch(PDO::FETCH_ASSOC)['next_id'];

    // INSERT
    $sql = '
        INSERT INTO friend_chat
            (user_id, message_id, chat_history)
        VALUES
            (:user_id, :message_id, :chat_history)
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id',      $_SESSION['user_id']);
    $stmt->bindValue(':message_id',   $next_id,   PDO::PARAM_INT);
    $stmt->bindValue(':chat_history', $message);
    $stmt->execute();

    $pdo->commit();

    echo json_encode(['success' => true, 'message_id' => $next_id]);

} catch (PDOException $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('friendchat_send.php PDOException: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB エラーが発生しました']);

}
