<?php
    declare(strict_types = 1);
    require_once "bootstrap.php";

    use Game\System\Enums\LOAError;
    use Game\System\Enums\Weather;
    session_start();

    if (!isset($argv)) {
        echo 'Access to cron.php directly is not allowed!';
        exit(LOAError::CRON_HTTP_DIRECT_ACCESS);
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
        check_eggs();
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
    
    function revive_all_players() {
        global $db, $log;
        
        $sql_query  = "SELECT * FROM {$_ENV['SQL_CHAR_TBL']} WHERE hp = 0";
        $characters = $db->execute_query($sql_query)->fetch_all();

        foreach ($characters as $character) {
            $character->stats->set_hp(
                $character->stats->get_maxHP()
            );
        }

        $log->info(count($characters) . " players revived during daily cronrun");
    }
    
    function regenerate() {
        global $db, $log;
        $sql_query = "SELECT * FROM  {$_ENV['SQL_CHAR_TBL']} WHERE `ep` <> `max_ep` OR `hp` <> `max_hp` OR `mp` <> `max_mp`";
        $results = $db->query($sql_query);
    
        while ($player = $results->fetch_assoc()) {
            $stats = [ "mp", "hp", "ep" ];

            foreach ($stats as $stat) {
                $new_stat = $player[$stat] + REGEN_PER_TICK;
                $new_stat = min($new_stat, $player["max_$stat"]);
    
                $sql_query = 'UPDATE ' . $_ENV['SQL_CHAR_TBL'] . " SET $stat = ? WHERE id = ?";
                $db->execute_query($sql_query, [ $new_stat, $player['id'] ]);

                $log->info("Updating $stat to $new_stat", ['SQL_QUERY' => $sql_query]);
                $db->query($sql_query);
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