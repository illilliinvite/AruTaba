<?php

//DB接続情報
$host = "localhost";
$dbname = "arutaba";
$user = "root";
$pass = "arutaba";

try{

    //mysql接続
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );

    //エラー表示
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //フォームから受け取り
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    //SQL
    $sql = "SELECT * FROM login
            WHERE user_id = :user_id
            AND password = :password";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam('password', $password);

    $stmt->execute();

    //ユーザーが存在するか
    if($stmt->rowCount() > 0){
        //echo "ログイン成功";

        //home画面へ飛ばしたい場合
        session_start();
        
        $_SESSION["user_id"] = $user_id;


        header("Location: ../html/home.html");
        exit();    

    }
    else{
        echo "ユーザーIDまたはパスワードが違います";
    }   
}catch(PDOException $e){
    echo "接続失敗" . $e->getMessage();
}


?>