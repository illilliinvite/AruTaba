<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "session.php";

header('Content-Type: application/json; charset=utf-8');

try{
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(Exception $e){

    echo json_encode([
        "status"=>"error",
        "message"=>"DB接続失敗"
    ]);
    exit;
}

if(!isset($_GET["user_id"])){

    echo json_encode([
        "status"=>"error",
        "message"=>"user_id がありません"
    ]);
    exit;
}

$target_user_id = $_GET["user_id"];
$my_user_id     = $_SESSION["user_id"];


// 自分情報
$stmt = $pdo->prepare("
    SELECT user_name, mail_address
    FROM profile
    WHERE user_id = :uid
");

$stmt->bindValue(":uid",$my_user_id);
$stmt->execute();

$me = $stmt->fetch(PDO::FETCH_ASSOC);


// 相手情報
$stmt = $pdo->prepare("
    SELECT user_name, mail_address
    FROM profile
    WHERE user_id = :uid
");

$stmt->bindValue(":uid",$target_user_id);
$stmt->execute();

$friend = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$me || !$friend){

    echo json_encode([
        "status"=>"error",
        "message"=>"ユーザー情報取得失敗"
    ]);
    exit;
}


// 元の申請を承認
$stmt = $pdo->prepare("
    UPDATE friend
    SET friend_wait = 0,
        friend = 1
    WHERE user_id = :user_id
      AND friend_id = :friend_id
");

$stmt->execute([
    ":user_id"   => $target_user_id,
    ":friend_id" => $me["mail_address"]
]);


// 逆向きを作成
$stmt = $pdo->prepare("
    INSERT INTO friend
    (
        user_id,
        user_name,
        friend_id,
        friend_wait,
        friend
    )
    VALUES
    (
        :user_id,
        :user_name,
        :friend_id,
        0,
        1
    )
");

$stmt->execute([
    ":user_id"   => $my_user_id,
    ":user_name" => $me["user_name"],
    ":friend_id" => $friend["mail_address"]
]);

echo json_encode([
    "status"=>"ok"
]);