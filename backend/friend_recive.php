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

// 承認待ち一覧取得（user_id だけ取る）
$stmt = $pdo->prepare("
    SELECT user_id
    FROM friend
    WHERE friend_wait = 1 AND friend_id = :friend_id
");
$stmt->bindParam(":friend_id", $mail_address);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$final = [];

// user_id から profile の user_name を取得
foreach ($rows as $r) {
    $stmt = $pdo->prepare("
        SELECT user_name,user_id
        FROM profile
        WHERE user_id = :uid
    ");
    $stmt->bindParam(":uid", $r["user_id"]);
    $stmt->execute();
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($p) {
        $final[] = [
            "user_id" => $r["user_id"],
            "user_name" => $p["user_name"]
        ];
    }
}

echo json_encode([
    "status" => "ok",
    "requests" => $final
], JSON_UNESCAPED_UNICODE);
exit;
