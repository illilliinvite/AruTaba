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

$data = date("Y-m-d");

$stmt = $pdo->prepare("select forum_history from forum where day = :data");

$stmt->bindParam(':data', $data, PDO::PARAM_STR);

$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    "forum_history_list" => $rows
], JSON_UNESCAPED_UNICODE);



?>