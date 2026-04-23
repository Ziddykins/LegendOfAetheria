/*
 * Handles the click event on form tabs on the login navigation nav-login.php.
 * Removes the icon 'bi-diamond-fill' and adds the icon 'bi-diamond' to all tabs
 * then adds the icon 'bi-diamond-fill' to the clicked tab.
 */
function tgl_active_signup (e) {
    document.querySelectorAll('i[class$="diamond-fill"]').forEach(function(ce) {
        ce.classList.remove('bi-diamond-fill');
        ce.classList.add('bi-diamond');
    });
    e.childNodes[1].classList.add('bi-diamond-fill');
    e.childNodes[1].classList.remove('bi-diamond');
  };

/*
 * Handles the click event on menu anchors.
 * Removes 'bg-primary' and 'text-white' classes from all menu anchors,
 * then adds them to the clicked anchor.
 */
$('a[id^="menu-anchor"]').on('click', function (e) {
    document.querySelectorAll('a[id^="menu-anchor"]').forEach(function(ce) {
        if (ce.classList.contains('bg-primary')) {
            ce.classList.remove('bg-primary');
            ce.classList.remove('text-white');
        }
    });

    e.target.classList.add('bg-primary');
    e.target.classList.add('text-white');
});

var { 
    OverlayScrollbars, 
    ScrollbarsHidingPlugin, 
    SizeObserverPlugin, 
    ClickScrollPlugin
} = OverlayScrollbarsGlobal;

const Default = {
    scrollbarTheme: "os-theme-light",
    scrollbarAutoHide: "leave",
    scrollbarClickScroll: true,
};


