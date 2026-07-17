<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "session.php";

try {
    $pdo =  PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );
} catch (Exception $e) {
    echo "データベースに接続できません。";
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.forum_history, f.user_name, f.day,
           CASE
               WHEN p.icon_path IS NOT NULL
               THEN CONCAT('../', p.icon_path)
               ELSE NULL
           END AS icon_path
    FROM forum f
    LEFT JOIN profile p ON f.user_id = p.user_id
    ORDER BY f.day ASC
");
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([<?php*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "session.php";
date_default_timezone_set('Asia/Tokyo');

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

$forum_history = $_POST["forum_history"];

$stmt = $pdo->prepare("SELECT user_name,icon_path FROM profile WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_name = $user["user_name"];
$icon_path = $user["icon_path"];

$day = date("Y-m-d H:i:s");

$stmt = $pdo->prepare("INSERT INTO forum(user_name, user_id, forum_history, day, icon_path) VALUES(:user_name, :user_id, :forum_history, :day, :icon_path)");

$stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);
$stmt->bindParam(':forum_history', $forum_history, PDO::PARAM_STR);
$stmt->bindParam(':day', $day, PDO::PARAM_STR);
$stmt->bindParam(':icon_path', $icon_path, PDO::PARAM_STR);

$stmt->execute();

?>