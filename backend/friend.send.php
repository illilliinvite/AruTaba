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

// ===============================
// 入力チェック
// ===============================
if (!isset($_POST["friend_id"]) || $_POST["friend_id"] === "") {
    echo json_encode(["status" => "error", "message" => "friend_id がありません"]);
    exit;
}

$friend_id = $_POST["friend_id"];  // メールアドレス
$my_user_id = $_SESSION["user_id"];

// ===============================
// 自分の user_name と mail_address を取得
// ===============================
$stmt = $pdo->prepare("
    SELECT user_name, mail_address
    FROM profile
    WHERE user_id = :uid
");
$stmt->bindParam(":uid", $my_user_id);
$stmt->execute();
$me = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$me) {
    echo json_encode(["status" => "error", "message" => "自分の情報が取得できません"]);
    exit;
}

$my_name = $me["user_name"];
$my_mail = $me["mail_address"];

// user_name が NULL の場合の対策
if ($my_name === null || $my_name === "") {
    $my_name = "名無し";  // ← 必要なら変更してOK
}

// ===============================
// 自分自身に申請していないかチェック
// ===============================
if ($friend_id === $my_mail) {
    echo json_encode(["status" => "error", "message" => "自分自身には申請できません"]);
    exit;
}

// ===============================
// 相手が存在するかチェック
// ===============================
$stmt = $pdo->prepare("
    SELECT user_id, user_name
    FROM profile
    WHERE mail_address = :mail
");
$stmt->bindParam(":mail", $friend_id);
$stmt->execute();
$target = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$target) {
    echo json_encode(["status" => "error", "message" => "このメールアドレスのユーザーは存在しません"]);
    exit;
}

$target_user_id = $target["user_id"];

// ===============================
// すでにフレンド or 承認待ちかチェック
// ===============================
$stmt = $pdo->prepare("
    SELECT *
    FROM friend
    WHERE user_id = :me AND friend_id = :target
");
$stmt->bindParam(":me", $my_user_id);
$stmt->bindParam(":target", $friend_id);
$stmt->execute();
$exists = $stmt->fetch(PDO::FETCH_ASSOC);

if ($exists) {
    if ($exists["friend"] == 1) {
        echo json_encode(["status" => "error", "message" => "すでにフレンドです"]);
    } else {
        echo json_encode(["status" => "error", "message" => "すでに申請済みです"]);
    }
    exit;
}

// ===============================
// フレンド申請を登録
// ===============================
$stmt = $pdo->prepare("
    INSERT INTO friend (user_id, user_name, friend_id, friend_wait, friend)
    VALUES (:uid, :uname, :fid, 1, 0)
");

$stmt->bindParam(":uid", $my_user_id);
$stmt->bindParam(":uname", $my_name);  // ← ここが重要！必ず名前を入れる
$stmt->bindParam(":fid", $friend_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "フレンド申請を送信しました"]);
} else {
    echo json_encode(["status" => "error", "message" => "申請に失敗しました"]);
}

exit;