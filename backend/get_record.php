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

    echo json_encode([
        "tobacco_days" => 0,
        "alcohol_days" => 0
    ]);
    exit;
}

$user_id = $_SESSION["user_id"];

$tobacco_days = 0;
$alcohol_days = 0;

/*
 * 禁煙継続日数
 */
$date = new DateTime();

while (true) {

    $targetDate = $date->format("Y-m-d");

    $stmt = $pdo->prepare("
        SELECT ciggarette_consumption
        FROM calender
        WHERE user_id = :user_id
        AND osake_drinking = :target_date
    ");

    $stmt->execute([
        ':user_id' => $user_id,
        ':target_date' => $targetDate
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        break;
    }

    if ($row["ciggarette_consumption"] > 0) {
        break;
    }

    $tobacco_days++;

    $date->modify('-1 day');
}

/*
 * 禁酒継続日数
 */
$date = new DateTime();

while (true) {

    $targetDate = $date->format("Y-m-d");

    $stmt = $pdo->prepare("
        SELECT alcohol_consumption
        FROM calender
        WHERE user_id = :user_id
        AND osake_drinking = :target_date
    ");

    $stmt->execute([
        ':user_id' => $user_id,
        ':target_date' => $targetDate
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        break;
    }

    if ($row["alcohol_consumption"] > 0) {
        break;
    }

    $alcohol_days++;

    $date->modify('-1 day');
}

echo json_encode([
    "tobacco_days" => $tobacco_days,
    "alcohol_days" => $alcohol_days
]);