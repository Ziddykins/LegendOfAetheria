<?php
    declare(strict_types = 1);
    require_once "bootstrap.php";

    use Game\System\Enums\LOAError;
    use Game\System\Enums\Weather;
    use Game\Character\Character;

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
//        check_eggs();
    }
    
    function do_hourly() {
        global $log;
        cycle_weather();
        $log->info("Hourly cron tick: " . time());
    }
    
    function do_daily() {
        global $log;
        revive_all_players();
        $log->info("Daily cron tick: " . time());
    }
    
    function cycle_weather() {
        global $log;

        $cur_weather = get_globals('weather');
        $new_weather = Weather::random();

        while ($new_weather === $cur_weather) {
            $new_weather = Weather::random();
        }
        
        set_globals('weather', $new_weather->name);

        $log->info("Weather change: $cur_weather -> {$new_weather->name}");
        
    }
    
    function revive_all_players(): void {
        global $db, $log;
        
        $sql_query  = "SELECT * FROM {$_ENV['SQL_CHAR_TBL']} WHERE hp = 0";
        $characters = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);

        foreach ($characters as $t_character) {
            $character = new Character($t_character['account-id'], $t_character['id']);
            $character->load();

            $character->stats->set_hp(
                $character->stats->get_maxHP()
            );
        }

        $log->info(count($characters) . " players revived during daily cronrun");
    }
    
    function regenerate() {
        global $db, $log;
        $sql_query = "SELECT * FROM {$_ENV['SQL_CHAR_TBL']}";
        $characters = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);
    
        foreach ($characters as $character) {
            $obj_char = new Character($character['account_id'], $character['id']);
            $obj_char->stats = safe_serialize($character['stats'], true);
            
            $stats = [ "mp", "hp", "ep" ];
            foreach ($stats as $stat) {
                $set_str = "add_$stat";
                $obj_char->stats->$set_str(REGEN_PER_TICK);
            }
        }
    }

    function check_eggs() {
        global $db, $log;

        $sql_query = <<<SQL
            SELECT 
                `id`,
                `character_id`,
                `hatch_time`,
                `hatched`,
                `date_acquired`
            FROM {$_ENV['SQL_FMLR_TBL']}
            WHERE `hatched` = 'False'
        SQL;

        $results = $db->query($sql_query);
        $players = $results->fetch_all();

        foreach ($players as $player) {
            $hatch_time    = $player['hatch_time'];
            $date_acquired = $player['date_acquired'];
            
            $time_remaining = sub_mysql_datetime($date_acquired, $hatch_time);

            if (!$time_remaining) {
                $sql_query = "UPDATE {$_ENV['SQL_FMLR_TBL']} SET `hatched` = ? WHERE `character_id` = ?";
                $db->execute_query($sql_query, [ 'True', $player['character_id'] ]);
            }
        }
    }