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

    echo json_encode([]);
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("
    SELECT
        osake_drinking,
        alcohol_consumption,
        ciggarette_consumption
    FROM calender
    WHERE user_id = :user_id
");

$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $date = $row["osake_drinking"];

    $data[$date] = [
        "smoke"   => $row["ciggarette_consumption"],
        "alcohol" => $row["alcohol_consumption"]
    ];
}

echo json_encode($data);