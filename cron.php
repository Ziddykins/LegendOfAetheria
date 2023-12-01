<?php
    declare(strict_types = 1);
    session_start();
    require '../../../vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();  

    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'functions.php';
    
    if (!isset($argv)) {
        $log->warning('Access to cron.php directly is not allowed!', ['REQUEST' => print_r($_REQUEST, true)]);
        echo 'Access to cron.php directly is not allowed!';
        exit(CRON_HTTP_DIRECT_ACCESS);
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
        add_energy();
        check_eggs();
    }
    
    function do_hourly() {
        cycle_weather();
    }
    
    function do_daily() {
        revive_all_players();
    }
    
    function cycle_weather() {
        $cur_weather = get_globals('weather');
        
        while ($new_weather === $cur_weather) {
            $new_weather = Weather::random();
        }
        
        set_globals('weather', $weather);
    }
    
    function revive_all_players() {
        global $db, $log;
        $count = 0;
        
        $sql_query = "SELECT * FROM `" . $ENV['SQL_CHAR_TBL'] . "` WHERE hp = 0;";
        $results   = $db->query($sql_query);
        
        while ($player = $results->fetch_assoc()) {
            $player['hp'] = $player['max_hp'];
            $count++;
        }
        
        $log->info("$count players revived during daily cronrun");
    }
    
    function add_energy() {
        global $db, $log;
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_CHAR_TBL'] . ' WHERE ep <> max_ep';
        $results = $db->query($sql_query);
    
        while ($player = $results->fetch_assoc()) {
            $new_ep = $player['ep'] + 3;
            $new_ep = min($new_ep, $player['max_ep']);
    
            $sql_query = 'UPDATE ' . $_ENV['SQL_CHAR_TBL'] . " SET ep = $new_ep WHERE id = " . $player['id'];
          
            $log->info("Updating ep to $new_ep", ['SQL_QUERY' => $sql_query]);
            $db->query($sql_query);
        }
    }

    function check_eggs() {
        global $db, $log;

        $sql_query = 'SELECT ' .
            '`id`, `character_id`, `hatch_time`, `hatched`, `date_acquired` ' .
            'FROM ' . $_ENV['SQL_FMLR_TBL'] . ' WHERE `hatched` = "False"';

        $results = $db->query($sql_query);        
        while ($player = $results->fetch_assoc()) {
            $hatch_time    = $player['hatch_time'];
            $hatched       = $player['hatched'];
            $date_acquired = $player['date_acquired'];
            
            $time_remaining = sub_mysql_datetime($date_acquired, $hatch_time);

            if (!$time_remaining) {
                $sql_query = 'UPDATE ' . $_ENV['SQL_FMLR_TBL'] . ' ' .
                             'SET `hatched` = ?' .
                             'WHERE `character_id` = ?';
                $prepped = $db->prepare($sql_query);
                $prepped->bind_param('si', 'True', $player['character_id']);
                $prepped->execute();
            }
        }
    }

?>
