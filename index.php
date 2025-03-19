<?php
    declare(strict_types = 1);
    use Game\System\Enums\AbuseType;
    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
    use Game\Character\Character;

    require_once 'bootstrap.php';
    session_start();

    if (isset($_POST['login-submit']) && $_POST['login-submit'] == 1) {
        $email    = $_POST['login-email'];
        $password = $_POST['login-password'];
        $account  = null;

        if (!check_valid_email($email)) {
            header('Location: /?invalid_email');
            exit();
        }

        $account_id = Account::checkIfExists($email);

        if ($account_id > 0) {
            $account = new Account($email);
            $account->load($account_id);
        } else {
            $log->warning('Attempted login with a non-existing account', [ 'Email' => $email ]);
            header("Location: /?do_register&email=$email");
            exit();
        }

        /* Password for supplied email was correct */
        if (password_verify($password, $account->get_password())) {
            /* Check if account is IP locked and verify IP logging in matches stored IP lock address */
            if ($account->get_ipLock() == 'True') {
                if ($account->get_ipLockAddr() != $_SERVER['REMOTE_ADDR']) {
                    $log->warning("User tried to login from non-matching IP address on IP locked account",
                        [ "On File" => $account->get_ipLockAddr(), "Current" => $_SERVER['REMOTE_ADDR'] ]);
                    header('Location: /?ip_locked');
                    exit();
                }
            }

            $log->info('Account login success', [
                'Email'      => $account->get_email(),
                'Privileges' => $account->get_privileges(),
                'IpAddr'     => $account->get_ipAddress()
            ]);

            $_SESSION['logged-in']     = 1;
            $_SESSION['email']         = $account->get_email();
            $_SESSION['account-id']    = $account->get_id();
            $_SESSION['selected-slot'] = -1;

            $account->set_sessionID(session_id());
                        
            header('Location: /select');
            exit();
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
            $sql_query_get_count = <<<SQL
                SELECT COUNT(*) AS `count` FROM {$_ENV['SQL_LOGS_TBL']} WHERE`ip` = ? AND
                    `date` BETWEEN (NOW() - INTERVAL 1 HOUR) AND NOW() AND
                    `type` = 'MULTISIGNUP'
                SQL;

            $failed_login_count = $db->execute_query($sql_query_get_count, [ $ip ])->fetch_assoc()['count'];

            if ($failed_login_count >= 4) {
                header('Location: /banned');
                exit();
            } else {
                $message = "Failed login for IP Address $ip trying {$_POST['login-email']}";
                //write_log('MULTISIGNUP', $message, $ip);
            }

            header('Location: /?failed_login');
            exit();
        }
    } elseif (isset($_POST['register-submit']) && $_POST['register-submit'] == 1) {
        /* Account information */
        $email              = $_POST['register-email'];
        $password           = $_POST['register-password'];
        $password_confirm   = $_POST['register-password-confirm'];
        $time_sqlformat     = get_mysql_datetime();
        $ip_address         = $_SERVER['REMOTE_ADDR'];
        $verification_code  = strrev(hash('sha256', session_id()));
        $verification_code .= substr(hash('sha256', strval(mt_rand(0,100))), 0, 15);

        /* Character information */
        $char_name = preg_replace('/[^a-zA-Z0-9_-]+/', '', $_POST['register-character-name']);

        $avatar = validate_avatar('avatar-' . $_POST['avatar-select'] . '.webp');
        $race   = validate_race($_POST['race-select']);

        $str    = $_POST['str-ap'];
        $def    = $_POST['def-ap'];
        $int    = $_POST['int-ap'];

        if (!check_valid_email($email)) {
            header('Location: /?invalid_email');
            exit();
        }

        /* Email doesn't exist */
        if (Account::checkIfExists($email) == -1) {
            /* AP assigned properly */
            if ($str + $def + $int === MAX_ASSIGNABLE_AP) {
                /* Passwords match */
                if ($password === $password_confirm) {
                    $password = password_hash($password, PASSWORD_BCRYPT);

                    $account = new Account($email);
                    $account->new();
                    
                    /* Hasn't been found creating multiple accounts */
                    if (check_abuse(AbuseType::MULTISIGNUP, $account->get_id(), $ip_address, 3)) {
                        ban_user($account->get_id(), 3600, "Multiple accounts within allotted time frame");
                        exit();
                    }
                    
                    /* ya forgin' posts I know it */
                    if (($str < 10 || $def < 10 || $int < 10)) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        write_log(AbuseType::TAMPERING->name, "Sign-up attributes modified", $ip);
                        check_abuse(AbuseType::TAMPERING, $account->get_id(), $ip, 2);
                    }

                    if ($account->get_id() === 1) {
                        $account->set_privileges(Privileges::ADMINISTRATOR->value);
                    } else {
                        $account->set_privileges(Privileges::UNVERIFIED->value);
                    }

                    $account->set_password($password);
                    $account->set_dateRegistered($time_sqlformat);
                    $account->set_ipAddress($ip_address);
                    $account->set_loggedIn('False');
                    $account->set_verificationCode($verification_code);

                    $character = new Character($account->get_id());
                    $character->new();

                    $character->set_avatar($avatar);
                    $character->set_name($char_name);
                    $character->set_race($race);

                    $character->stats->set_str((int) $str);
                    $character->stats->set_int((int) $int);
                    $character->stats->set_def((int) $def);

                    $character->stats->set_hp(100);
                    $character->stats->set_maxHP(100);
                    $character->stats->set_mp(100);
                    $character->stats->set_maxMP(100);
 
                    //$character->stats->set_status(CharacterStatus::HEALTHY);

                    //send_mail($email, $account);
                    header('Location: /?register_success');
                    exit();
                }
            }
        } else {
            header('Location: /?account_exists');
            exit();
        }
    }
   
    include 'html/opener.html';
?>

    <head>
        <?php include 'html/headers.html'; ?>

    </head>

    <body data-bs-theme="dark" class="main-font">


        <div class="d-flex align-items-center min-vh-100" style="min-width: 60%;">
            <div id="login-container" class="container shadow border border-round border-1 border-tertiary" style="max-width:550px; width: 100%;">
                <div class="row">
                    <div class="col p-4">
                        <div class="logo-container">
                            <img src="img/logos/logo-banner-no-bg.webp" alt="main-logo" class="w-100" />
                            <div class="ee-dot" onclick="handleEasterEgg()"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <?php require_once 'navs/nav-login.php'; ?>
                </div>

                <div aria-live="polite" aria-atomic="true" class="position-relative">
                    <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'html/footers.html'; ?>

    </body>
</html>