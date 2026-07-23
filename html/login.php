<!-- login.html -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>

    <div class="login-container">

        <!-- タイトル -->
        <div class="login-title">
            <h1>ログイン</h1>
            <div class="title-line"></div>
        </div>

        <!-- エラーメッセージ（JSで必要な時だけ表示） -->
        <p class="login-error" id="loginError" style="display:none;"></p>

        <!-- 入力フォーム -->
        <form class="login-form" action="../backend/login.php" method="POST">

            <input type="email" name="email" placeholder="メールアドレス" required>

            <input type="password" name="password" placeholder="パスワード" required>

            <a href="pass_rename.php" class="forgot-password">
                パスワードを忘れた方
            </a>

            <button type="submit" class="login-button">
                ログイン
            </button>

            <a href="register.php" class="register-link">
                アカウント新規登録
            </a>

        </form>

    </div>

    <script>
        (() => {
            const params = new URLSearchParams(window.location.search);
            const errorCode = params.get("error");
            if (!errorCode) return;

            const messages = {
                "1": "メールアドレスまたはパスワードが違います",
                "2": "サーバーエラーが発生しました。しばらくしてから再度お試しください"
            };

            const el = document.getElementById("loginError");
            el.textContent = messages[errorCode] || "ログインに失敗しました";
            el.style.display = "block";

            // URLからクエリを消す（リロード時に再表示されないように）
            window.history.replaceState({}, document.title, window.location.pathname);
        })();
    </script>

</body>
</html>