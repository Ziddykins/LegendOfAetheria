document.getElementById("chat-handle").addEventListener("click", (e) => {
    const chatbox    = document.getElementById("chatbox-bottom");
    const chatButton = document.getElementById("open-chat");
    const chatInput  = dpcument.getElementById("chat-input");
    if (chatbox.style.height === "300px") {
        chatbox.style.height = "35px";
        chatButton.textContent = "expand_less";
        chatInput.classList.toggle("invisible");
    } else {
        chatbox.style.height = "300px";
        chatButton.textContent = "expand_more";
        chatInput.classList.toggle("invisible");
    }
});