
<?php

if(!isset($_POST["friend_id"]))
{
    echo "nothing";
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

// ① 相手のメールアドレスと名前を取得
$stmt = $pdo->prepare("
    SELECT mail_address, user_name 
    FROM profile 
    WHERE mail_address = :mail
");

$stmt->bindParam(":mail", $_POST["friend_id"], PDO::PARAM_STR);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode([
        "status" => "error",
        "message" => "アカウントが見つかりませんでした"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$friend_id  = $row["mail_address"];
$user_name  = $row["user_name"];

// ② friend テーブルに登録
$stmt = $pdo->prepare("
    INSERT INTO friend(user_id, user_name, friend_id, friend_wait, friend)
    VALUES(:user_id, :user_name, :friend_id, 1, 0)
");

$user_id = $_SESSION["user_id"];

$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
$stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_STR);

$stmt->execute();

// ③ 成功レスポンス
echo json_encode([
    "status" => "ok",
    "message" => "登録が完了しました"
], JSON_UNESCAPED_UNICODE);

exit;
