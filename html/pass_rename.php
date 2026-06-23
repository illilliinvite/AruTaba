<!-- register.html -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再設定</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>

    <div class="rename-container">

        <!-- タイトル -->
        <div class="rename-title">
            <h1>パスワード再設定</h1>
            <div class="title-line"></div>
        </div>

        <!-- 入力フォーム -->
        <form class="login-form" action="../backend/pass_rename.php" method="POST">

            <input type="email" name="mail_address" placeholder="メールアドレス" required>

            <input type="password" name="password" placeholder="パスワード" required>

            <input type="password" name="password_confirm" placeholder="パスワード（確認用）" required>

            <button type="submit" class="rename-button">
                登録
            </button>

            <a href="login.html" class="login-link">
                ログイン画面へ
            </a>

        </form>

    </div>

</body>
</html>