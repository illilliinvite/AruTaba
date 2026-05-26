<?php

session_start();

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

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {

    die("DB接続失敗: " . $e->getMessage());
}


// POST確認
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // フォーム取得
    $user_name = $_POST["user_name"];
    $mail_address = $_POST["mail_address"];

    // ログイン中のuser_id取得
    $user_id = $_SESSION["user_id"];

    try {

        // profile更新
        $sql = "UPDATE profile
                SET
                    user_name = :user_name,
                    mail_address = :mail_address
                WHERE
                    user_id = :user_id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":user_id", $user_id);
        $stmt->bindValue(":user_name", $user_name);
        $stmt->bindValue(":mail_address", $mail_address);

        $stmt->execute();

        echo "更新成功";

    } catch(PDOException $e) {

        echo "更新失敗: " . $e->getMessage();
    }
}
?>