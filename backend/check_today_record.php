<?php

require_once "session.php";

header("Content-Type: application/json");

$pdo = new PDO(
    "mysql:host=localhost;dbname=arutaba;charset=utf8",
    "root",
    "arutaba"
);

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("
    SELECT COUNT(*) cnt
    FROM calender
    WHERE user_id = ?
    AND osake_drinking = CURDATE()
");

$stmt->execute([$user_id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "exists" => $row["cnt"] > 0
]);