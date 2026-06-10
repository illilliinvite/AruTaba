let allPosts = [];

function loadPosts() {
    fetch("../backend/getchat.php")
        .then(res => res.json())
        .then(data => {
            allPosts = data.forum_history_list;
            updateSelectors();
            renderPosts();
        })
        .catch(err => console.error("読み込みエラー:", err));
}

function updateSelectors() {
    const yearSelect = document.getElementById("year-select");
    const monthSelect = document.getElementById("month-select");

    const currentYear = yearSelect.value;
    const currentMonth = monthSelect.value;

    const now = new Date();
    const thisYear = now.getFullYear();

    // 年セレクター：1990〜2038
    yearSelect.innerHTML = "";
    for (let y = 1990; y <= 2038; y++) {
        const opt = document.createElement("option");
        opt.value = y;
        opt.textContent = `${y}年`;
        yearSelect.appendChild(opt);
    }

    // 月セレクター
    monthSelect.innerHTML = "";
    for (let m = 1; m <= 12; m++) {
        const opt = document.createElement("option");
        opt.value = m;
        opt.textContent = `${m}月`;
        monthSelect.appendChild(opt);
    }

    yearSelect.value = currentYear || thisYear;
    monthSelect.value = currentMonth || (now.getMonth() + 1);
}

function renderPosts() {
    const board = document.getElementById("post-list");
    const selectedYear = parseInt(document.getElementById("year-select").value);
    const selectedMonth = parseInt(document.getElementById("month-select").value);

    const filtered = allPosts.filter(msg => {
        const d = new Date(msg.day);
        return d.getFullYear() === selectedYear && d.getMonth() + 1 === selectedMonth;
    });

    board.innerHTML = "";

    if (filtered.length === 0) {
        board.innerHTML = `<p style="color:#FDF0DC; font-size:13px; text-align:center; margin-top:20px;">この月の投稿はありません</p>`;
        return;
    }

    // 新しい順に並べて5件ずつ表示
    const sorted = [...filtered].reverse();
    const display = sorted.slice(0, 5);

    display.forEach(msg => {
        const initials = msg.user_name.slice(0, 2);
        const date = new Date(msg.day);
        const timeStr = `${date.getMonth()+1}/${date.getDate()} ${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`;

        const card = document.createElement("div");
        card.className = "post-card";
        card.innerHTML = `
            <div class="post-header">
                <span class="pin"></span>
                <div class="avatar">${initials}</div>
                <p class="post-name">${msg.user_name}</p>
                <p class="post-time">${timeStr}</p>
            </div>
            <p class="post-body">${msg.forum_history}</p>
        `;
        board.appendChild(card);
    });

    // 5件以上あればスクロールで残りも見れる旨を表示
    if (filtered.length > 5) {
        const note = document.createElement("p");
        note.style.cssText = "color:#FDF0DC; font-size:11px; text-align:center; margin:8px 0 0;";
        note.textContent = `他 ${filtered.length - 5} 件はスクロールして確認できます`;
        board.appendChild(note);

        // 残りも追加（スクロールで見れる）
        sorted.slice(5).forEach(msg => {
            const initials = msg.user_name.slice(0, 2);
            const date = new Date(msg.day);
            const timeStr = `${date.getMonth()+1}/${date.getDate()} ${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`;

            const card = document.createElement("div");
            card.className = "post-card";
            card.innerHTML = `
                <div class="post-header">
                    <span class="pin"></span>
                    <div class="avatar">${initials}</div>
                    <p class="post-name">${msg.user_name}</p>
                    <p class="post-time">${timeStr}</p>
                </div>
                <p class="post-body">${msg.forum_history}</p>
            `;
            board.appendChild(card);
        });
    }
}

function sendPost() {
    const text = document.getElementById("forum_history").value.trim();
    if (text === "") return;

    const formData = new URLSearchParams();
    formData.append("forum_history", text);

    fetch("../backend/postchat.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString()
    }).then(() => {
        document.getElementById("forum_history").value = "";
        loadPosts();
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadPosts();
    setInterval(loadPosts, 5000);

    document.getElementById("sendBtn").addEventListener("click", sendPost);
    document.getElementById("forum_history").addEventListener("keydown", (e) => {
        if (e.key === "Enter") sendPost();
    });

    document.getElementById("year-select").addEventListener("change", renderPosts);
    document.getElementById("month-select").addEventListener("change", renderPosts);
});