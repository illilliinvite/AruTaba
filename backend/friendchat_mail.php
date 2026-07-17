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
        mail_address,
        icon_path
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

    // friend_llist.php と同じ考え方でパスを整形
    if ($user && !empty($user["icon_path"])) {
        $user["icon_path"] = "../" . $user["icon_path"];
    } elseif ($user) {
        $user["icon_path"] = null;
    }

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