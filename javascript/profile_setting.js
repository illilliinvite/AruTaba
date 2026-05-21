document.addEventListener("DOMContentLoaded", async () => {
  try {
    const response = await fetch("../backend/profile_setting.php", {
      method: "POST"
    });

    if (!response.ok) {
      throw new Error("通信に失敗しました");
    }

    const result = await response.text();
    console.log(result);
  } catch (error) {
    console.error(error);
  }
});