<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "session.php";

// ★ 本来はログイン時にセットされる値
//   今はテスト用に 123 を使う

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

// POST 受け取り
$tobacco_brand   = $_POST["tobacco_brand"];
$tobacco_amount  = $_POST["tobacco_amount"];
$alcohol_dosuu   = $_POST["alcohol_dosuu"];
$alcohol_amount  = $_POST["alcohol_amount"];

$today = date("Y-m-d");

// スコア計算
$score = ($alcohol_amount * $alcohol_dosuu) + ($tobacco_amount * 400);

// 外部キーに使う user_id
$user_id = $_SESSION["user_id"];


// INSERT or UPDATE
$stmt = $pdo->prepare("
    INSERT INTO calender(
        alcohol_consumption,
        ciggarette_consumption,
        osake_drinking,
        score,
        smoke_day,
        user_id
    ) VALUES (
        :alcohol_consumption,
        :ciggarette_consumption,
        :osake_drinking,
        :score,
        :smoke_day,
        :user_id
    )

    ON DUPLICATE KEY UPDATE
        alcohol_consumption = :alcohol_update,
        ciggarette_consumption = :ciggarette_update,
        score = :score_update
");

$stmt->bindParam(':alcohol_consumption', $alcohol_amount, PDO::PARAM_INT);
$stmt->bindParam(':ciggarette_consumption', $tobacco_amount, PDO::PARAM_INT);
$stmt->bindParam(':osake_drinking', $today, PDO::PARAM_STR);
$stmt->bindParam(':score', $score, PDO::PARAM_INT);
$stmt->bindParam(':smoke_day', $today, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);

$stmt->bindParam(':alcohol_update', $alcohol_amount, PDO::PARAM_INT);
$stmt->bindParam(':ciggarette_update', $tobacco_amount, PDO::PARAM_INT);
$stmt->bindParam(':score_update', $score, PDO::PARAM_INT);

$stmt->execute();

// 完了後リダイレクト
header("Location: ../html/carender.html");
exit;

?>
