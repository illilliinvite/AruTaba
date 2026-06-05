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
    echo json_encode(["status" => "error", "message" => "DB接続失敗"]);
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
$my_user_id   = $_SESSION["user_id"];

// ===============================
// friend = 1 のフレンド一覧を両方から取得
// ===============================

// ① 自分が「受け取った側」 friend_id = 自分のメール
$stmt = $pdo->prepare("
    SELECT user_name
    FROM friend
    WHERE friend = 1 AND friend_id = :friend_id
");
$stmt->bindParam(":friend_id", $mail_address);
$stmt->execute();
$friends_from_receive = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ② 自分が「送った側」 user_id = 自分の user_id
$stmt = $pdo->prepare("
    SELECT friend_id AS mail
    FROM friend
    WHERE friend = 1 AND user_id = :user_id
");
$stmt->bindParam(":user_id", $my_user_id);
$stmt->execute();
$friends_from_send = $stmt->fetchAll(PDO::FETCH_ASSOC);

// メールアドレスから user_name を取得
$final_friends = [];

// ① 受け取った側（すでに user_name がある）
foreach ($friends_from_receive as $f) {
    $final_friends[] = ["user_name" => $f["user_name"]];
}

// ② 送った側（メールアドレス → user_name を取得）
foreach ($friends_from_send as $f) {
    $stmt = $pdo->prepare("
        SELECT user_name, mail_address
        FROM profile
        WHERE mail_address = :mail
    ");
    $stmt->bindParam(":mail", $f["mail"]);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile) {
    $final_friends[] = [
        "user_name" => $profile["user_name"],
        "mail_address" => $profile["mail_address"]
    ];
}

}

echo json_encode([
    "status" => "ok",
    "friends" => $final_friends
], JSON_UNESCAPED_UNICODE);
exit;