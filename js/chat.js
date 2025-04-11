document.getElementById("chat-handle").addEventListener("click", (e) => {
    const chatbox     = document.getElementById("chatbox-bottom");
    const chat_button = document.getElementById("open-chat");
    const chat_input  = document.getElementById("chat-input");
    const chat_online = document.getElementById("chat-online");

    if (chatbox.style.height === "300px") {
        chatbox.style.height = "35px";
        chat_button.textContent = "expand_less";
        chat_input.focus();
        chat_input.classList.toggle("invisible");
    } else {
        chatbox.style.height = "300px";
        chat_button.textContent = "expand_more";
        chat_input.classList.toggle("invisible");
    }
});

document.getElementById("chat-input").addEventListener("keyup", (e) => {
    if (e.keyCode == 13) {
        message = document.getElementById("chat-input").value.trim();
        document.getElementById("chat-input").value = "";
        const chat_msg = {message: message, char_id: loa.u_cid, room: '!main', nickname: loa.u_name, action: 'add'};
        const chat_content = document.getElementById('chat-content');
        
        if (!message || message.length === 0 || /^\s*$/.test(message)) {
            return;
        }

        fetch('chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(chat_msg)
        }).then((response) => {
            if (!response.ok) {
                throw new Error('couldnt add message');
            } else {
                const html_msg = gen_message(chat_msg);
                if (chat_content.children.length > 20) {
                    chat_content.lastChild.remove();
                }
                chat_content.prepend(html_msg);
                loa.chat_history.push(message);
            }
        }).catch((error) => {
            console.error(error);
        })
    } else if (e.keyCode == 38) { //up

    } else if (e.keyCode == 40) { //down

    }
});


document.addEventListener('DOMContentLoaded', () => {
    const chatbox = document.getElementById('chatbox-bottom');
    const chat_handle = document.getElementById('chat-handle');
    const chat_content = document.getElementById('chat-content');
    const chat_input = document.getElementById('chat-input');
    const open_chat_btn = document.getElementById('open-chat');
    const close_chat_btn = document.getElementById('close-chat-btn');
    let is_open = false;

    chat_handle.addEventListener('click', () => {
        is_open = !is_open;
        if (is_open) {
            chatbox.style.height = 'calc(100vh - 50px)';
            chat_content.style.display = 'block';
            chat_input.style.display = 'block';
            open_chat_btn.textContent = 'expand_more';
            close_chat_btn.style.display = 'block';
        } else {
            chatbox.style.height = '10px';
            chat_content.style.display = 'none';
            chat_input.style.display = 'none';
            open_chat_btn.textContent = 'expand_less';
            close_chat_btn.style.display = 'none';
        }
    });

    messages = null;

    ["get", "online"].forEach((actn) => {
    
});

function gen_message(msg) {
    console.log(msg);
    console.log(typeof(msg));
    let chat_msg = document.createElement('div');
    let nick_len = msg['nickname'].length;
    let max_len = 10;
    let spaces = max_len - nick_len;
    let final_nick = "&nbsp;".repeat(spaces) + msg.nickname;
    chat_msg.id = `chat-msg-${msg.id}`;
    chat_msg.innerHTML = `<span class="text-warning text-end">${final_nick}:</span><span> ${msg.message}</span>`;

    return chat_msg;
}

