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

$stmt = $pdo->prepare("
    SELECT forum_history, user_name, day, icon_path
    FROM forum
    ORDER BY day ASC
");
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "forum_history_list" => $rows
], JSON_UNESCAPED_UNICODE);
?>