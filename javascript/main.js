// 要素取得
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");

// クリックで開閉
hamburger.addEventListener("click", () => {
    sidebar.classList.toggle("active");
});
