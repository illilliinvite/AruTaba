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

$user_name = $_POST["user_name"];
$forum_history = $_POST["forum_history"];

$stmt = $pdo->prepare("insert into forum(user_name, forum_history, day) values(:user_name, :forum_history, :day)");

$stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
$stmt->bindParam(':forum_history', $forum_history, PDO::PARAM_STR);
$stmt->bindParam(':day', $day, PDO::PARAM_STR);

$stmt->execute();

?>