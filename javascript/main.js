// 要素取得
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");

// アイコンの読み込み
async function loadUserIcon() {
    const formData = new FormData();
    formData.append("action", "get_profile");

    const response = await fetch("../backend/profile_setting.php", {
        method: "POST",
        body: formData
    });

    const data = await response.json();

    if (data.icon_path) {
        const headerIcon = document.getElementById("headerIcon");
        if (headerIcon) {
            headerIcon.src = data.icon_path;
        }
    }
}

// ユーザー名の読み込み
async function loadUserName() {
    try {
        const response = await fetch("../backend/getname.php");
        const data = await response.json();

        if (data.status === "ok") {
            document.getElementById("userName").textContent =
                data.user_name ?? "ユーザー名未設定";
        }
    } catch (error) {
        console.error("ユーザー名取得エラー:", error);
    }
}

// 初期読み込み
loadUserIcon();
loadUserName();

// ハンバーガーメニュー
hamburger.addEventListener("click", () => {
    sidebar.classList.toggle("active");
});

async function loadUserIcon() {
    try {
        const response = await fetch("../backend/getpicture.php");
        const data = await response.json();

        if (data.status === "ok" && data.icon_path) {
            document.getElementById("headerIcon").src = data.icon_path;
        }
    } catch (error) {
        console.error("アイコン取得エラー:", error);
    }
}
