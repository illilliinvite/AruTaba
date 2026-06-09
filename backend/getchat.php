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

// 今日の日付
$data = date("Y-m-d");

// 今日のチャットを新しい順に取得
$stmt = $pdo->prepare("
    SELECT forum_history, user_name
    FROM forum
    WHERE DATE(day) = :data
    ORDER BY day ASC
");

$stmt->bindParam(':data', $data, PDO::PARAM_STR);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "forum_history_list" => $rows
], JSON_UNESCAPED_UNICODE);


?>
