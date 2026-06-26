<?php

header('Content-Type: application/json');

// JSから受信
$data = json_decode(
    file_get_contents("php://input"),
    true
);

$mail = $data["mail"] ?? "";

// DB接続
$host = "localhost";
$dbname = "arutaba";
$user = "root";
$pass = "arutaba";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // メールアドレスからユーザー取得
    $sql = "
    SELECT
        user_id,
        user_name,
        mail_address
    FROM profile
    WHERE mail_address = :mail
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ":mail",
        $mail
    );

    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "user" => $user
    ]);

} catch(Exception $e) {

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);

}