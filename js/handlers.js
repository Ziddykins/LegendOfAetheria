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

document.getElementById("ip-lock-switch").addEventListener("click", function(e) {
	document.getElementById("ip-lock-address").classList.toggle("invisible");
});

function save_settings(which) {
    let ip_address = document.getElementById("ip-lock-address").value;
    let toggle_sw  = document.getElementById("ip-lock-switch").checked == true ? 'on' : 'off';    

    if (which == 'ip_lock') {
        $.ajax({
            type: "POST",
            url: "/save",
            data: `save=ip_lock&ip=${ip_address}&status=${toggle_sw}`,
            success: function(ret) {
                $("#status-msg").fadeIn(1000);
                $("#status-msg").text(ret);
                $("#status-msg").fadeOut(1000);
            }
        });
    }
}