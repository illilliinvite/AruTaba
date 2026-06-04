<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "session.php";

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );
} catch (Exception $e) {
    echo json_encode(["error" => "db connection failed"]);
    exit;
}

// 自分のメールアドレス取得
$stmt = $pdo->prepare("
    SELECT mail_address
    FROM profile 
    WHERE user_id = :user_id
");
$stmt->bindParam(":user_id", $_SESSION["user_id"]);
$stmt->execute();
$me = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$me) {
    echo json_encode(["status" => "error", "message" => "ユーザー情報が取得できません"]);
    exit;
}

$mail_address = $me["mail_address"];

// 承認待ち一覧取得
$stmt = $pdo->prepare("
    SELECT user_id, user_name
    FROM friend
    WHERE friend_wait = 1 AND friend_id = :friend_id
");
$stmt->bindParam(":friend_id", $mail_address);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => "ok",
    "requests" => $rows
], JSON_UNESCAPED_UNICODE);
exit;
