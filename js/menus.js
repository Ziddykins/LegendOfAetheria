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

document.addEventListener("DOMContentLoaded", function() {
    const sidebarWrapper = document.querySelector("#sidebar");
  //  var elements = document.querySelectorAll('#sidebar,#main-section,#inbox-list');

    elements.forEach((element) => {
        const style = window.getComputedStyle(element);
        console.log(element);
        const isScrollable = style.overflow === 'auto' || style.overflow === 'scroll' || style.overflowY === 'auto' || style.overflowY === 'scroll' || style.overflowX === 'auto' || style.overflowX === 'scroll';

        if (isScrollable || element === document.body) {
            OverlayScrollbarsGlobal.OverlayScrollbars(element, {
                scrollbars: {
                    theme: Default.scrollbarTheme,
                    autoHide: Default.scrollbarAutoHide,
                    clickScroll: Default.scrollbarClickScroll,
                },
            });
        }
    });


    /*if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined") {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
                theme: Default.scrollbarTheme,
                autoHide: Default.scrollbarAutoHide,
                clickScroll: Default.scrollbarClickScroll,
            },
        });
    }*/
    /*if (contentWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined") {
        OverlayScrollbarsGlobal.OverlayScrollbars(contentWrapper, {
            scrollbars: {
                theme: Default.scrollbarTheme,
                autoHide: Default.scrollbarAutoHide,
                clickScroll: Default.scrollbarClickScroll,
            },
        });
    }*/


    let query =  new URLSearchParams(location.search);
    if (document.body.contains(document.getElementById('login-email'))) {
        document.getElementById('login-email').focus();
    };

    if (query.has('do_register') && query.has('email')) {
        document.getElementById("register-password").focus();
    }
});

