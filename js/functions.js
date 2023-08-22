function stat_adjust(which, slider) {
    let [stat, direction] = which.split('-');
    let selector    = "#stats-" + stat + "-cur";
    let stat_cur_ap = document.querySelector(selector);
    let obj_ap      = document.querySelector('#stats-remaining-ap');

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