function sendMessage() {

    const input =
        document.getElementById("messageInput");

    const message =
        input.value.trim();

    if (message === "") {
        return;
    }

    const now = new Date();

    const time =
        String(now.getHours()).padStart(2, "0")
        + ":" +
        String(now.getMinutes()).padStart(2, "0");

    const chatContainer =
        document.getElementById("chatContainer");

    chatContainer.insertAdjacentHTML(
        "beforeend",
        `
        <div class="message-row me">
            <div class="message-content">

                <div class="bubble me-bubble">
                    ${message}
                </div>

                <div class="time">
                    ${time}
                </div>

            </div>
        </div>
        `
    );

    input.value = "";

    chatContainer.scrollTop =
        chatContainer.scrollHeight;
}


// Enterキーで送信
document.addEventListener("DOMContentLoaded", () => {

    const input =
        document.getElementById("messageInput");

    input.addEventListener("keypress", (e) => {

        if (e.key === "Enter") {
            sendMessage();
        }

    });

});