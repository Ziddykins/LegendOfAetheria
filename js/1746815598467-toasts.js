
const ToastManager = {
    configs: { 
        failed_login:         { id: 'error-login-toast',        duration: 3000, type: 'danger',  icon: 'bi-dash-circle',      header: 'Failed Login',            message: 'Invalid login credentials!'},
        logged_out:           { id: 'logged-out',               duration: 3000, type: 'success', icon: 'bi-check',            header: 'Logged Out',              message: 'Successfully logged out!' },
        account_exists:       { id: 'account-exists',           duration: 3000, type: 'danger',  icon: 'bi-dash-circle',      header: 'Account Exists',          message: 'An account already exists with that email' },
        not_logged_in:        { id: 'error-nologin-toast',      duration: 3000, type: 'danger',  icon: 'bi-dash-circle',      header: 'Not Logged In',           message: 'Please login first' },
        contact_form_sent:    { id: 'success-contactform-sent', duration: 3000, type: 'success', icon: 'bi-chat-heart-fill',  header: 'Contact Form Sent',       message: 'Thank you for contacting us, we will get back to you as soon as possible' },
        password_not_changed: { id: 'success-changepw-fail',    duration: 3000, type: 'danger',  icon: 'bi-key',              header: 'Password Mis-has',        message: 'The two passwords do not match; password unchanged' },
        password_changed:     { id: 'password-changed',         duration: 3000, type: 'success', icon: 'bi-key',              header: 'Password Changed',        message: 'Your password has been successfully updated - Please re-login' },
        friend_req_sent:      { id: 'request-sent',             duration: 3000, type: 'success', icon: 'bi-person-plus-fill', header: 'Friend Request Sent',     message: 'Your friend request has been sent to the user' },
        friend_invalid_email: { id: 'invalid-email',            duration: 3000, type: 'danger',  icon: 'bi-slash-circle',     header: 'Invalid Email',           message: 'You have supplied an invalid email address' },
        already_verified:     { id: 'already-verified',         duration: 3000, type: 'warning', icon: 'bi-person-check',     header: 'Already Verified',        message: 'You have already verified your account!' },
        resent_verification:  { id: 'resent-verification',      duration: 3000, type: 'success', icon: 'bi-dash-circle',      header: 'Verification Resent',     message: 'Verification email has been resent!' },
        verification_success: { id: 'success-verification',     duration: 3000, type: 'success', icon: 'bi-check',            header: 'Verification Successful', message: 'Account verification successful!' },
        verification_failed:  { id: 'failed-verification',      duration: 3000, type: 'danger',  icon: 'bi-envelope-slash',   header: 'Verification Failed',     message: 'Account verification failed - check email/code combination' },
        abuse_signup:         { id: 'abuse-signup',             duration: 3000, type: 'danger',  icon: 'bi-slash-circle',     header: 'Sign-Up Abuse',           message: 'Throttled; too many account creations!' },
        iplock_failed:        { id: 'ip-locked-fail',           duration: 3000, type: 'danger',  icon: 'bi-dash-circle',      header: 'IP Locked',               message: 'IP Locked Account - Non-matching IP address' },
        friend_add_self:      { id: 'self-add',                 duration: 3000, type: 'danger',  icon: 'bi-slash-circle',     header: 'Adding Self',             message: 'Cannot add yourself to friends list!' },
        friend_invalid_email: { id: 'invalid-email',            duration: 3000, type: 'danger',  icon: 'bi-slash-circle',     header: 'Invalid Email',           message: 'Email supplied does not exist or you have been blocked' },
        friend_already_added: { id: 'already-friend',           duration: 3000, type: 'danger',  icon: 'bi-slash-circle',     header: 'Already Friended',        message: 'User already added as a friend!' },
        csrf_failed:          { id: 'csrf-failed',              duration: 3000, type: 'danger',  icon: 'bi-highlighter',      header: 'CSRF Failed',             message: 'CSRF token invalid, please refresh the page' },
        dun_gooft:            { id: 'dun-gooft',                duration: 3000, type: 'warning', icon: 'bi-balloon',          header: 'Warning',                 message: '<marquee>Aw snap ya mighta gooft</marquee>' },
        
        register_success:     { id: 'success-register-toast',   duration: 3000, type: 'success', icon: 'bi-check',            header: 'Success',                 message: 'Registration successful, please login',
            callback: () => { document.getElementById('login-tab').click(); document.getElementById('login-email').focus(); },
        },

        account_not_exist: {
            id: 'account-not-exist', duration: 3000, type: 'success', icon: 'bi-check', header: 'Success', message: 'No account associated with this email, register?',
            callback: () => { document.getElementById('register-tab').click(); document.getElementById('register-email').value = query.get('email'); }
        },

    },

    show(configKey, extraOptions = {}) {
        if (!this.configs[configKey]) {
            console.error(`Toast config '${configKey}' not found`);
            return;
        }
        const config = { ...this.configs[configKey], ...extraOptions };
        this.createToast(config);
    },

    create(options) {
        const defaults = {
            id: `toast-${Date.now()}`,
            duration: 3000,
            type: 'info',
            icon: 'bi-info-circle',
            header: 'Notice',
            message: '',
        };
        this.createToast({ ...defaults, ...options });
    },

    createToast({ id, type, icon, header, message, duration, callback }) {
        const toast_div = document.createElement('div');
        const badge_text = type[0].toUpperCase() + type.slice(1).replace('Danger', 'Error');

        toast_div.id = id;
        toast_div.classList.add('toast');
        toast_div.setAttribute('role', 'alert');
        toast_div.setAttribute('aria-live', 'assertive');
        toast_div.setAttribute('aria-atomic', 'true');

        toast_div.innerHTML = `
            <div class="toast-header">
                <span class="badge text-bg-${type} bg-gradient me-auto">
                    <i class="bi ${icon} rounded me-2"></i> ${badge_text}
                </span> ${header}
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${message}</div>
        `;

        document.getElementById('toast-container').append(toast_div);
        const toast = bootstrap.Toast.getOrCreateInstance(toast_div, {
            delay: duration
        });

        toast.show();
        if (callback) callback();
    }
};

