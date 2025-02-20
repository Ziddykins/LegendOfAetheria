<?php
    declare(strict_types = 1);
    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
    use Game\Character\Character;
    use Game\Monster\Pool;
    use Game\Battle\Enums\Turn;

    $verbs = ["attacks", "pummels", "strikes", "assaults", "blugeons", "ambushes", "beats", "besieges", "blasts", "bombards", "charges", "harms", "hits", "hurts", "infiltrates", "invades", "raids", "stabs", "stormss", "strikes"];
    $adverbs = [ "clumsily", "lazily", "spastically", "carefully", "precisely" ];

    session_start();

    require_once "bootstrap.php";

    $account = new Account($_SESSION['email']);
    $account->load();

    $character = new Character($account->get_id(), $_SESSION['character-id']);
    $character->load();

    $monster = $character->get_monster();
    $ch_name = $character->get_name();
    $mn_name = $monster->get_name();

    $colors = [ 'text-danger', 'text-primary' ];

    if (check_session() === true) {
        if (isset($_POST['action'])) {
            if (isset($_POST['csrf-token']) && $_POST['csrf-token'] !== $_SESSION['csrf-token']) {
                http_response_code(400);
                echo "CSRF Token Mismatch";
                exit();
            }
            
            $action = $_POST['action'];
            $out_msg = null;

            if ($action === 'attack') {
                if ($character->stats->get_ep() > 0) {
                    do_turn(Turn::PLAYER);
                    $character->stats->sub_ep(1);
                    do_turn(Turn::ENEMY);
                } else {
                    http_response_code(400);
                    echo "<span class=\"text-danger\">SYSTEM></div><div class=\"text-warning\">No EP Left</div>\r\n\r\n";
                    return false;
                }
            }
        }
    }

    function do_turn(Turn $current) {
        global $monster, $character;
        [$attacker, $attackee] = [ $character, $monster ];
        $roll = roll(1, 100);
        [ $attack, $defense, $damage ] = [ null, null, null ];

        if ($current == Turn::ENEMY) {
            $attacker = $monster;
            $attackee = $character;
        }

        $attack  = roll(0, $attacker->stats->get_str());
        $defense = roll(0, $attackee->stats->get_def());
        $damage  = $defense - $attack;

        // crit
        if ($roll === 100) {
            $damage *= intval(random_float(0.1, 1.5));
        }
            
        // miss
        if ($roll === 0) {
            attack_missed($attacker, $attackee, 1, $current);
        }

        // block
        if ($damage <= 0) {
            $parry = roll(1,300) >= 150 ? 1 : 0;
            attack_blocked($attacker, $attackee, $parry, $current);
        } else {
            attack_success($attacker, $attackee, $damage, $current);
        }
    }

    function attack_missed($attacker, $attackee, $offguard, $turn) {
        global $mn_name, $ch_name, $monster, $character, $verbs, $adverbs, $turn, $colors;
        $atk_verb = $verbs[array_rand($verbs)];
        $atk_adverb = $adverbs[array_rand($adverbs)];
        

        $out_msg = "<span class=\"{$colors[0]}\">{$attacker->get_name()} $atk_adverb $atk_verb {$attackee->get_name()} but misses!</span><br>";

        if ($offguard) {
            $attack = roll(0, intval($attackee->stats->get_str() / 2));
            $attacker->stats->sub_hp($attack);
            
            if (!check_alive($attacker)) {
                $class = "bg-text-danger";
                $out_msg .= "<span class=\"fw-bold text-center bg-text-danger\">{$attacker->get_name()} has been killed!</span><br>";
                
                if ($turn == Turn::ENEMY) {
                    award_player($attacker, $attackee);
                }

                echo $out_msg;
                return;
            }
            $out_msg .= "<span class=\"{$colors[$turn->value]}}\">{$attackee->get_name()} sees an opportunity to strike the {$attacker->get_name()} and with a carefully timed blow, lands a hit for $attack damage! ({$attackee->stats->get_hp()} left)</span><br>";
            $attackee->stats->sub_hp($attack);
            check_alive($attackee);
        }
        echo $out_msg;
        
    }

    function attack_success($attacker, $attackee, $damage, $turn){
        global $colors, $verbs;
        $out_msg = "<span class=\"{$colors[$turn->value]}}\">{$attackee->get_name()} {$verbs[array_rand($verbs)]} {$attacker->get_name()} for $damage damage! ({$attackee->stats->get_hp()} left)</span><br>";
        $attackee->stats->sub_hp($damage);
        echo $out_msg;
    }

    function attack_blocked($attacker, $attackee, $parry, $turn) {
        global $colors, $verbs;

        $out_msg = "<span class=\"{$colors[0]}\">{$attacker->get_name()} {$verbs[array_rand($verbs)]} {$attackee->get_name()} but {$attackee->get_name()} blocks it!</span><br>";
        echo $out_msg;
    }

    function check_alive(&$target): bool {
        if ($target->stats->get_hp() > 0) {
            return true;
        }
        http_response_code(401);
        echo "{$target->get_name()} is no longer alive";
        return false;
    }

    function roll($min, $max): int {
        return random_int($min, $max);
    }