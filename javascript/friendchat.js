const POLLING_INTERVAL = 3000;
let lastMessageId = 0;
let pollingTimer = null;

document.addEventListener("DOMContentLoaded", async () => {
    await loadMessages(true);

    document.getElementById("messageInput").addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            sendMessage();
        }
    });

    document.getElementById("sendBtn").addEventListener("click", () => {
        sendMessage();
    });

    pollingTimer = setInterval(() => {
        loadMessages(false);
    }, POLLING_INTERVAL);
});

function getMailFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get("mail");
}

async function loadMessages(isInitial) {
    try {
        const mail = getMailFromUrl();

        if (!mail) {
            throw new Error("mail パラメータがありません");
        }

        const response = await fetch(
            `../backend/friendchat_load.php?last_id=${lastMessageId}&mail=${encodeURIComponent(mail)}`
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

            if (parseInt(msg.message_id, 10) > lastMessageId) {
                lastMessageId = parseInt(msg.message_id, 10);
            }
        });

        chatContainer.scrollTop = chatContainer.scrollHeight;

    } catch (error) {
        console.error("メッセージ読み込みエラー:", error);

        if (isInitial) {
            const loadingMsg = document.getElementById("loadingMsg");
            if (loadingMsg) {
                loadingMsg.textContent = "メッセージの読み込みに失敗しました";
                loadingMsg.style.color = "#e74c3c";
            }
        }
    }
}

async function sendMessage() {
    const input = document.getElementById("messageInput");
    const sendBtn = document.getElementById("sendBtn");
    const message = input.value.trim();
    const mail = getMailFromUrl();

    if (message === "") return;

    if (!mail) {
        alert("送信先が取得できませんでした。");
        return;
    }

    sendBtn.disabled = true;

    try {
        const response = await fetch("../backend/friendchat_send.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                message: message,
                mail: mail
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            alert(result.error || "送信に失敗しました。もう一度お試しください。");
            console.log(result);
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
    if (!datetimeStr) return "";
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
