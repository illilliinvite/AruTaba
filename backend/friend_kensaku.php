<?php

if(!isset($_POST["friend_serach"])) {
    echo json_encode(["error" => "no input"]);
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

// ★ LIKE の書き方を修正（% を SQL 内に書かないと動かない）
$stmt = $pdo->prepare("
    SELECT friend_id, friend_wait
    FROM friend
    WHERE user_id = :user_id and friend_id LIKE :friend_serach
");

// ★ % を PHP 側で付ける
$search = "%" . $_POST["friend_serach"] . "%";
$stmt->bindParam(':friend_serach', $search, PDO::PARAM_STR);
$stmt->bindParam('::user_id', $_SESSION["user_id"], PDO::PARAM_STR);

$stmt->execute();

// ★ fetchAll(PDO::FETCH_ASSOC) にしないと JSON にしづらい
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ★ JSON を返す
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
exit;

?>
