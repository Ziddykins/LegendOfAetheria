function gen_toast(id, type, icon, header_txt, message) {
    let toast_div = document.createElement('div');
    let toast_badge_text = type[0].toUpperCase() + type.slice(1);
    toast_badge_text = toast_badge_text.replace('Danger', 'Error');
    
    toast_div.id = id;
    toast_div.classList.add('toast');
    toast_div.ariaLive = 'assertive';
    toast_div.ariaAtomic = 'true';
    toast_div.role = 'alert';

    toast_div.innerHTML = `<div class="toast-header">
                                <span class="badge text-bg-${type} me-auto">
                                    <i class="bi ${icon} rounded me-2"></i> ${toast_badge_text}
                                </span> ${header_txt}
                                
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                ${message}
                            </div>
                        </div>`;

    document.getElementById('toast-container').append(toast_div);

    let toast = document.querySelector(`#toast-container #${id}`);
    let toast_bubble = bootstrap.Toast.getOrCreateInstance(toast);
    toast_bubble.show();
};

$(document).ready(function() {
    let query =  new URLSearchParams(location.search);

    if (query.has('failed_login')) {
        gen_toast('error-login-toast', 'danger', 'bi-dash-circle', 'Error', 'Invalid login credentials!');
    } else if (query.has('register_success')) {
        gen_toast('success-register-toast', 'success', 'bi-check', 'Success', 'Account and Character successfully created, you can now log in');
        document.getElementById('login-tab').click();
        document.getElementById('login-tab').focus();        
    } else if (query.has('do_register')) {
        document.getElementById('register-tab').click();
        document.getElementById('register-email').value = query.get('email');
        gen_toast('account-not-exist', 'success', 'bi-check', 'Success', 'No account associated with this email, register?');
    } else if (query.has('logged_out')) {
        gen_toast('logged-out', 'success', 'bi-check', 'Logged Out', 'Successfully logged out!');
    } else if (query.has('gooft')) {
        gen_toast('test-popup', 'warning', 'bi-balloon " style="font-size: 72px;"','Warning', '<marquee>Aw snap ya mighta gooft</marquee>');
    } else if (query.has('account_exists')) {
        gen_toast('account-exists', 'danger', 'bi-dash-circle', 'Account Exists', 'An account already exists with that email');
    } else if (query.has('no_login')) {
        gen_toast('error-nologin-toast', 'danger', 'bi-dash-circle', 'Not Logged In', 'Please login first');
    } else if (query.has('contact_form_submitted', '1')) {
        gen_toast('success-contactform-sent', 'success', 'bi-chat-heart-fill', 'Contact Form Sent', 'Thank you for contacting us, we will get back to you as soon as possible');
    } else if (query.has('page', 'profile') && query.has('action', 'pw_reset') && query.has('result', 'fail')) {
        gen_toast('success-changepw-fail', 'danger', 'bi-key', 'Password Mis-has', 'The two passwords do not match; password unchanged');
    } else if (query.has('action', 'pw_reset') && query.has('result', 'pass')) {
        gen_toast('password-changed', 'success', 'bi-key', 'Password Changed', 'Your password has been successfully updated - Please re-login');
    } else if (query.has('verification_failed')) {
        gen_toast('failed-verification', 'danger', 'bi-envelope-slash', 'Verification Failed', 'Account verification failed - check email/code combination');
    } else if (query.has('page', 'friends') && query.has('action', 'send_request')) {
        gen_toast('request-sent', 'success', 'bi-person-plus-fill', 'Friend Request Sent', 'Your friend request has been sent to the user');
    } else if (query.has('invalid_email')) {
        gen_toast('invalid-email', 'danger', 'bi-envelope-slash-fill', 'Invalid Email', 'You have supplied an invalid email address');
    } else if (query.has('already_verified')) {
        gen_toast('already-verified', 'warning', 'bi-person-check', 'Already Verified', 'You have already verified your account!');
        document.getElementById('login-email').value = query.get('email');
        document.getElementById('login-password').focus();
    }
});
