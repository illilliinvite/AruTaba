// 要素取得
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");

//アイコンの読み込み
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

// クリックで開閉
hamburger.addEventListener("click", () => {
    sidebar.classList.toggle("active");
});


// headerアイコン
document.addEventListener("DOMContentLoaded", async () => {
 
  const headerProfileImage =
    document.getElementById("headerProfileImage");
 
  if (!headerProfileImage) return;
 
  try {
 
    const formData = new FormData();
    formData.append("action", "get_profile");
 
    const response = await fetch(
      "../backend/profile_setting.php",
      {
        method: "POST",
        body: formData
      }
    );
 
    const data = await response.json();
 
    if (data.icon_path) {
      headerProfileImage.src = data.icon_path;
    }
 
  } catch (error) {
    console.error(error);
  }
 
});