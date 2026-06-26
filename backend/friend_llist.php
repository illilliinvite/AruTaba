<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "session.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => "DB接続失敗"
    ]);
    exit;
}

// 自分のメールアドレス取得
$stmt = $pdo->prepare("
    SELECT mail_address
    FROM profile
    WHERE user_id = :user_id
");

$stmt->execute([
    ":user_id" => $_SESSION["user_id"]
]);

$me = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$me) {

    echo json_encode([
        "status" => "error",
        "message" => "ユーザー情報が取得できません"
    ]);
    exit;
}

$mail_address = $me["mail_address"];
$my_user_id   = $_SESSION["user_id"];

$final_friends = [];
$addedMails = [];

/* =========================
   自分が受け取った側
========================= */

$stmt = $pdo->prepare("
    SELECT
        p.user_name,
        p.mail_address,
        p.icon_path
    FROM friend f
    INNER JOIN profile p
        ON f.user_id = p.user_id
    WHERE f.friend = 1
      AND f.friend_id = :mail
");

$stmt->execute([
    ":mail" => $mail_address
]);

$receiveFriends = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($receiveFriends as $f) {

    if (!in_array($f["mail_address"], $addedMails)) {

        $final_friends[] = [
            "user_name"    => $f["user_name"],
            "mail_address" => $f["mail_address"],
            "icon_path" => !empty($f["icon_path"]) ? "../" . $f["icon_path"] : null
        ];

        $addedMails[] = $f["mail_address"];
    }
}

/* =========================
   自分が送った側
========================= */

$stmt = $pdo->prepare("
    SELECT
        p.user_name,
        p.mail_address,
        p.icon_path
    FROM friend f
    INNER JOIN profile p
        ON f.friend_id = p.mail_address
    WHERE f.friend = 1
      AND f.user_id = :user_id
");

$stmt->execute([
    ":user_id" => $my_user_id
]);

$sendFriends = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($sendFriends as $f) {

    if (!in_array($f["mail_address"], $addedMails)) {

        $final_friends[] = [
            "user_name"    => $f["user_name"],
            "mail_address" => $f["mail_address"],
            "icon_path" => !empty($f["icon_path"]) ? "../" . $f["icon_path"] : null
        ];

        $addedMails[] = $f["mail_address"];
    }
}

echo json_encode([
    "status" => "ok",
    "friends" => $final_friends
], JSON_UNESCAPED_UNICODE);
?>