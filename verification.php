<?php
    declare(strict_types = 1);
    session_start();
    require '../../../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    include 'logger.php';
    require_once 'db.php';
    include 'constants.php';
    include 'functions.php';
    
    if (isset($_REQUEST['code']) && isset($_REQUEST['email'])) {
        $verification_code = $_REQUEST['code'];
        $email             = $_REQUEST['email'];
        
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_ACCT_TBL'] . ' WHERE `verification_code` = ? AND `email` = ?';
        
        $prepped = $db->prepare($sql_query);
        $prepped->bind_param('ss', $verification_code, $email);
        $prepped->execute();
        
        $results = get_results();
        
        /* 
            Player found with matching verification code,
            set privileges to a registered user
        */
        if ($results->num_rows) {
            $player = $results->fetch_assoc();
            $db->query('UPDATE ' . $_ENV['SQL_ACCTS_TBL'] . ' ' .
                         'SET `privileges` = "' . UserPrivileges::VERIFIED->name . '" ' .
                         'WHERE `id` = ' . $player['id']
            );
            
            $log->info("User verification successful",
                        [
                            'User' => $player['email'],
                            'Code' => $player['verification_code']
                        ]
            );
        } else {
            header('Location: /?verification_failed');
            exit();
        }
    }
