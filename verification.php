<?php
    declare(strict_types = 1);
    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
        require 'vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();




    $account = new Account($_SESSION['email']);
    $account->load();

    if (isset($_GET['resend']) && $_GET['resend'] === 1) {
        send_mail($account->get_email(), $account);
        header('/game?resent_verification');
        exit();
    }
    
    if (isset($_POST['code'])) {
        $verification_code = $_POST['code'];
        $sql_query = "SELECT `id` FROM {$t['accounts']} WHERE `verification_code` = ? AND `email` = ?";
        $results = $db->execute_query($sql_query, [ $verification_code, $account->get_email() ]);
                
        /* 
            Player found with matching verification code,
            set privileges to a registered user
        */
        if ($results->num_rows) {
            $current_privs = $account->get_privileges()->value;
            
            if ($account->get_verified() === 'True' || $current_privs >= Privileges::USER->value) {
                $query_path = "/game?already_verified=1&email={$account->get_email()}";
                header("Location: $query_path");
                exit();
            }

            $sql_query = "UPDATE {$t['accounts']} SET `privileges` = '" . Privileges::USER->name . "', `verified` = 'True' WHERE `id` = {$account->get_id()}";
            $db->execute_query($sql_query);

            $log->info("User verification successful", [
                'User' => $account->get_email(),
                'Code' => $account->get_verificationCode()
            ]);

            header('Location: /game?verification_successful');
            exit();
        } else {
            header('Location: /?verification_failed');
            exit();
        }
    }
