<?php

session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=arutaba;charset=utf8",
    "root",
    "arutaba"
);

$data = json_decode(file_get_contents("php://input"), true);

$message = $data["message"];
$user_id = $_SESSION["user_id"];

$sql = "INSERT INTO chat(message,user_id)
        VALUES(:message,:user_id)";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(":message", $message);
$stmt->bindValue(":user_id", $user_id);

$stmt->execute();

echo json_encode([
    "success" => true
]);