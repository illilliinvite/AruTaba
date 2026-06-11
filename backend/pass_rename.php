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

$mail_address = $_POST["mail_address"];
$password = $_POST["password"];
$password_confirm = $_POST["password_confirm"];

/* パスワード一致確認 */
if($password !== $password_confirm){
    die("パスワードが一致しません");
}

/* メールアドレス確認 */
$sql = "SELECT user_id
        FROM login
        WHERE mail_address = :mail_address";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':mail_address', $mail_address);
$stmt->execute();

if(!$stmt->fetch()){
    die("メールアドレスが存在しません");
}

/* パスワード更新 */
$sql = "UPDATE login
        SET password = :password
        WHERE mail_address = :mail_address";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':password', $password);
$stmt->bindParam(':mail_address', $mail_address);

if($stmt->execute()){
    header("Location: ../html/login.html");
    exit;
}else{
    echo "パスワード変更に失敗しました";
}
?>