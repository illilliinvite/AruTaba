<?php
    $host = "localhost";
    $dbname = "arutaba";
    $user = "root";
    $pass = "arutaba";

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

    $user_name = $_POST["user_name"];
    $mail_address = $_POST["mail_address"];
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    if($password !== $password_confirm){
        die("パスワードが一致しません");
    }

    // メールアドレス重複確認
    $sql = "SELECT * FROM login WHERE mail_address = :mail_address";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':mail_address', $mail_address);
    $stmt->execute();

    if($stmt->fetch()){
        die("このメールは既に登録されています");
    }

    $user_id = uniqid("user_");

    // 🔧 トランザクション開始（両テーブルへの登録を一体化）
    try{
        $pdo->beginTransaction();

        // loginテーブルに登録
        $sql = "INSERT INTO login (user_id, user_name, password, mail_address)
                VALUES (:user_id, :user_name, :password, :mail_address)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':mail_address', $mail_address);
        $stmt->execute();

        // 🔧 profileテーブルにも登録（alcohol_level・smoke_levelは0）
        $sql = "INSERT INTO profile (user_id, user_name, mail_address, alcohol_level, smoke_level)
                VALUES (:user_id, :user_name, :mail_address, 0, 0)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':mail_address', $mail_address);
        $stmt->execute();

        $pdo->commit();

        header("Location: ../html/login.html");
        exit;

    }catch(PDOException $e){
        // 🔧 どちらか失敗したら両方ロールバック
        $pdo->rollBack();
        echo "登録失敗：" . $e->getMessage();
    }

?>