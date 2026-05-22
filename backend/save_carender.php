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

    echo "DB接続OK\n";

} catch (Exception $e) {

    die("DB接続失敗\n");
}

echo "SESSION確認\n";

var_dump($_SESSION);

echo "\nPOST確認\n";

var_dump($_POST);

$user_id = $_SESSION["user_id"];

$date    = $_POST["date"];
$smoke   = $_POST["smoke"];
$alcohol = $_POST["alcohol"];

$score = ($alcohol * 1) + ($smoke * 400);

echo "\nSQL実行前\n";

$stmt = $pdo->prepare("
    INSERT INTO calender (
        user_id,
        osake_drinking,
        smoke_day,
        alcohol_consumption,
        ciggarette_consumption,
        score
    )
    VALUES (
        :user_id,
        :date,
        :date,
        :alcohol,
        :smoke,
        :score
    )

    ON DUPLICATE KEY UPDATE
        alcohol_consumption = :alcohol_update,
        ciggarette_consumption = :smoke_update,
        score = :score_update
");

try {

    $result = $stmt->execute([
        ':user_id'        => $user_id,
        ':date'           => $date,
        ':alcohol'        => $alcohol,
        ':smoke'          => $smoke,
        ':score'          => $score,
        ':alcohol_update' => $alcohol,
        ':smoke_update'   => $smoke,
        ':score_update'   => $score
    ]);

    echo "SQL成功";

} catch (PDOException $e) {

    echo $e->getMessage();
}