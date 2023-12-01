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

$(document).ready(function(){ 
    if (document.body.contains(document.getElementById('login-email'))) {
        document.getElementById('login-email').focus();
    };
});