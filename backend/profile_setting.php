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

    echo "更新失敗";
    exit;
}


// POST確認
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // フォーム取得
    $user_name = $_POST["user_name"];
    $mail_address = $_POST["mail_address"];
    $password = $_POST["password"];
    $smoking_limit = $_POST["smoking_limit"];
    $drinking_limit = $_POST["drinking_limit"];

    // 空欄チェック
    if (
        empty($user_name) ||
        empty($mail_address) ||
        empty($password) ||
        $smoking_limit === "" ||
        $drinking_limit === ""
    ) {
        echo "更新失敗";
        exit;
    }

    // セッション取得
    $user_id = $_SESSION["user_id"];

    try {

        // 前のユーザーネーム取得
        $sql = "select user_name from profile where user_id = :user_id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":user_id", $_SESSION["user_id"], PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $user_name_before = $result["user_name"];


        //フレンド一覧名前変更sql
        $sql = "update friend set user_name = :user_name where user_name = :user_name_before";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->bindParam(":user_name_before", $user_name_before, PDO::PARAM_STR);

        $stmt->execute();




        //掲示板名前変更sql
        $sql = "update forum set user_name = :user_name where user_name = :user_name_before";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->bindParam(":user_name_before", $user_name_before, PDO::PARAM_STR);

        $stmt->execute();





        // profileテーブル更新
        $sql = "UPDATE profile
                SET
                    user_name = :user_name,
                    mail_address = :mail_address
                WHERE
                    user_id = :user_id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->bindParam(":mail_address", $mail_address, PDO::PARAM_STR);

        $stmt->execute();

        // loginテーブル更新
        $sql = "UPDATE login
                SET
                    user_name = :user_name,
                    mail_address = :mail_address,
                    password = :password
                WHERE
                    user_id = :user_id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->bindParam(":mail_address", $mail_address, PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);

        $stmt->execute();

        // goal_valueテーブルにuser_idが存在するか確認
        $sql = "SELECT COUNT(*)
                FROM goal_value
                WHERE user_id = :user_id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);

        $stmt->execute();

        $count = $stmt->fetchColumn();

        if ($count > 0) {

            // 存在する場合はUPDATE
            $sql = "UPDATE goal_value
                    SET
                        alcohol_limit = :alcohol_limit,
                        ciggarette_limit = :ciggarette_limit
                    WHERE
                        user_id = :user_id";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
            $stmt->bindParam(":alcohol_limit", $drinking_limit, PDO::PARAM_INT);
            $stmt->bindParam(":ciggarette_limit", $smoking_limit, PDO::PARAM_INT);

            $stmt->execute();

        } else {

            // 存在しない場合はINSERT
            $sql = "INSERT INTO goal_value
                    (
                        user_id,
                        alcohol_limit,
                        ciggarette_limit
                    )
                    VALUES
                    (
                        :user_id,
                        :alcohol_limit,
                        :ciggarette_limit
                    )";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
            $stmt->bindParam(":alcohol_limit", $drinking_limit, PDO::PARAM_INT);
            $stmt->bindParam(":ciggarette_limit", $smoking_limit, PDO::PARAM_INT);

            $stmt->execute();
        }

        echo "更新成功";



    } catch(PDOException $e) {

        echo $e->getMessage();
    }
}

?>