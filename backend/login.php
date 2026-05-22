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

    // 🔧 user_id → email に変更
    // 🔧 POST受け取り
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🔧 カラム名を mail_address に合わせる
    $sql = "SELECT * FROM login
            WHERE mail_address = :email
            AND password = :password";

    $stmt = $pdo->prepare($sql);

    // 🔧 バインド変数をemailに変更
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password); // 🐛 元のコードはコロンが抜けていたので修正

    $stmt->execute();

    if($stmt->rowCount() > 0){

        session_start();
        
        $sql = "SELECT user_id FROM login
            WHERE mail_address = :email
            AND password = :password";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        $stmt->execute();

        $user_id = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔧 セッションにuser_idを保存
        $_SESSION["user_id"] = $user_id['user_id'];

        header("Location: ../html/home.html");
        exit();    

    }
    else{
        echo "メールアドレスまたはパスワードが違います";
    }   
}catch(PDOException $e){
    echo "接続失敗" . $e->getMessage();
}

?>