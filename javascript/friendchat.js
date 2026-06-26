// ===========================
// 定数・設定
// ===========================
const POLLING_INTERVAL = 3000;
let lastMessageId = 0;
let pollingTimer = null;

// ===========================
// 初期化
// ===========================
document.addEventListener("DOMContentLoaded", async () => {
    await fetchUserByMail(); // ← async 関数内なら await が使える
    await loadMessages(true);

    document.getElementById("messageInput")
        .addEventListener("keypress", (e) => {
            if (e.key === "Enter") {
                sendMessage();
            }
        });

    pollingTimer = setInterval(() => {
        loadMessages(false);
    }, POLLING_INTERVAL);
});



// ===========================
// URLパラメータ mail を取得
// ===========================
function getMailFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get("mail");
}

// ===========================
// mail を PHP に送信してユーザー情報取得
// ===========================
async function fetchUserByMail() {
    const mail = getMailFromUrl();
    if (!mail) {
        console.warn("mail パラメータがありません");
        return;
    }

    try {
        const response = await fetch("../backend/friendchat_load.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ mail: mail })
        });

        const result = await response.json();
        console.log("ユーザー情報:", result);

        if (result.success && result.user) {
            // 必要ならここでユーザー情報を画面に反映できる
            console.log("ログインユーザー:", result.user);
        }

    } catch (error) {
        console.error("ユーザー取得エラー:", error);
    }
}

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

        if (isInitial) {
            const loadingMsg = document.getElementById("loadingMsg");
            if (loadingMsg) loadingMsg.remove();
        }

        if (!Array.isArray(messages) || messages.length === 0) return;

        const chatContainer = document.getElementById("chatContainer");
        const myUserId = getMyUserId();

        let lastDate = getLastDisplayedDate();

        messages.forEach((msg) => {
            const msgDate = formatDate(msg.sent_at);

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

            if (parseInt(msg.message_id) > lastMessageId) {
                lastMessageId = parseInt(msg.message_id);
            }
        });

        chatContainer.scrollTop = chatContainer.scrollHeight;

    } catch (error) {
        console.error("メッセージ読み込みエラー:", Merror);

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

    if (message === "") return;

    sendBtn.disabled = true;

    try {
        const response = await fetch("../backend/friendchat_send.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message: message })
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            alert("送信に失敗しました。もう一度お試しください。");
            return;
        }

        input.value = "";
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
function getMyUserId() {
    const meta = document.querySelector('meta[name="user-id"]');
    return meta ? meta.getAttribute("content") : null;
}

function getLastDisplayedDate() {
    const dividers = document.querySelectorAll(".date-divider");
    if (dividers.length === 0) return null;
    return dividers[dividers.length - 1].textContent;
}

function formatDate(datetimeStr) {
    if (!datetimeStr) return "";
    const d = new Date(datetimeStr);
    if (isNaN(d)) return "";
    return `${d.getFullYear()}年${d.getMonth() + 1}月${d.getDate()}日`;
}

function formatTime(datetimeStr) {
    if (!datetimeStr) {
        const now = new Date();
        return String(now.getHours()).padStart(2, "0") + ":" +
               String(now.getMinutes()).padStart(2, "0");
    }
    const d = new Date(datetimeStr);
    if (isNaN(d)) return "";
    return String(d.getHours()).padStart(2, "0") + ":" +
           String(d.getMinutes()).padStart(2, "0");
}

function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
}