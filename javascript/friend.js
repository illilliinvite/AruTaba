document.addEventListener("DOMContentLoaded", () => {
    const tabFriend = document.getElementById("tab-friend");
    const tabRequest = document.getElementById("tab-request");

    const friendList = document.getElementById("friend-list");
    const requestList = document.getElementById("request-list");

    // タブ切り替え
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
});
