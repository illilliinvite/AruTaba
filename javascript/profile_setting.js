console.log("JS読み込み成功");

document.getElementById("profileForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const form = document.getElementById("profileForm");
  const message = document.getElementById("message");
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

    message.textContent = result;
    message.style.display = "block";
    message.style.color = result.includes("成功") ? "green" : "red";

    setTimeout(() => {
      message.style.display = "none";
    }, 3000);

  } catch (error) {
    message.textContent = "更新失敗";
    message.style.color = "red";
    message.style.display = "block";

    setTimeout(() => {
      message.style.display = "none";
    }, 3000);

    console.error(error);
  }

});