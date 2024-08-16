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

function append_to_header($data, $comment) {
    document.head.append(`<!-- ${comment} -->`);
    document.head.append(`${data}`);
}