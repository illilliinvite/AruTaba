<?php

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
    echo "データベースに接続できません。";
    exit;
}

$day = date("Y-m-d");

$forum_history = $_POST["forum_history"];


$stmt = $pdo->prepare("SELECT user_name FROM profile WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_name = $user["user_name"];  // ← これで取り出せる

// ★ 現在時刻を入れる（datetime 用）
$day = date("Y-m-d H:i:s");


$stmt = $pdo->prepare("insert into forum(user_name, forum_history, day) values(:user_name, :forum_history, :day)");

$stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
$stmt->bindParam(':forum_history', $forum_history, PDO::PARAM_STR);
$stmt->bindParam(':day', $day, PDO::PARAM_STR);

$stmt->execute();

?>