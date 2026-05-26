console.log("friend.js 読み込まれた");

document.addEventListener("DOMContentLoaded", () => {


        const searchBtn = document.getElementById("search-btn");

        searchBtn.addEventListener("click", () => {
            const friendId = document.getElementById("friend-search").value;

            

            // PHP に friend_id を送信
            fetch("../backend/friend_kensaku.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    friend_id: friendId
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);

                if (data.status === "ok") {
                    alert("フレンド申請を送りました！");
                } else {
                    alert("エラー：" + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert("通信エラー");
            });
        });
        // ============================
    // タブ切り替え
    // ============================
    const tabFriend = document.getElementById("tab-friend");
    const tabRequest = document.getElementById("tab-request");

    const friendList = document.getElementById("friend-list");
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


    // ============================
    // フレンド検索 → PHP へ送信
    // ============================


});

