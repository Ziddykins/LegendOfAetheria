<?php
    declare(strict_types = 1);


    use Game\System\Enums\LOAError;
    use Game\System\Enums\Weather;
    use Game\Character\Character;
    use Game\Account\Account;

    require_once "functions.php";

    
    if (!isset($argv)) {
        echo 'Access to cron.php directly is not allowed!<br>';
        exit(LOAError::CRON_HTTP_DIRECT_ACCESS->name);
    }
    
    switch ($argv[1]) {
        case 'minute':
            do_minute();
            break;
        case 'hourly':
            do_hourly();
            break;
        case 'daily':
            do_daily();
            break;
        default:
            $log->warning('No cron job specified!');
            echo 'No cron job specified!';
    }

    function do_minute() {
        regenerate();
        check_expired_sessions();
//        check_eggs();
    }
    
    function do_hourly() {
        global $log;
        cycle_weather();
    }
    
    function do_daily() {
        global $log;
        revive_all_players();
        calculate_bank_interests();

        $log->info("Daily cron tick: " . time());
    }
    
    function cycle_weather() {
        global $log;

        $cur_weather = get_globals('weather');
        $new_weather = Weather::random_enum();

        while ($new_weather === $cur_weather) {
            $new_weather = Weather::random_enum();
        }
        
        set_globals('weather', $new_weather->name);

        $log->info("Weather change: $cur_weather -> {$new_weather->name}");
        
    }
    
    function revive_all_players(): void {
        global $db, $log, $t;
        
        $sql_query  = "SELECT * FROM {$t['characters']}";
        $characters = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);

        foreach ($characters as $t_character) {
            $character = new Character($t_character['account-id'], $t_character['id']);
            $character->load();
            $max_hp = $character->stats->get_maxHP();
            if ($character->stats->get_hp() <= 0) {
                $character->stats->set_hp($max_hp);
            }
        }

        $log->info(count($characters) . " players revived during daily cronrun");
    }
    
    function regenerate() {
        global $db, $log, $t;
        $sql_query = "SELECT * FROM {$t['characters']}";
        $characters = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);
    
        foreach ($characters as $character) {
            $stats = [ "mp", "hp", "ep" ];

            $obj_char = new Character($character['account_id'], $character['id']);
            $obj_char->stats = $obj_char->stats->propRestore($character['stats']);

            foreach ($stats as $stat) {
                $set_str = "add_$stat";
                $obj_char->stats->$set_str(REGEN_PER_TICK);
            }
        }
    }

    function check_eggs() {
        global $db, $log, $t;

        $sql_query = <<<SQL
            SELECT 
                `id`,
                `character_id`,
                `hatch_time`,
                `hatched`,
                `date_acquired`
            FROM {$t['familiars']}
            WHERE `hatched` = 'False'
        SQL;

        $results = $db->query($sql_query);
        $players = $results->fetch_all();

        foreach ($players as $player) {
            $hatch_time    = $player['hatch_time'];
            $date_acquired = $player['date_acquired'];
            
            $time_remaining = sub_mysql_datetime($date_acquired, $hatch_time);

            if (!$time_remaining) {
                $sql_query = "UPDATE {$t['familiars']} SET `hatched` = ? WHERE `character_id` = ?";
                $db->execute_query($sql_query, [ 'True', $player['character_id'] ]);
            }
        }
    }

    function check_expired_sessions() {
        global $db, $log, $t;

        $sql_query = "SELECT `id` FROM {$t['accounts']} WHERE `last_action` < NOW() - INTERVAL 30 MINUTE";
        $results = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);

        foreach ($results as $account) {
            $temp_acct = new Account($account['email']);
            $temp_acct->set_sessionID(null);
            $log->info("Session Expire", ['Account' => $temp_acct->get_email(), 'Session ID' => $account['session_id']]);
        }
    }

    function calculate_bank_interests(): void {
        global $db, $log, $t;

        $sql_query = "SELECT `interest_rate`, `gold_amount`, `loan`, `dpr` FROM {$t['bank']} WHERE (interest_rate > 0 AND gold_amount > 0) OR (loan > 0 AND dpr > 0)";
        $results = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);

        foreach ($results as $result) {
            $interest_added = $result['gold_amount'] * $result['interest_rate'];
            $sql_query = "UPDATE {$t['bank']} SET gold_amount += $interest_added WHERE `character_id` = ?";
            $db->execute_query($sql_query, [ $result['character_id'] ]);
        }
    }