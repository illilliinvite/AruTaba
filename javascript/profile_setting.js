console.log("JS読み込み成功");

document.addEventListener("DOMContentLoaded", () => {

  const iconWrapper = document.getElementById("iconWrapper");
  const iconInput = document.getElementById("iconInput");
  const iconImage = document.getElementById("iconImage");
  const iconPlaceholder = document.getElementById("iconPlaceholder");
  const message = document.getElementById("message");
  const profileForm = document.getElementById("profileForm");

  // メッセージ表示の共通処理
  function showMessage(text, isSuccess) {
    message.textContent = text;
    message.style.display = "block";
    message.style.color = isSuccess ? "green" : "red";

    setTimeout(() => {
      message.style.display = "none";
    }, 3000);
  }

  // アイコン画像をプレビューに反映する共通処理
  function setIconPreview(url) {
    iconImage.src = url;
    iconImage.style.display = "block";
    iconPlaceholder.style.display = "none";
    iconWrapper.classList.add("has-image");
  }

  // ページ読み込み時：既存のアイコンがあれば表示する
  async function loadCurrentIcon() {
    try {
      const formData = new FormData();
      formData.append("action", "get_profile");

      const response = await fetch("../backend/profile_setting.php", {
        method: "POST",
        body: formData
      });

      if (!response.ok) return;

      const data = await response.json();

      if (data.icon_path) {
        setIconPreview(data.icon_path);
      }

    } catch (error) {
      console.error("アイコン取得失敗", error);
    }
  }

  loadCurrentIcon();

  // アイコンクリックでファイル選択を開く
  iconWrapper.addEventListener("click", () => {
    iconInput.click();
  });

  // ファイル選択時：即アップロード
  iconInput.addEventListener("change", async () => {

    const file = iconInput.files[0];

    if (!file) return;

    // 選択直後にローカルプレビューを先行表示
    const localPreviewUrl = URL.createObjectURL(file);
    setIconPreview(localPreviewUrl);

    const formData = new FormData();
    formData.append("action", "upload_icon");
    formData.append("icon_image", file);

    try {
      const response = await fetch("../backend/profile_setting.php", {
        method: "POST",
        body: formData
      });

      if (!response.ok) {
        throw new Error("通信失敗");
      }

      const result = await response.json();

      if (result.status === "success") {
        // サーバー保存後の本来のパスに置き換え
        setIconPreview(result.icon_path);
        showMessage("アイコンを更新しました", true);
      } else {
        showMessage(result.message || "アイコン更新失敗", false);
      }

    } catch (error) {
      showMessage("アイコン更新失敗", false);
      console.error(error);
    } finally {
      URL.revokeObjectURL(localPreviewUrl);
    }
  });

  // プロフィールフォーム送信
  profileForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(profileForm);
    formData.append("action", "update_profile");

    try {
      const response = await fetch("../backend/profile_setting.php", {
        method: "POST",
        body: formData
      });

      if (!response.ok) {
        throw new Error("通信失敗");
      }

      const result = await response.text();

      showMessage(result, result.includes("成功"));

    } catch (error) {
      showMessage("更新失敗", false);
      console.error(error);
    }
  });

});