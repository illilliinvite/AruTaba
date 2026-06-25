<!-- register.html -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>

    <div class="resister-container">

        <!-- タイトル -->
        <div class="resister-title">
            <h1>新規登録</h1>
            <div class="title-line"></div>
        </div>

        <!-- 入力フォーム -->
        <form class="login-form" action="../backend/register.php" method="POST">

            <input type="text" name="user_name" placeholder="ユーザー名" required>

            <input type="email" name="mail_address" placeholder="メールアドレス" required>

            <input type="password" name="password" placeholder="パスワード" required>

            <input type="password" name="password_confirm" placeholder="パスワード（確認用）" required>

            <button type="submit" class="resister-button">
                登録
            </button>

            <a href="login.php" class="login-link">
                ログイン画面へ
            </a>

        </form>

    </div>

</body>
</html>