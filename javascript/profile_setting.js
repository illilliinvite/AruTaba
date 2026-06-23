console.log("JS読み込み成功");

document.addEventListener("DOMContentLoaded", () => {

  const iconWrapper = document.getElementById("iconWrapper");
  const iconInput = document.getElementById("iconInput");
  const iconImage = document.getElementById("iconImage");
  const iconPlaceholder = document.getElementById("iconPlaceholder");
  const message = document.getElementById("message");
  const profileForm = document.getElementById("profileForm");

  // 削除関連の要素
  const openDeleteModalBtn = document.getElementById("openDeleteModalBtn");
  const deleteModalOverlay = document.getElementById("deleteModalOverlay");
  const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  const deleteConfirmPassword = document.getElementById("deleteConfirmPassword");

  // パスワード表示切り替え関連の要素
  const passwordInput = document.getElementById("passwordInput");
  const togglePassword = document.getElementById("togglePassword");

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

  // ページ読み込み時：登録済みのプロフィール情報をフォームに反映する
  // （アイコン・名前・メールアドレス・喫煙/飲酒上限）
  // パスワードのみ、セキュリティ上の理由で取得・表示せず空欄のままにする
  async function loadCurrentProfile() {
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

      if (data.user_name !== undefined && data.user_name !== null) {
        profileForm.elements["user_name"].value = data.user_name;
      }

      if (data.mail_address !== undefined && data.mail_address !== null) {
        profileForm.elements["mail_address"].value = data.mail_address;
      }

      if (data.smoking_limit !== undefined && data.smoking_limit !== null) {
        profileForm.elements["smoking_limit"].value = data.smoking_limit;
      }

      if (data.drinking_limit !== undefined && data.drinking_limit !== null) {
        profileForm.elements["drinking_limit"].value = data.drinking_limit;
      }

    } catch (error) {
      console.error("プロフィール取得失敗", error);
    }
  }

  loadCurrentProfile();

  // アイコンクリックでファイル選択を開く
  iconWrapper.addEventListener("click", () => {
    iconInput.click();
  });

  // キーボード操作（Enter / Space）でもファイル選択を開けるようにする
  iconWrapper.addEventListener("keydown", (e) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      iconInput.click();
    }
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


  // ==========================================
  // パスワード表示切り替え
  // ==========================================

  if (passwordInput && togglePassword) {
    togglePassword.addEventListener("click", () => {
      const willShow = passwordInput.type === "password";
      passwordInput.type = willShow ? "text" : "password";

      togglePassword.classList.toggle("fa-eye", !willShow);
      togglePassword.classList.toggle("fa-eye-slash", willShow);
      togglePassword.setAttribute(
        "aria-label",
        willShow ? "パスワードを非表示" : "パスワードを表示"
      );
    });
  }


  // ==========================================
  // アカウント削除処理
  // ==========================================

  function openDeleteModal() {
    deleteConfirmPassword.value = "";
    deleteModalOverlay.classList.add("show");
  }

  function closeDeleteModal() {
    deleteModalOverlay.classList.remove("show");
    deleteConfirmPassword.value = "";
  }

  openDeleteModalBtn.addEventListener("click", () => {
    openDeleteModal();
  });

  cancelDeleteBtn.addEventListener("click", () => {
    closeDeleteModal();
  });

  // モーダルの外側をクリックしたら閉じる
  deleteModalOverlay.addEventListener("click", (e) => {
    if (e.target === deleteModalOverlay) {
      closeDeleteModal();
    }
  });

  confirmDeleteBtn.addEventListener("click", async () => {

    const password = deleteConfirmPassword.value;

    if (!password) {
      showMessage("パスワードを入力してください", false);
      return;
    }

    // 二重送信防止
    confirmDeleteBtn.disabled = true;

    const formData = new FormData();
    formData.append("action", "delete_account");
    formData.append("password", password);

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
        closeDeleteModal();
        showMessage("アカウントを削除しました", true);
        // セッションが破棄されているのでログイン画面等へ遷移
        setTimeout(() => {
          window.location.href = "login.php";
        }, 1500);
      } else {
        showMessage(result.message || "削除に失敗しました", false);
      }

    } catch (error) {
      showMessage("削除に失敗しました", false);
      console.error(error);
    } finally {
      confirmDeleteBtn.disabled = false;
    }
  });

});