function toggle_stretch() {
    document.getElementById('direct-chat-container').classList.toggle('align-self-stretch');
};

document.querySelectorAll('a[id^="dropitem-"]').forEach(function(e) {
    $(e).on("click", function(d) {
   	  d.target.parentElement.previousElementSibling.innerText = e.innerText;
    });
});

function set_theme(theme) {
    var hidden_el = document.getElementById("profile-theme");
    hidden_el.value = theme;
}
