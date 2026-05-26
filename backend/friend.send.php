<?php

if(!isset($_POST["friend_id"]))
{
    echo "nothing";
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "session.php";

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=arutaba;charset=utf8",
        "root",
        "arutaba"
    );
} catch (Exception $e) {
    echo json_encode(["error" => "db connection failed"]);
    exit;
}

$stmt = $pdo->prepare("
    insert into friend(user_id, friend_id, friend_wait, friend) values(:user_id, :friend_id, 1, 0)
");

$user_id = $_SESSION["user_id"];
$friend_id = $_POST["friend_id"];
$stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);

echo "登録成功!!";
exit;

?>