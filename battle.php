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


    /*if (check_session() === true) {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $out_msg = null;

            if ($action === 'attack') {
                do_turn(Turn::PLAYER);
                $character->sub_ep(1);
            }
        }
    }


    function do_turn(Turn $target=null) {
        global $monster, $character;
        $flip = random_int(1, 100);

        if ($target !== null) {
            $next_up = null;

            if ($flip == 100) {
                $next_up = Turn::ENEMY;                
            } elseif ($flip == 0) {
                $next_up = Turn::PLAYER;
            }
            
            if ($next_up) {
                attack_misses($target);
                do_turn($next_up);
                return;
            }

            if ($target == Turn::PLAYER) {
                $attack  = random_int(0, $character->get_str());
                $defense = random_int(0, $monster->get_def());
                $damage  = $defense - $attack;
            } else {
                $attack  = random_int(0, $monster->get_str());
                $defense = random_int(0, $character->get_def());
                $damage  = $defense - $attack;
            }

            if ($attack > $defense) {
                attack_success($target, $damage);
            } else {
                $parry = random_int(1,300) >= 150 ? 1 : 0;
                attack_blocked($target, $parry);
            }
        }
    }

    function attack_misses(&$target, $offguard) {
//
    }

    function attack_success(&$attacker, &$attackee){
        $out_msg = '';

    }

    function attack_blocked(&$target, $parry) {

    }

    function check_alive(&$target) {


    }

    // $out_msg .= player_msg("The $mn_name takes a swing at $ch_name and lands a hit, dealing $damage damage!");
    // $out_msg .= player_msg("$ch_name {$action}s the $mn_name for $damage damage!");
    */