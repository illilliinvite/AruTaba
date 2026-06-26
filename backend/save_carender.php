<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "session.php";

header('Content-Type: text/plain');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );
} catch (Exception $e) {
    die("DB接続失敗\n");
}

$user_id = $_SESSION["user_id"];

$date    = $_POST["date"];
$smoke   = $_POST["smoke"];
$alcohol = $_POST["alcohol"];
$brand   = $_POST["brand"]  ?? null;
$degree  = $_POST["degree"] ?? null;

$score = ($alcohol * 1) + ($smoke * 400);

$stmt = $pdo->prepare("
    INSERT INTO calender (
        user_id,
        osake_drinking,
        smoke_day,
        alcohol_consumption,
        ciggarette_consumption,
        score,
        brand,
        alcohol_degree
    )
    VALUES (
        :user_id,
        :date,
        :date,
        :alcohol,
        :smoke,
        :score,
        :brand,
        :alcohol_degree
    )
    ON DUPLICATE KEY UPDATE
        alcohol_consumption    = :alcohol_update,
        ciggarette_consumption = :smoke_update,
        score                  = :score_update,
        brand                  = :brand_update,
        alcohol_degree         = :alcohol_degree_update
");

try {

    $stmt->execute([
        ':user_id'               => $user_id,
        ':date'                  => $date,
        ':alcohol'               => $alcohol,
        ':smoke'                 => $smoke,
        ':score'                 => $score,
        ':brand'                 => $brand  ?: null,
        ':alcohol_degree'        => $degree ?: null,
        ':alcohol_update'        => $alcohol,
        ':smoke_update'          => $smoke,
        ':score_update'          => $score,
        ':brand_update'          => $brand  ?: null,
        ':alcohol_degree_update' => $degree ?: null,
    ]);

} catch (PDOException $e) {

    echo $e->getMessage();
}