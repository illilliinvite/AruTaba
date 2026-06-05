// ===============================
// iframe 内の処理（chatview.js 相当）
// ===============================

// iframe 内の DOM が読み込まれたら実行
function initIframeFunctions() {
    const frame = document.querySelector("iframe").contentWindow;

    // iframe 内に関数を注入
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

    // 過去ログ読み込み
    loadChatHistory();
}


// ===============================
// 過去ログを読み込む（getchat.php）
// ===============================
function loadChatHistory() {
    fetch("../backend/getchat.php")
        .then(res => res.json())
        .then(data => {
            const list = data.forum_history_list;
            const frame = document.querySelector("iframe").contentWindow;

            list.forEach(text => {
                frame.addMessage({
                    user: "相手",
                    text: text,
                    side: "left"
                });
            });

            frame.scrollToBottom();
        })
        .catch(err => {
            console.error("読み込みエラー:", err);
        });
}


// ===============================
// 送信ボタンのイベント
// ===============================
document.addEventListener("DOMContentLoaded", () => {
    const sendBtn = document.getElementById("sendBtn");

    sendBtn.addEventListener("click", sendChat);

    // Enterキーでも送信
    document.getElementById("forum_history").addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            sendChat();
        }
    });

    // iframe が読み込まれたら関数を注入
    document.querySelector("iframe").addEventListener("load", initIframeFunctions);
});


// ===============================
// チャット送信処理
// ===============================
function sendChat() {
    const user = "me";
    const text = document.getElementById("forum_history").value.trim();

    if (user === "" || text === "") return;

    const frame = document.querySelector("iframe").contentWindow;

    // iframe 内にメッセージを追加
    if (frame && typeof frame.addMessage === "function") {
        frame.addMessage({
            user: "me",
            text: text,
            side: "right"
        });
    }

    // PHP に送信（$_POST 形式）
    const formData = new URLSearchParams();
    formData.append("user_name", user);
    formData.append("forum_history", text);

    fetch("../backend/postchat.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData.toString()
    })
    .then(res => res.text())
    .then(data => {
        console.log("送信成功:", data);
    })
    .catch(err => {
        console.error("送信エラー:", err);
    });

    // 入力欄をクリア
    document.getElementById("forum_history").value = "";
}
