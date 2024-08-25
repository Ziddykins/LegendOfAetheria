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

    $account = table_to_obj($_SESSION['email'], 'account');

    if (isset($_POST['save']) && $_POST['save'] == 'ip_lock') {
        if (isset($_POST['status']) && $_POST['status'] == 'on') {
            $ip = $_POST['ip'];
            if (strlen($ip) >= 7 && strlen($ip) <= 15) {
                if (preg_match('/^[0-9]{1,3}\.(?:[0-9]{1,3}\.){2}[0-9]{1,3}$/', $ip)) {
                    $account['ip_lock'] = 'True';
                    $sql_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `ip_lock` = ?, `ip_lock_addr` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ 'True', $ip, $account['id'] ]);
                    echo "Successfully turned on IP Lock";
                } else {
                    echo "IP address invalid";
                }
            } else {
                echo "IP address invalid";
            }
        } else {
            $account['ip_lock'] = 'False';
            $sql_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `ip_lock` = ?, `ip_lock_addr` = ? WHERE `id` = ?";
            $db->execute_query($sql_query, [ 'False', 'off', $account['id'] ]);
            echo "Successfully turned off IP Lock";
        }
    }
?>