<?php
    require_once "../backend/session.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<style>
    body {
        margin: 0;
        padding: 20px;
        background: #f5f5f5;
        font-family: sans-serif;
    }

    .message {
        display: flex;
        margin-bottom: 15px;
        align-items: flex-start;
    }

    .message.left {
        flex-direction: row;
    }

    .message.right {
        flex-direction: row-reverse;
    }

    .icon {
        width: 40px;
        height: 40px;
        background: #ddd;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 10px;
    }

    .bubble {
        background: white;
        padding: 12px 16px;
        border-radius: 10px;
        max-width: 70%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
</head>

<body>

<div id="chat-list">
    <!-- JS でチャットログが流れるように追加される -->
</div>

<script src="../javascript/keiziban.js"></script>

<script>
function scrollToBottom() {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: "smooth"
    });
}
</script>

</body>
</html>
