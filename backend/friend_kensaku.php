<?php

if(!isset($_POST["friend_id"])) {
    echo json_encode(["status" => "error", "message" => "no input"]);
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "db connection failed"]);
    exit;
}

// SQL
$stmt = $pdo->prepare("
    SELECT friend_id, friend_wait
    FROM friend
    WHERE user_id = :user_id
      AND friend_id LIKE :friend_id
");

// LIKE 用に % を付ける
$search = "%" . $_POST["friend_id"] . "%";

$stmt->bindParam(':friend_id', $search, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);

$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON 返却
header('Content-Type: application/json; charset=utf-8');
echo json_encode(["status" => "ok", "data" => $rows]);
exit;

?>
