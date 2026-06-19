const POLLING_INTERVAL = 3000;
let lastMessageId = 0;

document.addEventListener("DOMContentLoaded", async () => {

    await loadMessages(true);

    document.getElementById("messageInput").addEventListener("keypress", (e) => {
        if (e.key === "Enter") sendMessage();
    });

    document.getElementById("sendBtn").addEventListener("click", sendMessage);

    // ＋ボタンでファイル選択を開く
    document.querySelector(".plus-btn").addEventListener("click", () => {
        document.getElementById("fileInput").click();
    });

    // ファイルが選択されたらアップロード
    document.getElementById("fileInput").addEventListener("change", async (e) => {
        const file = e.target.files[0];
        if (file) {
            await sendFile(file);
            e.target.value = "";
        }
    });

    setInterval(async () => {
        await loadMessages(false);
    }, POLLING_INTERVAL);
});

function getMailFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get("mail");
}

async function loadMessages(isInitial) {

    const mail = getMailFromUrl();

    const res = await fetch(
        `../backend/friendchat_load.php?last_id=${lastMessageId}&mail=${encodeURIComponent(mail)}`
    );

    const messages = await res.json();

    if (!Array.isArray(messages)) return;

    const chatContainer = document.getElementById("chatContainer");
    const myUserId = getMyUserId();

    if (isInitial) {
        document.getElementById("loadingMsg")?.remove();
    }

    // 新しいメッセージを追加する前に、現在「下付近」にいるか判定しておく
    const SCROLL_THRESHOLD = 80; // px の許容誤差
    const wasNearBottom =
        chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < SCROLL_THRESHOLD;

    let appendedAny = false;

    messages.forEach(msg => {

        const isMe = String(msg.user_id) === String(myUserId);
        const time = formatTime(msg.sent_at);
        const bubbleContent = renderBubbleContent(msg);

        const html = isMe
            ? `
            <div class="message-row me" data-message-id="${msg.message_id}">
                <div class="message-content">
                    <div class="bubble">${bubbleContent}</div>
                    <div class="time">
                        ${time}
                        ${msg.is_read == 1 ? '<span class="read-status">既読</span>' : ''}
                    </div>
                </div>
            </div>`
            : `
            <div class="message-row friend" data-message-id="${msg.message_id}" data-read="${msg.is_read == 1 ? '1' : '0'}">
                <div class="friend-icon">👤</div>
                <div class="message-content">
                    <div class="bubble">${bubbleContent}</div>
                    <div class="time">${time}</div>
                </div>
            </div>`;

        const temp = document.createElement("div");
        temp.innerHTML = html.trim();
        const node = temp.firstElementChild;

        chatContainer.appendChild(node);
        appendedAny = true;

        if (!isMe) {
            observer.observe(node);
        }

        lastMessageId = Math.max(lastMessageId, Number(msg.message_id));
    });

    // 初回表示時、または「もともと下付近にいた」場合のみ自動スクロール
    if (appendedAny && (isInitial || wasNearBottom)) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

function renderBubbleContent(msg) {
    const basePath = "../"; // html/ から AruTaba直下へ

    switch (msg.message_type) {
        case "image":
            return `<img src="${basePath}${escapeHtml(msg.file_path)}" class="chat-image" loading="lazy">`;
        case "video":
            return `<video src="${basePath}${escapeHtml(msg.file_path)}" class="chat-video" controls></video>`;
        default:
            return escapeHtml(msg.chat_history);
    }
}

async function sendMessage() {

    const input = document.getElementById("messageInput");
    const mail = getMailFromUrl();
    const message = input.value.trim();

    if (!message) return;

    await fetch("../backend/friendchat_send.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ message, mail })
    });

    input.value = "";
    await loadMessages(false);
}

async function sendFile(file) {

    const mail = getMailFromUrl();

    const formData = new FormData();
    formData.append("file", file);
    formData.append("mail", mail);

    const res = await fetch("../backend/friendchat_upload.php", {
        method: "POST",
        body: formData
    });

    const result = await res.json();

    if (!result.success) {
        alert(result.error || "送信に失敗しました");
        return;
    }

    await loadMessages(false);
}

function getMyUserId() {
    return document.querySelector('meta[name="user-id"]')?.content;
}

function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
}

function formatTime(datetimeStr) {
    const d = new Date(datetimeStr);
    return String(d.getHours()).padStart(2, "0") + ":" +
           String(d.getMinutes()).padStart(2, "0");
}

const observer = new IntersectionObserver(async (entries) => {
    for (const entry of entries) {

        if (!entry.isIntersecting) continue;

        const el = entry.target;

        const messageId = el.dataset.messageId;
        if (!messageId) continue;

        if (el.dataset.read === "1") continue;

        el.dataset.read = "1";

        markAsReadSingle(messageId);

        observer.unobserve(el);
    }
}, {
    threshold: 0.8
});

async function markAsReadSingle(messageId) {

    await fetch("../backend/mark_read_single.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            message_id: messageId
        })
    });
}