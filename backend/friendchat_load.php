<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

// ログインチェック
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

// last_id: このID以降のメッセージを返す（差分取得）
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

try {

    $pdo = new PDO(
        'mysql:host=localhost;dbname=arutaba;charset=utf8mb4',
        'root',
        'arutaba',
        [
            PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    /*
     * friend_chatテーブルには sent_at カラムがないため、
     * message_id の順でソートして返します。
     *
     * ※ 送信日時を表示したい場合は、以下のALTER文でカラムを追加してください。
     *    ALTER TABLE friend_chat
     *        ADD COLUMN sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
     *        AFTER chat_history;
     *
     * sent_at がない場合は NULL を返し、JS側で現在時刻を代替表示します。
     */

    // sent_at カラムが存在するか確認
    $colCheck = $pdo->query("
        SELECT COUNT(*) AS cnt
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = 'arutaba'
          AND TABLE_NAME   = 'friend_chat'
          AND COLUMN_NAME  = 'sent_at'
    ");
    $hasSentAt = (int)$colCheck->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;

    if ($hasSentAt) {
        $sql = '
            SELECT
                user_id,
                message_id,
                chat_history,
                sent_at
            FROM friend_chat
            WHERE message_id > :last_id
            ORDER BY message_id ASC
        ';
    } else {
        $sql = '
            SELECT
                user_id,
                message_id,
                chat_history,
                NULL AS sent_at
            FROM friend_chat
            WHERE message_id > :last_id
            ORDER BY message_id ASC
        ';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':last_id', $last_id, PDO::PARAM_INT);
    $stmt->execute();

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    error_log('friendchat_load.php PDOException: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);

}
