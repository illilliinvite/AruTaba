<?php
    //DB情報
    $host = "localhost";
    $dbname = "arutaba";
    $user = "root";
    $pass = "arutaba";

    //データベース接続
    try{
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $pass
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }catch(PDOException $e){
        die("接続失敗：" . $e->getMessage());
    }

    //フォーム受け取り
    $user_name = $_POST["user_name"];
    $mail_address = $_POST["mail_address"];
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    //パスワード一致確認
    if($password !== $password_confirm){
        die("パスワードが一致しません");
    }

    //メールアドレス重複確認
    $sql = "SELECT * FROM login WHERE mail_address = :mail_address";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':mail_address', $mail_address);

    $stmt->execute();

    $result = $stmt->fetch();

    if($result){
        die("このメールは既に登録されています");
    }

    //user_id自動生成
    $user_id =  uniqid("user_");

    //登録SQL
    $sql = "
    INSERT INTO login
    (user_id, user_name, password, mail_address)
    
    VALUES
    (:user_id, :user_name, :password, :mail_address)
    ";

    $stmt = $pdo->prepare($sql);

    //値セット
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':mail_address', $mail_address);

    //実行
    try{
        $stmt->execute();

        echo "登録成功";

    }catch(PDOException $e){
        echo "登録失敗：" . $e->getMessage();
    }

?>