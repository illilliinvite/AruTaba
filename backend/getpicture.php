<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "session.php";
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );
} catch (Exception $e) {
    echo json_encode(["error" => "DB接続エラー"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT  icon_path
    FROM profile
    WHERE user_id = :user_id
");

$stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => "ok",
    "icon_path" => $row ? $row["icon_path"] : null
], JSON_UNESCAPED_UNICODE);

exit;
