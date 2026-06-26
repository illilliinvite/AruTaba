<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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


// action振り分け（未指定の場合は従来通りプロフィール更新として扱う）
$action = $_POST["action"] ?? "update_profile";


// ==========================================
// アイコン画像アップロード処理
// ==========================================
if ($action === "upload_icon") {

    header("Content-Type: application/json; charset=utf-8");

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["status" => "error", "message" => "未ログイン"]);
        exit;
    }

    $user_id = $_SESSION["user_id"];

    if (!isset($_FILES["icon_image"]) || $_FILES["icon_image"]["error"] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "ファイルが選択されていません"]);
        exit;
    }

    $file = $_FILES["icon_image"];

    // 許可する画像形式
    $allowed_types = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/gif"  => "gif",
        "image/webp" => "webp",
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file["tmp_name"]);
    finfo_close($finfo);

    if (!isset($allowed_types[$mime_type])) {
        echo json_encode(["status" => "error", "message" => "対応していない画像形式です"]);
        exit;
    }

    // ファイルサイズ上限（5MB）
    $max_size = 5 * 1024 * 1024;

    if ($file["size"] > $max_size) {
        echo json_encode(["status" => "error", "message" => "ファイルサイズが大きすぎます（5MBまで）"]);
        exit;
    }

    // 保存先ディレクトリ（backendの一つ上の階層にuploads/iconsを作成）
    $upload_dir = __DIR__ . "/../uploads/icons/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $extension = $allowed_types[$mime_type];
    $filename = $user_id . "_" . time() . "." . $extension;
    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        echo json_encode(["status" => "error", "message" => "ファイル保存に失敗しました"]);
        exit;
    }

    // クライアントから見えるパス（profile_setting.htmlは /html/ 配下にある想定）
    $icon_path_for_client = "../uploads/icons/" . $filename;
    // DBに保存するパス（プロジェクトルートからの相対パス）
    $icon_path_for_db = "uploads/icons/" . $filename;

    try {

        // 古いアイコンファイルを取得（容量節約のため後で削除）
        $sql = "SELECT icon_path FROM profile WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $old = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "UPDATE profile
                SET icon_path = :icon_path
                WHERE user_id = :user_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":icon_path", $icon_path_for_db, PDO::PARAM_STR);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();



        $sql = "SELECT user_name FROM profile WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);  // $user_id に修正
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = $row["user_name"];  // 配列から文字列を取り出す

        // forum テーブル更新
        $sql = "UPDATE forum SET icon_path = :icon_path WHERE user_name = :user_name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":icon_path", $icon_path_for_db, PDO::PARAM_STR);
        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->execute();

        //掲示板名前変更sql


        $sql = "update forum set icon_path = :icon_path where user_name = :user_name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":icon_path", $icon_path_for_db, PDO::PARAM_STR);
        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->execute();

        if ($old && !empty($old["icon_path"])) {
            $old_file = __DIR__ . "/../" . $old["icon_path"];
            if (is_file($old_file)) {
                unlink($old_file);
            }
        }

        echo json_encode([
            "status" => "success",
            "icon_path" => $icon_path_for_client
        ]);

    } catch (PDOException $e) {

        echo json_encode(["status" => "error", "message" => "DB更新失敗"]);
    }

    exit;
}


// ==========================================
// プロフィール取得処理（フォームの初期表示用）
// アイコンに加えて、名前・メール・喫煙/飲酒上限も返す
// ==========================================
if ($action === "get_profile") {

    header("Content-Type: application/json; charset=utf-8");

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["status" => "error", "message" => "未ログイン"]);
        exit;
    }

    $user_id = $_SESSION["user_id"];

    try {

        // プロフィール情報（名前・メール・アイコン）
        $sql = "SELECT user_name, mail_address, icon_path FROM profile WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        // 喫煙・飲酒の上限値（goal_valueにまだ行が無いユーザーもいる）
        $sql = "SELECT ciggarette_limit, alcohol_limit FROM goal_value WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);

        $icon_path = null;

        if ($profile && !empty($profile["icon_path"])) {
            // HTMLは /html/ 配下にある想定なので、相対パスを ../ に変換
            $icon_path = "../" . $profile["icon_path"];
        }

        echo json_encode([
            "status" => "success",
            "icon_path" => $icon_path,
            "user_name" => $profile ? $profile["user_name"] : null,
            "mail_address" => $profile ? $profile["mail_address"] : null,
            "smoking_limit" => $goal ? $goal["ciggarette_limit"] : null,
            "drinking_limit" => $goal ? $goal["alcohol_limit"] : null
        ]);

    } catch (PDOException $e) {

        echo json_encode(["status" => "error", "message" => "取得失敗"]);
    }

    exit;
}


