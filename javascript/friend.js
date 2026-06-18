console.log("friend.js 読み込まれた");

// ===== アバターカラー =====
const AVATAR_COLORS = ['blue', 'green', 'amber', 'pink', 'teal', 'purple'];

function getAvatarColor(name) {
    let code = 0;
    for (let i = 0; i < name.length; i++) code += name.charCodeAt(i);
    return AVATAR_COLORS[code % AVATAR_COLORS.length];
}

function getInitials(name) {
    if (!name) return '?';
    return name.slice(0, 2).toUpperCase();
}

// ===== フレンド一覧取得 =====
function loadFriends() {
    fetch("../backend/friend_llist.php")
        .then(res => res.json())
        .then(data => {
            const ul          = document.getElementById("friend-list-ul");
            const badge       = document.getElementById("badge-friend");
            const countBadge  = document.getElementById("friend-count-badge");
            ul.innerHTML = "";

            if (data.status !== "ok" || data.friends.length === 0) {
                ul.innerHTML = '<li class="empty-message">フレンドはいません</li>';
                badge.textContent    = "0";
                countBadge.textContent = "0人";
                return;
            }

            const count = data.friends.length;
            badge.textContent      = count;
            countBadge.textContent = count + "人";

            data.friends.forEach(item => {
                const name  = item.user_name    ?? "(名前なし)";
                const mail  = item.mail_address ?? "";
                const color = getAvatarColor(name);

                const li = document.createElement("li");
                li.style.cursor = "pointer";

                li.innerHTML = `
                    <div class="friend-avatar avatar-${color}">${getInitials(name)}</div>
                    <div class="friend-name-block">
                        <span class="friend-name">${name}</span>
                        ${mail ? `<span class="friend-mail">${mail}</span>` : ""}
                    </div>
                    <button class="delete-friend" data-mail="${mail}">削除</button>
                `;

                // 削除ボタンに直接登録（li より先に）
                li.querySelector(".delete-friend").addEventListener("click", (e) => {
                    e.stopPropagation();
                    if (!confirm(`${mail} をフレンドから削除しますか？`)) return;

                    fetch(`../backend/friend_delete.php?mail=${encodeURIComponent(mail)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "ok") {
                                alert("削除しました");
                                loadFriends();
                            } else {
                                alert("エラー：" + data.message);
                            }
                        })
                        .catch(() => alert("通信エラー"));
                });

                // li クリック → チャットへ
                li.addEventListener("click", () => {
                    window.location.href = `friendchat.php?mail=${encodeURIComponent(mail)}`;
                });

                ul.appendChild(li);
            });
        })
        .catch(() => alert("フレンド一覧の取得に失敗しました"));
}

// ===== 承認待ち取得 =====
function loadRequests() {
    fetch("../backend/friend_recive.php")
        .then(res => res.json())
        .then(data => {
            const ul    = document.querySelector("#request-list ul");
            const badge = document.getElementById("badge-request");
            ul.innerHTML = "";

            if (data.status !== "ok" || data.requests.length === 0) {
                ul.innerHTML = '<li class="empty-message">承認待ちはありません</li>';
                badge.style.display = "none";
                return;
            }

            badge.textContent   = data.requests.length;
            badge.style.display = "inline-flex";

            data.requests.forEach(item => {
                const name  = item.user_name ?? "(名前なし)";
                const color = getAvatarColor(name);

                const li = document.createElement("li");
                li.innerHTML = `
                    <div class="friend-avatar avatar-${color}">${getInitials(name)}</div>
                    <div class="friend-name-block">
                        <span class="friend-name">${name}</span>
                    </div>
                    <button class="approve" data-user-id="${item.user_id}">承認</button>
                `;
                ul.appendChild(li);
            });
        })
        .catch(() => alert("承認待ちの取得に失敗しました"));
}

// ===== DOMContentLoaded =====
document.addEventListener("DOMContentLoaded", () => {

    // フレンド申請
    document.getElementById("search-btn").addEventListener("click", () => {
        const friendId = document.getElementById("friend-search").value.trim();
        if (friendId === "") { alert("検索ワードを入力してください"); return; }

        fetch("../backend/friend.send.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ friend_id: friendId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "ok") {
                alert("フレンド申請を送信しました");
                document.getElementById("friend-search").value = "";
            } else {
                alert("エラー：" + data.message);
            }
        })
        .catch(() => alert("通信エラー"));
    });

    // タブ切り替え
    const tabFriend   = document.getElementById("tab-friend");
    const tabRequest  = document.getElementById("tab-request");
    const friendList  = document.getElementById("friend-list");
    const requestList = document.getElementById("request-list");

    tabFriend.addEventListener("click", () => {
        tabFriend.classList.add("active");
        tabRequest.classList.remove("active");
        friendList.classList.add("active");
        requestList.classList.remove("active");
    });

    tabRequest.addEventListener("click", () => {
        tabRequest.classList.add("active");
        tabFriend.classList.remove("active");
        requestList.classList.add("active");
        friendList.classList.remove("active");
    });

    // 初期読み込み
    loadFriends();
    loadRequests();
});

// ===== 承認 & 削除（イベント委譲） =====
document.addEventListener("click", function(e) {

    // 承認ボタン
    if (e.target.classList.contains("approve")) {
        const userId = e.target.dataset.userId;

        fetch(`../backend/firend_syounin.php?user_id=${encodeURIComponent(userId)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === "ok") {
                    alert("承認しました");
                    loadRequests();
                } else {
                    alert("エラー：" + data.message);
                }
            })
            .catch(() => alert("通信エラー"));
    }

    // 削除ボタン
    if (e.target.classList.contains("delete-friend")) {
        e.stopPropagation(); // li のチャット遷移を防ぐ

        const mail = e.target.dataset.mail;
        if (!confirm(`${mail} をフレンドから削除しますか？`)) return;

        fetch(`../backend/friend_delete.php?mail=${encodeURIComponent(mail)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === "ok") {
                    alert("削除しました");
                    loadFriends();   // location.reload() より軽量
                } else {
                    alert("エラー：" + data.message);
                }
            })
            .catch(() => alert("通信エラー"));
    }
});