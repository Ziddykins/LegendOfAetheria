<?php
    $char_name = $character->get_name();
    $char_race = $character->get_race();
    $char_avatar = $character->get_avatar();
    $char_level = $character->get_level();
    $char_location = $character->get_location();

    $char_align = $character->get_alignment();
    $char_align = $char_align > 0 ? "+$char_align" : $char_align;

    $cur_hp = $character->stats->get_hp();
    $cur_mp = $character->stats->get_mp();
    $cur_ep = $character->stats->get_ep();

    $char_str = $character->stats->get_str();
    $char_def = $character->stats->get_def();
    $char_int = $character->stats->get_int();

    $max_hp = $character->stats->get_maxHP();
    $max_mp = $character->stats->get_maxMP();
    $max_ep = $character->stats->get_maxEP();

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