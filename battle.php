<?php
    declare(strict_types = 1);
    require_once "bootstrap.php";
    
    use Game\Account\Account;
    use Game\Character\Character;
    use Game\Battle\Enums\Turn;

    $verbs = ["attacks", "pummels", "strikes", "assaults", "blugeons", "ambushes", "beats", "besieges", "blasts", "bombards", "charges", "harms", "hits", "hurts", "infiltrates", "invades", "raids", "stabs", "storms", "strikes"];
    $adverbs = [ "clumsily", "lazily", "spastically", "carefully", "precisely" ];

    $account = new Account($_SESSION['email']);
    $character = new Character($account->get_id(), $_SESSION['character-id']);
    $monster = $character->get_monster();
    $ch_name = $character->get_name();
    $mn_name = $monster ? $monster->get_name() : '';

    $colors = [ 'text-danger', 'text-primary' ];
    
    if (!check_session()) {
        http_response_code(401);
        exit("Not logged in");
    }

    if (!isset($_POST['action'])) {
        http_response_code(400);
        exit("No action specified");
    }

    $action = $_POST['action'];
    $out_msg = '';

    if ($action === 'attack') {
        if (!validate_battle_state()) {
            exit();
        }

        $turn = determine_turn();
        $out_msg = format_turn_header($turn);
        do_turn($turn);
        echo $out_msg;
    }

    function validate_battle_state() {
        global $character, $out_msg;

        if ($character->stats->get_ep() <= 0) {
            http_response_code(401);
            $out_msg = "<span class=\"text-danger\">SYSTEM></span><span class=\"text-warning\">No EP Left</span><br>\r\n\r\n";
            echo $out_msg;
            return false;
        }

        if ($character->stats->get_hp() <= 0) {
            http_response_code(401);
            $out_msg = "<span class=\"text-danger\">SYSTEM></span><span class=\"text-warning\">No HP Left</span><br>\r\n\r\n";
            echo $out_msg;
            return false;
        }

        if (!$character->get_monster()) {
            http_response_code(401);
            $out_msg = "<span class=\"text-danger\">SYSTEM></span><span class=\"text-warning\">No Monster</span><br>\r\n\r\n";
            echo $out_msg;
            return false;
        }

        $character->stats->sub_ep(1);
        return true;
    }

    function determine_turn() {
        return roll(1, 100) > 50 ? Turn::PLAYER : Turn::ENEMY;
    }

    function format_turn_header(Turn $turn) {
        $turn_name = $turn == Turn::PLAYER ? 'Player' : 'Enemy';
        $turn_color = $turn == Turn::PLAYER ? 'text-primary' : 'text-danger';
        return "<div class=\"small text-warning\"><-}====[ <span class=\"$turn_color\">$turn_name</span> Turn ]====={-></div><br>";
    }

    function do_turn(Turn $current): void {
        global $monster, $character, $log;

        [$attacker, $attackee] = $current == Turn::ENEMY ? 
            [$monster, $character] : 
            [$character, $monster];

        $roll = roll(1, 100);
        process_combat($attacker, $attackee, $roll, $current);

        // Always persist state after combat
        if ($current == Turn::PLAYER) {
            $character->set_monster($monster);
        }
    }

    function process_combat($attacker, $attackee, $roll, Turn $current) {
        global $log;

        $attack = calculate_attack($attacker, $roll);
        $defense = calculate_defense($attackee);
        $damage = max(0, $attack - $defense);

        $log->debug("Combat calcs", [
            'attack' => $attack,
            'defense' => $defense,
            'damage' => $damage,
            'turn' => $current->name
        ]);

        if ($roll === 100) {
            handle_critical_hit($damage);
        } else if ($roll === 0) {
            handle_miss($attacker, $attackee, $current);
        } else if ($damage <= 0) {
            handle_block($attacker, $attackee, $current);
        } else {
            handle_hit($attacker, $attackee, $damage, $current);
        }
    }

    function calculate_attack($attacker, $roll) {
        $base_attack = roll(1, intval($attacker->stats->get_str()));
        return $roll === 100 ? $base_attack * 2 : $base_attack;
    }

    function calculate_defense($defender) {
        return roll(0, intval($defender->stats->get_def() * 0.8));
    }

    function handle_critical_hit(&$damage) {
        $damage *= intval(random_float(1.5, 2.0, 2));
    }

    function handle_miss($attacker, $attackee, Turn $turn) {
        global $verbs, $adverbs, $colors, $out_msg;
        
        $atk_verb = $verbs[array_rand($verbs)];
        $atk_adverb = $adverbs[array_rand($adverbs)];
        $out_msg .= "<span class=\"{$colors[$turn->value]}\">{$attacker->get_name()} $atk_adverb $atk_verb {$attackee->get_name()} but misses!</span><br>";

        if (roll(1, 100) > 70) { // 30% counter chance
            $counter_damage = roll(1, intval($attackee->stats->get_str() * 0.5));
            apply_damage($attacker, $counter_damage);
            $out_msg .= "<span class=\"{$colors[!$turn->value]}\">{$attackee->get_name()} sees an opening and counters for $counter_damage damage!</span><br>";
            check_alive($attacker, Turn::value_to_enum(!$turn->value));
        }
    }

    function handle_block($attacker, $attackee, Turn $turn) {
        global $colors, $verbs, $out_msg;

        $parry_chance = roll(1, 100);
        if ($parry_chance > 70) { // 30% parry chance
            $turn = Turn::value_to_enum(!$turn->value);
            $parry_dmg = roll(1, intval($attackee->stats->get_str() * 0.25));
            apply_damage($attacker, $parry_dmg);
            $out_msg .= "<span class=\"{$colors[$turn->value]}\">{$attackee->get_name()} parries {$attacker->get_name()}'s attack and deals $parry_dmg damage!</span><br>";
            check_alive($attacker, $turn);
        } else {
            apply_damage($attackee, 1);
            $out_msg .= "<span class=\"{$colors[$turn->value]}\">{$attacker->get_name()} {$verbs[array_rand($verbs)]} {$attackee->get_name()} but {$attackee->get_name()} blocks most of it!</span><br>";
            check_alive($attackee, $turn);
        }
    }

    function handle_hit($attacker, $attackee, $damage, Turn $turn) {
        global $colors, $verbs, $out_msg;
        
        apply_damage($attackee, $damage);
        $out_msg .= "<span class=\"{$colors[$turn->value]}\">{$attacker->get_name()} {$verbs[array_rand($verbs)]} {$attackee->get_name()} for $damage damage! ({$attackee->stats->get_hp()} HP left)</span><br>";
        check_alive($attackee, $turn);
    }

    function apply_damage($target, $damage) {
        $target->stats->sub_hp($damage);
    }

    function check_alive($target, Turn $turn): void {
        global $out_msg, $colors, $character;

        if ($target->stats->get_hp() <= 0) {
            $out_msg .= "<span class=\"{$colors[$turn->value]}\">{$target->get_name()} has been defeated!</span><br>";
        
            if ($turn == Turn::PLAYER) {    
                reward_player();
            }

            $character->set_monster(null);
        }
    }

    function reward_player() {
        global $character, $monster, $out_msg;

        $exp_gained = $monster->get_expAwarded();
        $gold_gained = $monster->get_goldAwarded();

        $character->add_experience($exp_gained);
        $character->add_gold($gold_gained);
        $character->set_monster(null);

        $out_msg .= "<span class=\"text-success\">Victory! You gained $exp_gained experience and $gold_gained gold!</span><br>";
    }

    function roll($min, $max): int {
        return random_int(intval($min), intval($max));
    }