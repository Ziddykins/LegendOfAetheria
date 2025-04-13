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

        do_post('chat', chat_msg);

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
    const chatbox        = document.getElementById('chatbox-bottom');
    const chat_handle    = document.getElementById('chat-handle');
    const chat_content   = document.getElementById('chat-content');
    const chat_input     = document.getElementById('chat-input');
    const open_chat_btn  = document.getElementById('open-chat');
    const close_chat_btn = document.getElementById('close-chat-btn');
    const online_count   = document.getElementById('online-count');
    var messages = null;
    let is_open = false;

    var obj_getmsgs = {
        action: 'get_msgs',
        room: '!main',
        count: 20
    };

    var obj_ocount = {
        action: 'online_count',
    };

    chat_handle.addEventListener('click', () => {
        online_count.innerText = do_post('chat', obj_ocount);
        messages = do_post('chat', obj_getmsgs);

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
	console.log("in gen_message(msg) ");
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

function do_post(uri, body_data) {
	console.log("in do_post(uri, body_data) ");
    
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
                case 'add':
                    const html_msg = gen_message(chat_msg);

                    if (chat_content.children.length > 20) {
                        chat_content.lastChild.remove();
                    }

                    chat_content.prepend(html_msg);
                    loa.chat_history.push(message);
                    break;
            }
        });
};