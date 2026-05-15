<?php

try {

    $pdo = new PDO(
        "mysql:host=localhost;dbname=sample;charset=utf8",
        "root",
        "arutaba"
    );

    echo "接続成功";

} catch (PDOException $e) {

    echo "接続失敗: " . $e->getMessage();

}