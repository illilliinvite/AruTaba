console.log("JS読み込み成功");

document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("profileForm");
  const message = document.getElementById("message");

  form.addEventListener("submit", async (e) => {

    // ページ遷移停止
    e.preventDefault();

    // フォームデータ取得
    const formData = new FormData(form);

    try {

      const response = await fetch("../backend/profile_setting.php", {
        method: "POST",
        body: formData
      });

      if (!response.ok) {
        throw new Error("通信失敗");
      }

      const result = await response.text();

      // メッセージ表示
      message.textContent = result;

      // 色変更
      if (result.includes("成功")) {
        message.style.color = "green";
      } else {
        message.style.color = "red";
      }

    } catch (error) {

  message.textContent = "更新失敗";
  message.style.color = "red";

  console.error(error);
}

  });

});