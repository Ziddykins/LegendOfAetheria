$("a[id^='menu-anchor']").on("click", 
    function (ev) {
        menu.querySelectorAll("a[id^='menu-anchor']").forEach(
            function(el) {
                el.classList.remove('bg-primary');
                el.classList.remove('text-white');
            }
        );

        ev.currentTarget.classList.add('bg-primary');
        ev.currentTarget.classList.add('text-white');
    }
);

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
                $("#status-msg").removeClass('text-danger');
                $("#status-msg").addClass('text-success');
                $("#status-msg").fadeIn(1000);
                $("#status-msg").text(data);
                $("#status-msg").fadeOut(5000);
            })
            .catch((error) => {
                $("#status-msg").removeClass('text-success');
                $("#status-msg").addClass('text-danger');
                $("#status-msg").fadeIn(1000);
                $("#status-msg").text(error);
                $("#status-msg").fadeOut(5000);
            });
        }
    }