// ==========================================
// アカウント削除処理
// ==========================================
if ($action === "delete_account") {

    header("Content-Type: application/json; charset=utf-8");

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["status" => "error", "message" => "未ログイン"]);
        exit;
    }

    $user_id = $_SESSION["user_id"];

    // パスワード確認（本人確認のため再入力必須）
    $password = $_POST["password"] ?? "";

    if ($password === "") {
        echo json_encode(["status" => "error", "message" => "パスワードを入力してください"]);
        exit;
    }

    try {

        // パスワード一致確認
        $sql = "SELECT password FROM login WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $login_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$login_row || $login_row["password"] !== $password) {
            echo json_encode(["status" => "error", "message" => "パスワードが正しくありません"]);
            exit;
        }

        // friend / forum は user_name で紐づいているため、先に user_name を取得
        $sql = "SELECT user_name FROM profile WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $profile_row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = $profile_row["user_name"] ?? null;

        // 削除前にアイコンファイルパスを取得（DB削除後にファイル削除するため）
        $sql = "SELECT icon_path FROM profile WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $icon_row = $stmt->fetch(PDO::FETCH_ASSOC);
        $icon_path = $icon_row["icon_path"] ?? null;

        // ここからトランザクション開始（途中で失敗したら全部ロールバック）
        $pdo->beginTransaction();

        // calender テーブル（user_id）
        $sql = "DELETE FROM calender WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();

        // friend_chat テーブル（user_id・receiver_idの両方に存在しうるため両方条件で削除）
        $sql = "DELETE FROM friend_chat WHERE user_id = :user_id OR receiver_id = :receiver_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->bindParam(":receiver_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();

        // goal_value テーブル（user_id）
        $sql = "DELETE FROM goal_value WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();

        // friend テーブル（user_name）
        if ($user_name !== null) {
            $sql = "DELETE FROM friend WHERE user_name = :user_name";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
            $stmt->execute();
        }

        // forum テーブル（user_name）
        if ($user_name !== null) {
            $sql = "DELETE FROM forum WHERE user_name = :user_name";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
            $stmt->execute();
        }

        // profile テーブル（user_id）
        $sql = "DELETE FROM profile WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();

        // login テーブル（user_id、主キー）最後に削除
        $sql = "DELETE FROM login WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->execute();

        // すべて成功したらコミット
        $pdo->commit();

        // DB削除が成功した後にアイコンファイルを削除（DB削除より後に行うことで、
        // ファイル削除が先に成功してDB側が失敗する事態を避ける）
        if (!empty($icon_path)) {
            $icon_file = __DIR__ . "/../" . $icon_path;
            if (is_file($icon_file)) {
                unlink($icon_file);
            }
        }

        // セッション破棄
        $_SESSION = [];
        session_destroy();

        echo json_encode(["status" => "success", "message" => "アカウントを削除しました"]);

    } catch (PDOException $e) {

        // トランザクション中であればロールバック
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo json_encode(["status" => "error", "message" => "削除失敗: " . $e->getMessage()]);
    }

    exit;
}


// ==========================================
// プロフィール更新処理（既存処理）
// パスワードは「変更する場合のみ入力」のため必須にしない
// ==========================================
if ($action === "update_profile" && $_SERVER["REQUEST_METHOD"] === "POST") {

    // フォーム取得
    $user_name = $_POST["user_name"];
    $mail_address = $_POST["mail_address"];
    $password = $_POST["password"]; // 空欄なら今回は変更しない
    $smoking_limit = $_POST["smoking_limit"];
    $drinking_limit = $_POST["drinking_limit"];

    // 空欄チェック（パスワードは対象外）
    if (
        empty($user_name) ||
        empty($mail_address) ||
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

        // loginテーブル更新（パスワードが入力されている場合のみ更新）
        if ($password !== "") {

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

        } else {

            $sql = "UPDATE login
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
        }

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