<?php
    $cur_hp = $character->stats->get_hp();
    $cur_mp = $character->stats->get_mp();
    $cur_ep = $character->stats->get_ep();

    $max_hp = $character->stats->get_maxHp();
    $max_mp = $character->stats->get_maxMp();
    $max_ep = $character->stats->get_maxEp();

    $ep_icon  = 'bi-battery-full';
    $ep_color = 'success';
    $ep_percent_full = ($cur_ep / $max_ep) * 100;
    
    if ($ep_percent_full > 25 && $ep_percent_full < 75) {
        $ep_icon  = 'bi-battery-half';
        $ep_color = 'warning';
    } elseif ($ep_percent_full >= 0 && $ep_percent_full <= 25) {
        $ep_icon  = 'bi-battery';
        $ep_color = 'danger';
    }

    $ap = $character->stats->get_ap();

    $cur_xp   = $character->stats->get_exp();
    $next_lvl = $character->stats->get_maxExp();

    $location = $character->get_location();
    $cur_x    = $character->get_x();
    $cur_y    = $character->get_y();

    $align = $character->get_alignment();

    $race = $character->get_race();