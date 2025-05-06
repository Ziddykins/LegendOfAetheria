// Chat element references
const chatbox = document.getElementById('chatbox-bottom');
const chat_handle = document.getElementById('chat-handle');
const chat_content = document.getElementById('chat-content');
const chat_input = document.getElementById('chat-input');
const open_chat_btn = document.getElementById('open-chat');
const online_count = document.getElementById('online-count');

// Chat configuration
const max_height = Math.round(window.getComputedStyle(chat_content).getPropertyValue("max-height").replace('px', ''));
const line_height = 16.77;
const msg_count = Math.round(max_height/line_height) - 4;
const POLL_INTERVAL = 3000; // Poll every 3 seconds

// Chat state
let is_open = false;
let lastMessageId = 0;
let pollInterval = null;

// Initialize chat state
function initializeChat() {
    chatbox.style.height = '35px';
    chat_content.style.display = 'none';
    chat_input.style.display = 'none';
    open_chat_btn.textContent = 'expand_less';
    fetchOnlineCount();
}

function toggleChat() {
    is_open = !is_open;
    
    if (is_open) {
        chatbox.style.height = 'calc(100vh - 50px)';
        chat_content.style.display = 'block';
        chat_input.style.display = 'block';
        open_chat_btn.textContent = 'expand_more';
        fetchOnlineCount();
        fetchMessages(true); // Initial fetch
        startPolling();
    } else {
        chatbox.style.height = '35px';
        setTimeout(() => {
            if (!is_open) {
                chat_content.style.display = 'none';
                chat_input.style.display = 'none';
            }
        }, 300);
        open_chat_btn.textContent = 'expand_less';
        stopPolling();
    }
}

function startPolling() {
    if (!pollInterval) {
        pollInterval = setInterval(() => {
            if (is_open) {
                fetchOnlineCount();
                fetchMessages(false);
            }
        }, POLL_INTERVAL);
    }
}

function stopPolling() {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}

// Event listeners
function setupEventListeners() {
    // Remove any existing listeners first
    chat_handle.removeEventListener('click', toggleChat);
    chat_handle.addEventListener('click', toggleChat);
    
    chat_input.removeEventListener('keydown', handleChatInput);
    chat_input.addEventListener('keydown', debounce(handleChatInput, 300));

    
    // Cleanup on page unload
    window.addEventListener('unload', () => {
        stopPolling();
    });
}

function handleChatInput(e) {
    if (e.keyCode === 13) {
        const message = chat_input.value.trim();

        if (!message || message.length === 0 || /^\s*$/.test(message)) {
            return;
        }

        const chat_msg = { 
            message: message, 
            char_id: loa.u_cid, 
            room: '!main', 
            nickname: loa.u_name, 
            action: 'add_msg' 
        };

        chat_input.value = "";
        loa.chat_history.push(message);
        loa.chat_pos = loa.chat_history.length;
        
        do_post('chat', 'add_msg', chat_msg).then(() => {
            fetchMessages(false);
        });
    } else if (e.keyCode === 38) {
        e.preventDefault();
        if (loa.chat_pos > 0) {
            loa.chat_pos--;
            chat_input.value = loa.chat_history[loa.chat_pos];
        } else {
            chat_input.value = "";
        }
    } else if (e.keyCode === 40) {
        e.preventDefault();
        if (loa.chat_pos < loa.chat_history.length - 1) {
            loa.chat_pos++;
            chat_input.value = loa.chat_history[loa.chat_pos];
        } else {
            chat_input.value = "";
            loa.chat_pos = loa.chat_history.length;
        }
    }
}

// Message handling
function fetchOnlineCount() {
    do_post('chat', 'get_online').then(count => {
        if (count && count.online) {
            online_count.innerText = count.online;
        }
    });
}

function fetchMessages(isInitial = false) {
    do_post('chat', 'get_msgs', { since_id: isInitial ? 0 : lastMessageId }).then(msgs => {
        if (msgs && Array.isArray(msgs)) {
            if (isInitial) {
                chat_content.innerHTML = '';
            }
            
            msgs.forEach(msg => {
                const messageEl = gen_message(msg);
                if (messageEl) {
                    chat_content.appendChild(messageEl);
                    lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
                    
                    // Auto-scroll if near bottom
                    const isNearBottom = chat_content.scrollHeight - chat_content.scrollTop - chat_content.clientHeight < 100;
                    if (isNearBottom) {
                        chat_content.scrollTop = chat_content.scrollHeight;
                    }
                }
            });
        }
    });
}

function gen_message(msg) {
    if (!msg || !msg.id || !msg.nickname || !msg.message) {
        return null;
    }

    if (document.getElementById(`chat-msg-${msg.id}`)) {
        return null;
    }

    let chat_msg = document.createElement('div');
    let nick_len = msg.nickname.length;
    let max_len = 10;
    let spaces = Math.max(0, max_len - nick_len);
    let final_nick = "&nbsp;".repeat(spaces) + msg.nickname;

    chat_msg.id = `chat-msg-${msg.id}`;
    chat_msg.innerHTML = `<span class="text-warning text-end">${final_nick}:</span><span> ${msg.message}</span>`;

    return chat_msg;
}

function do_post(uri, directive, data = null) {
    let body_data = null;

    switch (directive) {
        case 'get_online':
            body_data = { action: 'get_online' };
            break;
        case 'get_msgs':
            body_data = {
                action: 'get_msgs',
                room: '!main',
                count: msg_count,
                since_id: data?.since_id || 0
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

    return fetch('chat/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(body_data)
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .catch((error) => {
        console.error('Error:', error);
        return null;
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

// Initialize the chat when the script loads
initializeChat();
setupEventListeners();