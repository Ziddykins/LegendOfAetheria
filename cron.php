<?php
     __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    
    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'functions.php';
    
    if (!isset($argv)) {
        $log->critical('Access to cron.php directly is not allowed!', ['REQUEST' => print_r($_REQUEST, 1)]);
        echo 'Access to cron.php directly is not allowed!';
        exit(1);
    }
    
    $log->info('cronjob running', ['argv[1]' => $argv[1]]);
    
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
        case 'default':
            $log->warning('No cron job specified!');
            die('No cron job specified!');
    }

    function do_minute() {
        add_energy();
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
?>