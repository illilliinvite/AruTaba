<?php
session_start();
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
            <img src="../image/default_icon.png" id="chatHeaderIcon" class="chat-header-icon" alt="">
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

        // ===== アイコン画像の反映（friend_llist.php と同じ考え方） =====
        const iconEl = document.getElementById("chatHeaderIcon");
        const path = result.user.icon_path;

        if (path) {
            // すでに "../" 始まりならそのまま、そうでなければ付与
            iconEl.src = path.startsWith("../") ? path : "../" + path;
        }
        // pathが無ければ最初に設定した default_icon.png のまま
    }

})();

</script>

</body>
</html>