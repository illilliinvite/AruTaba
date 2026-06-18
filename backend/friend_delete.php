<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$host = "localhost";
$dbname = "arutaba";
$user = "root";
$pass = "arutaba";

header("Content-Type: application/json; charset=utf-8");

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "DB接続失敗"]);
    exit;
}

// ---------------------------
// パラメータ取得
// ---------------------------
$friend_mail = $_GET["mail"] ?? "";

if ($friend_mail === "") {
    echo json_encode(["status" => "error", "message" => "メールが指定されていません"]);
    exit;
}

// ---------------------------
// 自分のID（ログイン中ユーザー）
// ---------------------------
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "ログイン情報がありません"]);
    exit;
}

$my_id = $_SESSION["user_id"];

// ---------------------------
// メールから friend_id を取得
// ---------------------------
$sql = "SELECT user_id FROM profile WHERE mail_address = ?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $friend_mail, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(["status" => "error", "message" => "該当ユーザーが存在しません"]);
    exit;
}

$friend_id = $row["user_id"];

// ---------------------------
// フレンド削除（両方向）
// ---------------------------
$sql = "DELETE FROM friend 
        WHERE friend_id = :friend_id";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":friend_id", $friend_mail, PDO::PARAM_STR);


$result = $stmt->execute();

if ($result) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "message" => "削除に失敗しました"]);
}
