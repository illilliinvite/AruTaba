<?php

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

$stmt = $pdo->prepare("INSERT INTO users (name, age) VALUES (:name, :age)");

$name = "aaa";
$age = "5";
    // 値をバインドして実行
    $stmt->execute([
        ':name' => $name,
        ':age'  => $age
    ]);

    echo "データを保存しました";

?>