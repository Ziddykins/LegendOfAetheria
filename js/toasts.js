$(document).ready(function(){ 
    if (location.search.match(/\?failed_login/)) {
        gen_toast('error-login-toast', 'danger', 'bi-dash-circle', 'Error', 'Invalid login credentials!');
    } else if (location.search.match(/\?register_success/)) {
        gen_toast('success-register-toast', 'success', 'bi-check', 'Success', 'Account and Character successfully created, you can now log in');
    } else if (location.search.match(/\?do_register/)) {
        document.getElementById('register-tab').click();
        document.getElementById('register-email').value = location.search.split('&')[1].replace('email=', '');
        gen_toast('account-not-exist', 'success', 'bi-check', 'Success', 'No account associated with this email, register?');
    } else if (location.search.match(/\?logged_out/)) {
        gen_toast('logged-out', 'success', 'bi-check', 'Logged Out', 'Successfully logged out!');
    } else if (location.search.match(/\?gooft/)) {
        gen_toast('test-popup', 'warning', 'bi-balloon " style="font-size: 72px;"','Warning', '<marquee>Aw snap ya mighta gooft</marquee>');
    } else if (location.search.match(/\?account_exists/)) {
        gen_toast('account-exists', 'danger', 'bi-dash-circle', 'Account Exists', 'An account already exists with that email');
    } else if (location.search.match(/\?no_login/)) {
        gen_toast('error-nologin-toast', 'danger', 'bi-dash-circle', 'Not Logged In', 'Please login first');
    } else if (location.search.match(/\?contact_form_submitted=1/)) {
        gen_toast('success-contactform-sent', 'success', 'bi-chat-heart-fill', 'Contact Form Sent', 'Thank you for contacting us, we will get back to you as soon as possible');
    }
});