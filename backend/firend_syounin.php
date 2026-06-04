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

if (!isset($_GET["user_id"])) {
    echo json_encode(["status" => "error", "message" => "user_id がありません"]);
    exit;
}

$target_user_id = $_GET["user_id"];

// 自分のメールアドレス取得
$stmt = $pdo->prepare("SELECT mail_address FROM profile WHERE user_id = :uid");
$stmt->bindParam(":uid", $_SESSION["user_id"]);
$stmt->execute();
$me = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$me) {
    echo json_encode(["status" => "error", "message" => "ユーザー情報が取得できません"]);
    exit;
}

$mail_address = $me["mail_address"];

// 承認処理
$stmt = $pdo->prepare("
    UPDATE friend
    SET friend_wait = 0, friend = 1
    WHERE user_id = :user_id AND friend_id = :friend_id
");
$stmt->bindParam(":user_id", $target_user_id);
$stmt->bindParam(":friend_id", $mail_address);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "message" => "承認に失敗しました"]);
}
exit;
