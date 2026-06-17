function fadeIn(element, duration = 1000) {
    element.style.display = '';
    element.style.opacity = '0';
    let start = null;
    const step = (timestamp) => {
        if (!start) start = timestamp;
        const progress = timestamp - start;
        const opacity = Math.min(progress / duration, 1);
        element.style.opacity = opacity;
        if (progress < duration) {
            requestAnimationFrame(step);
        }
    };
    requestAnimationFrame(step);
}

function fadeOut(element, duration = 5000) {
    let start = null;
    const startOpacity = parseFloat(getComputedStyle(element).opacity) || 1;
    const step = (timestamp) => {
        if (!start) start = timestamp;
        const progress = timestamp - start;
        const opacity = Math.max(startOpacity - (progress / duration), 0);
        element.style.opacity = opacity;
        if (progress < duration) {
            requestAnimationFrame(step);
        } else {
            element.style.display = 'none';
        }
    };
    requestAnimationFrame(step);
}

if (document.getElementById("ip-lock-switch")) {
    document.getElementById("ip-lock-switch").addEventListener("click", function(e) {
    	document.getElementById("ip-lock-address").classList.toggle("invisible");
    });
}

function save_settings(which) {
    let ip_address = document.getElementById("ip-lock-address").value;
    let toggle_sw  = document.getElementById("ip-lock-switch").checked == true ? 'on' : 'off';    

    if (which == 'ip_lock') {
        fetch('/save', {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'text/plain'
                },
                method: 'POST',
                body: `save=ip_lock&ip=${ip_address}&status=${toggle_sw}`})
            .then((response) => {
                if (!response.ok) {
                    return response.text().then((data) => {
                        throw new Error(data);
                    });
                } else {
                    return response.text();
                }
            })
            .then((data) => {
                let status_msg = document.getElementById("status-msg");
                status_msg.classList.remove('text-danger');
                status_msg.classList.add('text-success');
                status_msg.innerText = data;
                fadeIn(status_msg, 1000);
                setTimeout(() => fadeOut(status_msg, 5000), 1000);
            })
            .catch((error) => {
                let status_msg = document.getElementById("status-msg");
                status_msg.classList.remove('text-success');
                status_msg.classList.add('text-danger');
                status_msg.innerText = error.message;
                fadeIn(status_msg, 1000);
                setTimeout(() => fadeOut(status_msg, 5000), 1000);
            });
        }
    }