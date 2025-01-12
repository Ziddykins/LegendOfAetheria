<?php
    declare(strict_types = 1);
    session_start();
    require 'vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'functions.php';

    $account = new Account($_SESSION['email']);

    if (isset($_POST['save']) && $_POST['save'] == 'ip_lock') {
        if (isset($_POST['status']) && $_POST['status'] == 'on') {
            $ip = $_POST['ip'];
            if (strlen($ip) >= 7 && strlen($ip) <= 15) {
                if (preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
                    $account->set_ipLock('True');
                    $account->set_ipLockAddr($ip);
                    echo "Successfully turned on IP Lock";
                } else {
                    echo "IP address invalid";
                }
            } else {
                echo "IP address invalid";
            }
        } else {
            $account->set_ipLock('False');
            $account->set_ipLockAddr('off');
            echo "Successfully turned off IP Lock";
        }
    }
?>