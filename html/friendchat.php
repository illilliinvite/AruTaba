<?php
    require_once "../backend/session.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="<?= htmlspecialchars($_SESSION['user_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <title>AruTaba - チャット</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/friendchat.css">
</head>
<body>

    <?php include "header.php" ?>

    <!-- オーバーレイ（サイドバー展開時） -->
    <div class="overlay" id="overlay"></div>

    <!-- チャットエリア -->
    <main class="chat-wrapper">
        <!-- 相手のヘッダー情報 -->
        <div class="chat-header">
            <div class="chat-header-icon">👤</div>
            <div class="chat-header-name" id="friendName"></div>
            <div class="chat-header-status">オンライン</div>
        </div>

        <!-- メッセージ一覧 -->
        <div class="chat-container" id="chatContainer">
            <!-- メッセージはJSで動的に追加 -->
            <div class="loading-msg" id="loadingMsg">読み込み中…</div>
        </div>

        <!-- 入力エリア -->
        <div class="chat-input-area">
            <button class="plus-btn" title="ファイル添付">＋</button>
            <input type="file" id="fileInput" accept="image/*,video/*" hidden>
            <input
                type="text"
                id="messageInput"
                placeholder="メッセージを入力"
                autocomplete="off"
            >
            <button class="send-btn" id="sendBtn">送信</button>
        </div>
    </main>

    <!-- フッター -->
    <footer class="footer"></footer>

    <script src="../javascript/main.js"></script>
    <script src="../javascript/friendchat.js"></script>
    <script>

(async () => {

    const urlParams = new URLSearchParams(window.location.search);
    const friendMail = urlParams.get("mail");

    const response = await fetch(
        "../backend/friendchat_mail.php",
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                mail: friendMail
            })
        }
    );

    const result = await response.json();

    console.log(result);
    if (result.success && result.user) {
        document.getElementById("friendName").textContent = result.user.user_name;
    }

})();

</script>

</body>
</html>
