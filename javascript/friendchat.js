// ===========================
// 定数・設定
// ===========================

// 自動更新の間隔（ミリ秒）
const POLLING_INTERVAL = 3000;

// 最後に読み込んだmessage_idを記録（差分取得用）
let lastMessageId = 0;

// ポーリングのタイマーID
let pollingTimer = null;


// ===========================
// 初期化
// ===========================


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



document.addEventListener("DOMContentLoaded", () => {

    // Enterキーで送信
    document.getElementById("messageInput")
        .addEventListener("keypress", (e) => {
            if (e.key === "Enter") {
                sendMessage();
            }
        });

    // 初回メッセージ読み込み
    loadMessages(true);

    // 定期ポーリング開始
    pollingTimer = setInterval(() => {
        loadMessages(false);
    }, POLLING_INTERVAL);

});


// ===========================
// メッセージ読み込み
// ===========================

async function loadMessages(isInitial) {

    try {

        const response = await fetch(
            `../backend/friendchat_load.php?last_id=${lastMessageId}`
        );

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const messages = await response.json();

        // 読み込み中テキストを非表示
        if (isInitial) {
            const loadingMsg = document.getElementById("loadingMsg");
            if (loadingMsg) {
                loadingMsg.remove();
            }
        }

        if (!Array.isArray(messages) || messages.length === 0) {
            return;
        }

        const chatContainer = document.getElementById("chatContainer");
        const myUserId = getMyUserId(); // セッションのユーザーID取得

        // 日付区切りの管理
        let lastDate = getLastDisplayedDate();

        messages.forEach((msg) => {

            const msgDate = formatDate(msg.sent_at);

            // 日付が変わったら区切り線を挿入
            if (msgDate !== lastDate) {
                chatContainer.insertAdjacentHTML(
                    "beforeend",
                    `<div class="date-divider">${msgDate}</div>`
                );
                lastDate = msgDate;
            }

            const isMe = (String(msg.user_id) === String(myUserId));
            const time = formatTime(msg.sent_at);

            if (isMe) {
                // 自分のメッセージ
                chatContainer.insertAdjacentHTML(
                    "beforeend",
                    `<div class="message-row me" data-date="${msgDate}">
                        <div class="message-content">
                            <div class="bubble">${escapeHtml(msg.chat_history)}</div>
                            <div class="time">${time}</div>
                        </div>
                    </div>`
                );
            } else {
                // 相手のメッセージ
                chatContainer.insertAdjacentHTML(
                    "beforeend",
                    `<div class="message-row friend" data-date="${msgDate}">
                        <div class="friend-icon">👤</div>
                        <div class="message-content">
                            <div class="bubble">${escapeHtml(msg.chat_history)}</div>
                            <div class="time">${time}</div>
                        </div>
                    </div>`
                );
            }

            // 最新のmessage_idを更新
            if (parseInt(msg.message_id) > lastMessageId) {
                lastMessageId = parseInt(msg.message_id);
            }

        });

        // 一番下へスクロール（新規メッセージがあった場合）
        chatContainer.scrollTop = chatContainer.scrollHeight;

    } catch (error) {
        console.error("メッセージ読み込みエラー:", error);

        // 初回読み込み失敗時のみエラー表示
        if (isInitial) {
            const loadingMsg = document.getElementById("loadingMsg");
            if (loadingMsg) {
                loadingMsg.textContent = "メッセージの読み込みに失敗しました";
                loadingMsg.style.color = "#e74c3c";
            }
        }
    }

}


// ===========================
// メッセージ送信
// ===========================

async function sendMessage() {

    const input = document.getElementById("messageInput");
    const sendBtn = document.getElementById("sendBtn");
    const message = input.value.trim();

    if (message === "") {
        return;
    }

    // 二重送信防止
    sendBtn.disabled = true;

    try {

        const response = await fetch(
            "../backend/friendchat_send.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ message: message })
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            alert("送信に失敗しました。もう一度お試しください。");
            return;
        }

        // 入力欄クリア
        input.value = "";

        // 即座に表示を更新
        await loadMessages(false);

    } catch (error) {
        console.error("送信エラー:", error);
        alert("通信エラーが発生しました。");
    } finally {
        sendBtn.disabled = false;
        input.focus();
    }

}


// ===========================
// ユーティリティ
// ===========================

// セッションのユーザーIDをhtmlのmetaタグから取得
// ※ PHPでheadタグ内に <meta name="user-id" content="<?= $_SESSION['user_id'] ?>"> を出力する想定
function getMyUserId() {
    const meta = document.querySelector('meta[name="user-id"]');
    return meta ? meta.getAttribute("content") : null;
}

// 最後に表示した日付を取得（区切り線の重複防止）
function getLastDisplayedDate() {
    const dividers = document.querySelectorAll(".date-divider");
    if (dividers.length === 0) return null;
    return dividers[dividers.length - 1].textContent;
}

// 日時文字列から「YYYY年M月D日」形式に変換
function formatDate(datetimeStr) {
    if (!datetimeStr) return "";
    const d = new Date(datetimeStr);
    if (isNaN(d)) return "";
    return `${d.getFullYear()}年${d.getMonth() + 1}月${d.getDate()}日`;
}

// 日時文字列から「HH:MM」形式に変換
function formatTime(datetimeStr) {
    if (!datetimeStr) {
        // sent_atがない場合は現在時刻
        const now = new Date();
        return String(now.getHours()).padStart(2, "0") + ":" +
               String(now.getMinutes()).padStart(2, "0");
    }
    const d = new Date(datetimeStr);
    if (isNaN(d)) return "";
    return String(d.getHours()).padStart(2, "0") + ":" +
           String(d.getMinutes()).padStart(2, "0");
}

// XSS対策：HTMLエスケープ
function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
}

const urlParams = new URLSearchParams(window.location.search);
const friendMail = urlParams.get("mail");
