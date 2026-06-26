<?php

require_once "session.php";

header('Content-Type: application/json');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );
} catch (Exception $e) {

    echo json_encode(["success" => false]);
    exit;
}

$user_id = $_SESSION["user_id"];
$date    = $_POST["date"];

$stmt = $pdo->prepare("
    DELETE FROM calender
    WHERE user_id = :user_id
    AND osake_drinking = :date
");

$stmt->execute([
    ':user_id' => $user_id,
    ':date'    => $date
]);


echo json_encode(["success" => true]);