var chatbox = document.getElementById('chatbox-bottom');
var chat_handle = document.getElementById('chat-handle');
var chat_content = document.getElementById('chat-content');
var chat_input = document.getElementById('chat-input');
var open_chat_btn = document.getElementById('open-chat');
var online_count = document.getElementById('online-count');
var max_height = Math.round(window.getComputedStyle(chat_content).getPropertyValue("max-height").replace('px', ''));
var line_height = 16.77;
var msg_count = Math.round(max_height/line_height) - 4;
console.log(`max_height: ${max_height} - line_height = ${line_height} - msg_count: ${msg_count}`);

document.getElementById("chat-handle").addEventListener("click", (e) => {
    if (chatbox.style.height === "300px") {
        chatbox.style.height = "35px";
        open_chat_btn.textContent = "expand_less";
        chat_input.focus();
        chat_input.classList.toggle("invisible");
    } else {
        chatbox.style.height = "300px";
        open_chat_btn.textContent = "expand_more";
        chat_input.classList.toggle("invisible");
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
        } else {
            chatbox.style.height = '10px';
            chat_content.style.display = 'none';
            chat_input.style.display = 'none';
            open_chat_btn.textContent = 'expand_less';
        }
    });

    messages = null;
});

function gen_message(msg) {
    if (!msg) {
        return;
    }
    if (!document.getElementById(`chat-msg-${msg.id}`)) {
        let chat_msg = document.createElement('div');
        let nick_len = msg.nickname.length;
        let max_len = 10;
        let spaces = max_len - nick_len;
        let final_nick = "&nbsp;".repeat(spaces) + msg.nickname;

        chat_msg.id = `chat-msg-${msg.id}`;
        chat_msg.innerHTML = `<span class="text-warning text-end">${final_nick}:</span><span> ${msg.message}</span>`;

        //return chat_msg;
    }
    return null;
}

function do_post(uri, directive, data = null) {
    let body_data = null;

    switch (directive) {
        case 'get_online':
            body_data = { action: 'get_online' };
            break;
        case 'get_msgs':
            console.log("getting msgs");
            body_data = {
                action: 'get_msgs',
                room: '!main',
                count: msg_count
            };
            break;
        case 'add_msg':
            console.log("adding msgs");
            body_data = {
                action: 'add_msg',
                data: data,
                room: '!main',
            };
            break;
    }

    fetch('chat/chat', {
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
        switch (body_data.action) {
            case 'get_msgs':
                var html_msg = null;

                console.log(json);
                json.forEach((msg) => {
                    html_msg = gen_message(msg);
                    var no_more = 0;
                    if (html_msg != null) {
                        if (chat_content.children.length > (max_height / line_height)) {
                            no_more = 1;
                        }
                        if (!no_more) {
                            chat_content.prepend(html_msg);
                        }
                    }
                });
            break;

            case 'add_msg':
                const new_msg = gen_message(data);
                if (chat_content.children.length >= msg_count) {
                    chat_content.lastChild.remove();
                }
                    console.log(new_msg);
                    chat_content.prepend(new_msg);

            break;
        }
    });
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

document.getElementById("chat-input").addEventListener("keydown", debounce((e) => {
    if (e.keyCode == 13) {
        const message = document.getElementById("chat-input").value.trim();

        if (!message || message.length === 0 || /^\s*$/.test(message)) {
            return;
        }

        const chat_msg = { message: message, char_id: loa.u_cid, room: '!main', nickname: loa.u_name, action: 'add_msg' };

        document.getElementById("chat-input").value = "";
        loa.chat_history.push(message);
        loa.chat_pos = loa.chat_history.length;
        do_post('chat', 'add_msg', chat_msg);

    } else if (e.keyCode == 38) {
        e.preventDefault();
        if (loa.chat_pos > 0) {
            loa.chat_pos--;
            chat_input.value = loa.chat_history[loa.chat_pos];
        } else {
            chat_input.value = "";
        }
    } else if (e.keyCode == 40) {
        e.preventDefault();
        if (loa.chat_pos < loa.chat_history.length - 1) {
            loa.chat_pos++;
            chat_input.value = loa.chat_history[loa.chat_pos];
        } else {
            chat_input.value = "";
            loa.chat_pos = loa.chat_history.length;
        }
    }
    console.log(`${loa.chat_history.length} - ${loa.chat_pos}`);
}, 300));