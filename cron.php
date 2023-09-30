<?php
    
     __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    
    include 'logger.php';
    include 'db.php';
    include 'constants.php';
    include 'functions.php';
    
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
        global $db, $log;
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_CHAR_TBL'] . ' WHERE ep <> max_ep';
        $results = $db->query($sql_query);
    
        while ($row = $results->fetch_assoc()) {
            $new_ep = $row['ep'] + 3;
    
            $new_ep = min($new_ep, $row['max_ep']);
    
            $sql_query = 'UPDATE ' . $_ENV['SQL_CHAR_TBL'] . " SET ep = $new_ep WHERE id = " . $row['id'];
            $log->info("Updating ep to $new_ep", ['SQL_QUERY' => $sql_query]);
            $db->query($sql_query);
        }
    }
    
    function do_hourly() {
        cycle_weather();
        
    }
    
    function do_daily() {
        // we just dont know - <° ?
    }
    
    function cycle_weather() {
        $cur_weather = get_globals('weather');
        
        while ($new_weather === $cur_weather) {
            $new_weather = Weather::random();
            
        }
    }
?>