function stat_adjust(which, slider) {
    let [stat, direction] = which.split('-');
    let selector          = "#stats-" + stat + "-cur";
    let stat_cur_ap       = document.querySelector(selector);
    let obj_ap            = document.querySelector('#stats-remaining-ap');

    if (direction == 'plus') {
        if (parseInt(obj_ap.innerHTML) == 0) {
            obj_ap.classList.add('text-danger');
        } else {
            obj_ap.classList.remove('text-danger');
            stat_cur_ap.innerHTML = parseInt(stat_cur_ap.innerHTML) + 1;
            obj_ap.innerHTML = parseInt(obj_ap.innerHTML) - 1;
        }
    } else {
        if (parseInt(obj_ap.innerHTML) == 10) {
            obj_ap.classList.add('text-danger');
        } else {
            if (parseInt(stat_cur_ap.innerHTML) <= 10) {
                stat_cur_ap.classList.add('text-danger');
                return;
            } else {
                stat_cur_ap.classList.remove('text-danger');
                obj_ap.classList.remove('text-danger');
                stat_cur_ap.innerHTML = parseInt(stat_cur_ap.innerHTML) - 1;
                obj_ap.innerHTML = parseInt(obj_ap.innerHTML) + 1;
            }
        }
    }
};

/* Deprecated - Replaced with toast wrapper - mm toast
function gen_toast(toast_id) {
    let toast = document.getElementById(toast_id);
    let toast_bubble = bootstrap.Toast.getOrCreateInstance(toast);
    toast_bubble.show();
};
*/

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

function tgl_active (e) {
    document.querySelectorAll('i[class$="diamond-fill"]').forEach(function(e) {
        e.classList.remove('bi-diamond-fill');
        e.classList.add('bi-diamond');
    });
    e.childNodes[1].classList.add('bi-diamond-fill');
    e.childNodes[1].classList.remove('bi-diamond');
};

function gen_envelope(subject, sender, message_fragment, date) {
    let envelope_html = '<div class="list-group">';
    envelope_html    += '    <a href="#" class="list-group-item list-group-item-action active" aria-current="true">';
    envelope_html    += '        <div class="d-flex w-100 justify-content-between">';
    envelope_html    += '            <h5 class="mb-1">' + sender + ' - ' + subject + '</h5>';
    envelope_html    += '            <small>' + date + '</small>';
    envelope_html    += '        </div>';
    envelope_html    += '        <small>' + message_fragment + '</small>';
    envelope_html    += '   </a>';
    envelope_html    += '</div>';
    return envelope_html;
}

$(document).ready(function(){ 
    if (document.body.contains(document.getElementById('login-email'))) {
        document.getElementById('login-email').focus();
    };
});
