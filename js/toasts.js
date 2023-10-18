(document).ready(function() {
    let rgx_failed_login        = /\?failed_login/;
    let rgx_successful_register = /\?register_success/;
    let rgx_start_new_register  = /\?do_register/;
    let rgx_logged_out          = /\?logged_out/;
    let rgx_dun_gooft_up        = /\?gooft/;
    let rgx_account_exists      = /\?account_exists/;
    let rgx_not_logged_in       = /\?no_login/;
    let rgx_verification_failed = /\?verification_failed/;
    let rgx_contactform_submit  = /\?contact_form_submitted=1/;
    let rgx_changepw_fail       = /game\?page=profile&action=pw_reset&result=fail/;
    
    URLSearchParams(location.search);

    if (query.match(rgx_failed_login)) {
        gen_toast('error-login-toast', 'danger', 'bi-dash-circle', 'Error', 'Invalid login credentials!');
    } else if (query.match(rgx_successful_register)) {
        gen_toast('success-register-toast', 'success', 'bi-check', 'Success', 'Account and Character successfully created, you can now log in');
    } else if (query.match(rgx_start_new_register)) {
        document.getElementById('register-tab').click();
        document.getElementById('register-email').value = params.get('email');
        gen_toast('account-not-exist', 'success', 'bi-check', 'Success', 'No account associated with this email, register?');
    } else if (query.match(rgx_logged_out)) {
        gen_toast('logged-out', 'success', 'bi-check', 'Logged Out', 'Successfully logged out!');
    } else if (query.match(rgx_dun_gooft_up)) {
        gen_toast('test-popup', 'warning', 'bi-balloon " style="font-size: 72px;"','Warning', '<marquee>Aw snap ya mighta gooft</marquee>');
    } else if (query.match(rgx_account_exists)) {
        gen_toast('account-exists', 'danger', 'bi-dash-circle', 'Account Exists', 'An account already exists with that email');
    } else if (query.match(rgx_not_logged_in)) {
        gen_toast('error-nologin-toast', 'danger', 'bi-dash-circle', 'Not Logged In', 'Please login first');
    } else if (query.match(rgx_contactform_submit)) {
        gen_toast('success-contactform-sent', 'success', 'bi-chat-heart-fill', 'Contact Form Sent', 'Thank you for contacting us, we will get back to you as soon as possible');
    } else if (query.match(rgx_changepw_fail)) {
        gen_toast('success-changepw-fail', 'danger', 'bi-key', 'Password Mis-match', 'The two passwords do not match; password unchanged');
    } else if (query.match(rgx_verification_failed)) {
        gen_toast('failed-verification', 'danger', 'bi-envelope-slash', 'Verification Failed', 'Account verification failed - check email/code combination');
    }
});