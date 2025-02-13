<?php
    declare(strict_types = 1);
    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
    use Game\Character\Character;
    use Game\Monster\Pool;
    use Game\Battle\Enums\Turn;
    
    session_start();

    require_once "bootstrap.php";


    $account = new Account($_SESSION['email']);
    $account->load();

    $character = new Character($account->get_id(), $_SESSION['character-id']);
    $character->load();

    $monster = $character->get_monster();
    $ch_name = $character->get_name();
    $mn_name = $monster->get_name();

   
    if (check_session() === true) {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $out_msg = null;

            if ($action === 'attack') {
                do_turn(Turn::PLAYER);
                $character->stats->sub_ep(1);
            }
        }
    }

 
    function do_turn(Turn $target=null) {
        global $monster, $character;
        $roll = roll(1, 100);

        if ($target !== null) {
            $next_up = null;

            if ($roll == 100) {
                $next_up = Turn::ENEMY;                
            } elseif ($roll == 0) {
                $next_up = Turn::PLAYER;
            }
            
            $offguard = 0;
            if (roll(0,250) >= 125) {
                $offguard = 1;
            }
            attack_misses($monster, $character,  $offguard);
            do_turn($next_up);
            return;
        }

        if ($target == Turn::PLAYER) {
            $attack  = roll(0, $character->stats->get_str());
            $defense = roll(0, $monster->stats->get_def());
            $damage  = $defense - $attack;
        } else {
            $attack  = roll(0, $monster->stats->get_str());
            $defense = roll(0, $character->stats->get_def());
            $damage  = $defense - $attack;
        }

        if ($attack > $defense) {
            attack_success($monster, $damage);
        } else {
            $parry = roll(1,300) >= 150 ? 1 : 0;
            attack_blocked($character, $monster, $parry);
        }
    }
    

    function attack_misses($attacker, $attackee, $offguard) {
        global $mn_name, $ch_name, $monster, $character;
        $verbs = ["attacks", "pummels", "strikes", "assaults", "blugeons", "ambushes", "beats", "besieges", "blasts", "bombards", "charges", "harms", "hits", "hurts", "infiltrates", "invades", "raids", "stabs", "stormss", "strikes"];
        $atk_verb = $verbs[array_rand($verbs)];

        $out_msg = "<span class=\"text-danger\">{$attacker->get_name()} $atk_verb {$attackee->get_name()} but misses horribly</span><br>";

        if ($offguard) {
            $attack = roll(0, intval($attackee->stats->get_str() / 2));
            $attacker->stats->sub_hp($attack);
            
            if (!check_alive($attacker)) {
                $class = "bg-text-danger";
                $out_msg .= "<span class=\"bg-text-danger fw-bold\">{$attacker->get_name()} has been killed!</span><br>";
                echo $out_msg;
                return;
            }
            $out_msg .= "<span class=\"text-danger\">{$attackee->get_name()} sees an opportunity to strike the {$attacker->get_name()} and with a carefully timed blow, lands a hit for $attack damage!</span><br>";
        }
        echo $out_msg;
        
    }

    function attack_success($attacker, $attackee){
        $out_msg = '';
    }

    function attack_blocked($attacker, $attackee, $parry) {

    }

    function check_alive(&$target): bool {
        if ($target->stats->get_hp()) {
            return true;
        }
        return false;
    }

    function roll($min, $max): int {
        return random_int($min, $max);
    }