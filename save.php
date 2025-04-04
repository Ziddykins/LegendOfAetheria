<?php
    declare(strict_types = 1);
    use Game\Account\Account;
        require_once "bootstrap.php";

    $account = new Account($_SESSION['email']);
    $account->load();

    if (isset($_POST['save']) && $_POST['save'] == 'ip_lock') {
        if (isset($_POST['status']) && $_POST['status'] == 'on') {
            $ip = $_POST['ip'];

            if (strlen($ip) >= 7 && strlen($ip) <= 15) {
                if (preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
                    $account->set_ipLock('True');
                    $account->set_ipLockAddr($ip);
                    echo "Successfully turned on IP Lock";
                } else {
                    http_response_code(400);
                    echo "IP address invalid format";
                    exit();
                }
            } else {
                http_response_code(400);
                echo "IP address invalid length";
                exit();
            }
        } else {
            $account->set_ipLock('False');
            $account->set_ipLockAddr('off');
            echo "Successfully turned off IP Lock";
        }
    }
?>