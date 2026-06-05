<?php

session_start();

header('Content-Type: application/json');

// DB接続
$pdo = new PDO(
    "mysql:host=localhost;dbname=arutaba;charset=utf8",
    "root",
    "arutaba"
);

// メッセージ取得
$data = json_decode(
    file_get_contents("php://input"),
    true
);

$message = $data["message"];

// 最大message_id取得
$sql = "
SELECT IFNULL(MAX(message_id), 0) + 1 AS next_id
FROM friend_chat
";

$stmt = $pdo->query($sql);

$next_id = $stmt->fetch(PDO::FETCH_ASSOC)["next_id"];

// 登録
$sql = "
INSERT INTO friend_chat
(
    user_id,
    message_id,
    chat_history
)
VALUES
(
    :user_id,
    :message_id,
    :chat_history
)
";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(
    ":user_id",
    $_SESSION["user_id"]
);

$stmt->bindValue(
    ":message_id",
    $next_id,
    PDO::PARAM_INT
);

$stmt->bindValue(
    ":chat_history",
    $message
);

$result = $stmt->execute();

echo json_encode([
    "success" => $result
]);