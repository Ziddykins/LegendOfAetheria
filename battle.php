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
    $character->stats->set_str(1000);
    $character->stats->set_hp(10000);
    $monster = $character->get_monster();
    $ch_name = $character->get_name();
    $mn_name = $monster->get_name();

    $colors = [ 'text-danger', 'text-primary' ];

    if (check_session() === true) {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $out_msg = null;

            if ($action === 'attack') {
                if ($character->stats->get_ep() > 0) {
                    if ($character->stats->get_hp() > 0) {
                        if ($character->get_monster()) {
                            $roll = roll(1, 100);

                            if ($roll > 50) {
                                $out_msg = '<div class="small text-warning"><-}====[ <span class="text-primary">Player</span> Turn ]====={-></div><br>';
                                do_turn(Turn::PLAYER);
                                echo $out_msg;
                            } else {
                                $out_msg = '<div class="small text-warning"><-}====[ <span class="text-danger">Enemy</span> Turn ]====={-></div><br>';
                                do_turn(Turn::ENEMY);
                                echo $out_msg;
                            }
                            $character->stats->sub_ep(1);
                            
                            return true;
                        }
                    } else {
                        http_response_code(401);
                        echo "<span class=\"text-danger\">SYSTEM></span><span class=\"text-warning\">No HP Left</span><br>\r\n\r\n";
                        return false;
                    }
                } else {
                    http_response_code(401);
                    echo "<span class=\"text-danger\">SYSTEM></span><span class=\"text-warning\">No EP Left</span><br>\r\n\r\n";
                    return false;
                }
            }
        }
    }

    function do_turn(Turn $current): void {
        global $monster, $character, $log;

        [ $attacker, $attackee ] = [ $character, $monster ];
        $roll = roll(1, 100);
        [ $attack, $defense, $damage ] = [ null, null, null ];

        if ($current == Turn::ENEMY) {
            [ $attacker, $attackee ] = [ $monster, $character ];
        }

        $attack  = roll(0, $attacker->stats->get_str());
        $defense = roll(0, $attackee->stats->get_def());
        $damage  = $attack - $defense;

        $log->debug("Attack: $attack, Defense: $defense, Damage: $damage (" . $current->name . ")");

        // critr
        if ($roll === 100) {
            $damage *= intval(random_float(0.1, 1.5, 2));
            $log->debug("Critical Hit! Damage: $damage (" . $current->name . ")");
        }
            
        // miss
        if ($roll === 0) {
            $log->debug("Missed Attack! (" . $current->name . ")");
            attack_missed($attacker, $attackee, 1, $current);
        }

        // block
        if ($damage <= 0) {
            $parry = roll(1,300) >= 150 ? 1 : 0;
            attack_blocked($attacker, $attackee, $parry, $current);
        } else {
            attack_success($attacker, $attackee, $damage, $current);
        }

        if ($current == Turn::PLAYER) {
            $character->set_monster($monster);
        }
    }

    function attack_missed($attacker, $attackee, $offguard, $turn) {
        global $mn_name, $ch_name, $monster, $character, $verbs, $adverbs, $turn, $colors, $out_msg;
        $atk_verb = $verbs[array_rand($verbs)];
        $atk_adverb = $adverbs[array_rand($adverbs)];
        
        $out_msg .= "<span class=\"{$colors[0]}\">{$attacker->get_name()} $atk_adverb $atk_verb {$attackee->get_name()} but misses!</span><br>";

        if ($offguard) {
            $attack = roll(0, intval($attackee->stats->get_str() / 2));
            $attacker->stats->sub_hp($attack);
            
            if (!check_alive($attacker)) {
                $class = "bg-text-danger";
                $out_msg .= "<span class=\"fw-bold text-center bg-text-danger\">{$attacker->get_name()} has been killed!</span><br>";
                
                if ($turn == Turn::ENEMY) {
                   //award_player($attacker, $attackee);
                }

                return;
            }
            $out_msg .= "<span class=\"{$colors[$turn->value]}}\">{$attackee->get_name()} sees an opportunity to strike the {$attacker->get_name()} and with a carefully timed blow, lands a hit for $attack damage! ({$attackee->stats->get_hp()} left)</span><br>";
            $attackee->stats->sub_hp($attack);
            check_alive($attackee);
        }
    }

    function attack_success($attacker, $attackee, $damage, $turn){
        global $colors, $verbs, $out_msg;
        $attackee->stats->sub_hp($damage);
        $out_msg .= "<span class=\"{$colors[$turn->value]}}\">{$attacker->get_name()} {$verbs[array_rand($verbs)]} {$attackee->get_name()} for $damage damage! ({$attackee->stats->get_hp()} left)</span><br>";
    }

    function attack_blocked($attacker, $attackee, $parry, $turn) {
        global $colors, $verbs, $out_msg;
        $out_msg .= "<span class=\"{$colors[0]}\">{$attacker->get_name()} {$verbs[array_rand($verbs)]} {$attackee->get_name()} but {$attackee->get_name()} blocks it!</span><br>";
    }

    function check_alive(&$target): bool {
        global $out_msg;
        if ($target->stats->get_hp() > 0) {
            return true;
        }
        http_response_code(401);
        $out_msg .= '<div class="text-warning">' . $target->get_name() . ' is no longer alive"></div><br>';
        return false;
    }

    function roll($min, $max): int {
        return random_int(intval($min), intval($max));
    }