// ===============================
// 新規チャットだけ追加するためのカウンタ
// ===============================
let lastCount = 0;


// ===============================
// iframe 内の処理
// ===============================
function initIframeFunctions() {
    const frame = document.querySelector("iframe").contentWindow;

    frame.addMessage = function(data) {
        const chatList = frame.document.getElementById("chat-list");

        const div = frame.document.createElement("div");
        div.classList.add("message", data.side);

        div.innerHTML = `
            <div class="icon">${data.user}</div>
            <div class="bubble">${data.text}</div>
        `;

        chatList.appendChild(div);

        frame.scrollTo({
            top: frame.document.body.scrollHeight,
            behavior: "smooth"
        });
    };

    frame.scrollToBottom = function() {
        frame.scrollTo({
            top: frame.document.body.scrollHeight,
            behavior: "smooth"
        });
    };

    // 初回読み込み
    loadChatHistory();

    // 1秒ごとに更新
    setInterval(loadChatHistory, 1000);
}


// ===============================
// 過去ログを読み込む（新規だけ追加）
// ===============================
function loadChatHistory() {
    fetch("../backend/getchat.php")
        .then(res => res.json())
        .then(data => {
            const list = data.forum_history_list;
            const frame = document.querySelector("iframe").contentWindow;

            const myName = document.getElementById("session_user_name").value;

            // ★ 新しいチャットだけ追加
            for (let i = lastCount; i < list.length; i++) {
                const msg = list[i];

                const side = (msg.user_name === myName) ? "right" : "left";

                frame.addMessage({
                    user: msg.user_name,
                    text: msg.forum_history,
                    side: side
                });
            }

            lastCount = list.length;

            frame.scrollToBottom();
        })
        .catch(err => {
            console.error("読み込みエラー:", err);
        });
}


// ===============================
// 送信処理
// ===============================
function sendChat() {
    const user = document.getElementById("session_user_name").value;
    const text = document.getElementById("forum_history").value.trim();

    if (text === "") return;

    const frame = document.querySelector("iframe").contentWindow;

    frame.addMessage({
        user: user,
        text: text,
        side: "right"
    });

    const formData = new URLSearchParams();
    formData.append("user_name", user);
    formData.append("forum_history", text);

    fetch("../backend/postchat.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString()
    });

    document.getElementById("forum_history").value = "";
}


// ===============================
// イベント設定
// ===============================
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("sendBtn").addEventListener("click", sendChat);

    document.getElementById("forum_history").addEventListener("keydown", (e) => {
        if (e.key === "Enter") sendChat();
    });

    document.querySelector("iframe").addEventListener("load", initIframeFunctions);
});
