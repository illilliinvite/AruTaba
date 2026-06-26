<?php

require_once 'session.php';

try
{
    $pdo = new PDO(
            "mysql:host=localhost;dbname=arutaba;charset=utf8",
            "root",
            "arutaba"
        );
}catch(exception $e)
{
    echo "データベースに接続できません。";
}

// フォームが送信された時
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // フォームの値を取得
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $mail_address = $_POST['mail_address'];
    $alcohol_level = $_POST['alcohol_level'];
    $smoke_level = $_POST['smoke_level'];

    try
    {
        // SQL文
        $sql = "INSERT INTO profile
        (user_id, user_name, mail_address, alcohol_level, smoke_level)
        VALUES
        (:user_id, :user_name, :mail_address, :alcohol_level, :smoke_level)";

        // SQL準備
        $stmt = $pdo->prepare($sql);

        // 値をセット
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':mail_address', $mail_address);
        $stmt->bindParam(':alcohol_level', $alcohol_level);
        $stmt->bindParam(':smoke_level', $smoke_level);

        // 実行
        $stmt->execute();

        echo "登録完了";

    }catch(Exception $e)
    {
        echo "登録失敗";
        echo $e->getMessage();
    }
}


?>