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

// ★ 重要：profile に user_id が存在しないと外部キーエラーになる
//   必要なら自動で作る（任意）
$check = $pdo->prepare("SELECT user_id FROM profile WHERE user_id = :user_id");
$check->bindParam(':user_id', $user_id);
$check->execute();

if ($check->rowCount() === 0) {
    // login にも必要なので、login の構造に合わせて修正してね
    $pdo->prepare("INSERT INTO login (user_id, password) VALUES (:user_id, 'dummy')")
        ->execute([':user_id' => $user_id]);

    $pdo->prepare("
        INSERT INTO profile (user_id, user_name, mail_address, alcohol_level, smoke_level)
        VALUES (:user_id, 'test_user', 'test@example.com', 0, 0)
    ")->execute([':user_id' => $user_id]);
}

// INSERT 処理
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
");

$stmt->bindParam(':alcohol_consumption', $alcohol_amount, PDO::PARAM_INT);
$stmt->bindParam(':ciggarette_consumption', $tobacco_amount, PDO::PARAM_INT);
$stmt->bindParam(':osake_drinking', $today, PDO::PARAM_STR);
$stmt->bindParam(':score', $score, PDO::PARAM_INT);
$stmt->bindParam(':smoke_day', $today, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);

$stmt->execute();

// 完了後リダイレクト
header("Location: ../html/carender.html");
exit;

?>
