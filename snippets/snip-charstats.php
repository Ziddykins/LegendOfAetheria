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
    $char_luck = $character->stats->get_luck();
    $char_chsm = $character->stats->get_chsm();
    $char_dext = $character->stats->get_dext();
    $char_sped = $character->stats->get_sped();
    $char_mdef = $character->stats->get_mdef();
    $char_crit = $character->stats->get_crit();
    $char_dodg = $character->stats->get_dodg();
    $char_blck = $character->stats->get_blck();
    $char_accu = $character->stats->get_accu();
    $char_rsst = $character->stats->get_rsst();
    $char_evsn = $character->stats->get_evsn();
    $char_rgen = $character->stats->get_rgen();
    $char_absb = $character->stats->get_absb();

    $max_hp = $character->stats->get_maxHP();
    $max_mp = $character->stats->get_maxMP();
    $max_ep = $character->stats->get_maxEP();

    $ep_icon  = 'bi-battery-full';
    $ep_color = 'success'; 
    
    $ep_percent_full = $cur_ep / $max_ep;
    $mp_percent_full = $cur_mp / $max_mp;
    $hp_percent_full = $cur_hp / $max_hp;

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

    $class   = get_class($character->stats);
    $reflect = new ReflectionClass($class);
    $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

    $stats_map = [
        'str' => [
            'name' => 'Strength',
            'icon' => 'swords',
            'value' => $char_str,
            'description' => 'Increases physical attack damage',
			'color' => 'secondary'
        ],
        'def' => [
            'name' => 'Defense',
            'icon' => 'security',
            'value' => $char_def,
            'description' => 'Reduces incoming physical damage',
			'color' => 'primary'
        ],
        'int' => [
            'name' => 'Intelligence',
            'icon' => 'neurology',
            'value' => $char_int,
            'description' => 'Increases magical attack power',
			'color' => 'light'
        ],
        'luck' => [
            'name' => 'Luck',
            'icon' => 'poker_chip',
            'value' => $char_luck,
            'description' => 'Affects item drops and random events',
			'color' => 'success'
        ],
        'char' => [
            'name' => 'Charisma',
            'icon' => 'favorite',
            'value' => $char_chsm,
            'description' => 'Influences NPC interactions and prices',
			'color' => 'danger'
        ],
        'dext' => [
            'name' => 'Dexterity',
            'icon' => 'import_contacts',
            'value' => $char_dext,
            'description' => 'Improves crafting and precision skills',
			'color' => 'info'
        ],
        'sped' => [
            'name' => 'Speed',
            'icon' => 'sprint',
            'value' => $char_sped,
            'description' => 'Determines turn order in combat',
			'color' => 'warning'
        ],
        'mdef' => [
            'name' => 'Magic Defense',
            'icon' => 'shield_moon',
            'value' => $char_mdef,
            'description' => 'Reduces incoming magical damage',
			'color' => 'primary'
        ],
        'crit' => [
            'name' => 'Critical',
            'icon' => 'brightness_alert',
            'value' => $char_crit,
            'description' => 'Chance to deal critical hit damage',
			'color' => 'danger'
        ],
        'dodg' => [
            'name' => 'Dodge',
            'icon' => 'switch_left',
            'value' => $char_dodg,
            'description' => 'Chance to evade physical attacks',
			'color' => 'warning'
        ],
        'blck' => [
            'name' => 'Block',
            'icon' => 'encrypted_minus_circle',
            'value' => $char_blck,
            'description' => 'Chance to block and reduce damage',
			'color' => 'secondary'
        ],
        'accu' => [
            'name' => 'Accuracy',
            'icon' => 'target',
            'value' => $char_accu,
            'description' => 'Increases chance to hit targets',
			'color' => 'info'
        ],
        'rsst' => [
            'name' => 'Resist',
            'icon' => 'special_character',
            'value' => $char_rsst,
            'description' => 'Resistance to status effects',
			'color' => 'black'
        ],
        'rgen' => [
            'name' => 'Regeneration',
            'icon' => 'compost',
            'value' => $char_rgen,
            'description' => 'Restores HP/MP over time',
			'color' => 'success'
        ]
    ];