// Event handler for URL parameters
document.addEventListener('DOMContentLoaded', () => {
    const query = new URLSearchParams(location.search);

    if (query.has('failed_login')) {
        ToastManager.show('failed_login');
    } else if (query.has('register_success')) {
        ToastManager.show('register_success');
    } else if (query.has('do_register')) {
        ToastManager.show('account_not_exist');
    } else if (query.has('logged_out')) {
        ToastManager.show('logged_out');
    } else if (query.has('gooft')) {
        ToastManager.show('dun_gooft');        
    } else if (query.has('account_exists')) {
        ToastManager.show('account_exists');
    } else if (query.has('no_login')) {
        ToastManager.show('not_logged_in');
    } else if (query.has('contact_form_submitted', '1')) {
        ToastManager.show('contact_form_sent');
    } else if (query.has('page', 'profile') && query.has('action', 'pw_reset') && query.has('result', 'fail')) {
        ToastManager.show('password_not_changed');
    } else if (query.has('action', 'pw_reset') && query.has('result', 'pass')) {
        ToastManager.show('password_changed');
    } else if (query.has('page', 'friends') && query.has('action', 'send_request')) {
        ToastManager.show('friend_req_sent');
    } else if (query.has('invalid_email')) {
        ToastManager.show('friend_invalid_email');
    } else if (query.has('already_verified')) {
        ToastManager.show('friend_already_added');        
    } else if (query.has('resent_verification')) {
        
    } else if (query.has('verification_successful')) {

    } else if (query.has('verification_failed')) {

    } else if (query.has('abuse_signup')) {

    } else if (query.has('ip_locked')) {

    } else if (query.has('page') && query.has('error')) {
        if (query.get('page') == 'friends') {
            if (query.get('error') == 'self_add') {

            } else if (query.get('error') == 'invalid_email') {

            } else if (query.get('error') == 'already_friend') {

            }
        }
    } else if (query.has('csrf-failed')) {

    }

});