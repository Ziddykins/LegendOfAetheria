    const chatbox        = document.getElementById('chatbox-bottom');
    const chat_handle    = document.getElementById('chat-handle');
    const chat_content   = document.getElementById('chat-content');
    const chat_input     = document.getElementById('chat-input');
    const open_chat_btn  = document.getElementById('open-chat');
    const close_chat_btn = document.getElementById('close-chat-btn');
    const online_count   = document.getElementById('online-count');

document.getElementById("chat-handle").addEventListener("click", (e) => {
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
        const chat_msg = {message: message, char_id: loa.u_cid, room: '!main', nickname: loa.u_name, action: 'add_msg'};
        
        if (!message || message.length === 0 || /^\s*$/.test(message)) {
            return;
        }

        do_post('chat', 'add_msg', chat_msg);

    } else if (e.keyCode == 38) { //up
        if (loa.chat_pos == loa.chat_history.length) {
            return;
        } else {
            loa.chat_pos++;
            chat_input.value = loa.chat_history[loa.chat_pos];
        }
    } else if (e.keyCode == 40) { //down
        if (loa.chat_pos < 1) {
            return;
        } else {
            loa.chat_pos--;
            chat_input.value = loa.chat_history[loa.chat_pos];
        }
    }
});


document.addEventListener('DOMContentLoaded', () => {
    var messages = null;
    let is_open = false;

    chat_handle.addEventListener('click', () => {
        online_count.innerText = do_post('chat', 'get_online');
        messages = do_post('chat', 'get_msgs');
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
});

function gen_message(msg) {
    if (!msg) {
        return;
    }
	console.log("in gen_message(msg) ");
    console.log(msg);
    console.log(typeof(msg));
    let chat_msg = document.createElement('div');
    let nick_len = msg.nickname.length;
    let max_len = 10;
    let spaces = max_len - nick_len;
    let final_nick = "&nbsp;".repeat(spaces) + msg.nickname;
    chat_msg.id = `chat-msg-${msg.id}`;
    chat_msg.innerHTML = `<span class="text-warning text-end">${final_nick}:</span><span> ${msg.message}</span>`;

    return chat_msg;
}

function do_post(uri, directive, data=null) {
    let body_data = null;

    switch(directive) {
        case 'get_online':
            body_data = { action: 'get_online' };
            break;
        case 'get_msgs':
            body_data = {
                action: 'get_msgs',
                room: '!main',
                count: 20
            };
            break;
        case 'add_msg':
            body_data = {
                action: 'add_msg',
                data: data,
                room: '!main',
            };
            break;
    }

    fetch('chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(body_data)
        }).then((response) => {
            if (!response.ok) {
                throw new Error("couldn't get messages");
            } else {
                return response.json();
            }
        }).then((json) => {
            
            switch(body_data.action) {
                case 'get_msgs':
                    const html_msg = gen_message(data);

                    if (chat_content.children.length > 20) {
                        chat_content.lastChild.remove();
                    }

                    chat_content.prepend(html_msg);
                    loa.chat_history.push(data.message); // Push the message to chat history
                    break;
            }
        });
};