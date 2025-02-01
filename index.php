<?php
    declare(strict_types = 1);
    session_start();

    require 'vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'traits/trait-HandlePropsAndCols.php';
    require_once 'traits/trait-HandlePropSync.php';
    require_once 'functions.php';
    require_once 'mailer.php';
    
    require_once 'classes/class-account.php';
    require_once 'classes/class-inventory.php';
    require_once 'classes/class-character.php';

    if (isset($_REQUEST['login-submit']) && $_REQUEST['login-submit'] == 1) {
        $email = $_REQUEST['login-email'];
        $password = $_REQUEST['login-password'];

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
            $log->error('Attempted login with a non-existing account', [ 'Email' => $email ]);
            header("Location: /?do_register&email=$email");
            exit();
        }

        /* Password for supplied email was correct */
        if (password_verify($password, $account->get_password())) {
            /* Check if account is IP locked and verify IP logging in matches stored IP lock address */
            if ($account->get_ipLock() == 'True') {
                if ($account->get_ipLockAddr() != $_SERVER['REMOTE_ADDR']) {
                    $log->info("User tried to login from non-matching IP address on IP locked account",
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
                SELECT COUNT(*) AS `count` FROM {$_ENV['SQL_LOGS_TBL']} WHERE
                    `ip` = ? AND
                    `date` BETWEEN (NOW() - INTERVAL 1 HOUR) AND NOW() AND
                    `type` = 'MULTISIGNUP'
                SQL;

            $failed_login_count = $db->execute_query($sql_query_get_count, [ $ip ])->fetch_assoc()['count'];

            if ($failed_login_count >= 4) {
                header('Location: /banned');
                exit();
            } else {
                $message = "Failed login for IP Address $ip trying {$_REQUEST['login-email']}";
                //write_log('MULTISIGNUP', $message, $ip);
            }

            header('Location: /?failed_login');
            exit();
        }
    } else if (isset($_REQUEST['register-submit']) && $_REQUEST['register-submit'] == 1) {
        /* Account information */
        $email              = $_REQUEST['register-email'];
        $password           = $_REQUEST['register-password'];
        $password_confirm   = $_REQUEST['register-password-confirm'];
        $time_sqlformat     = get_mysql_datetime();
        $ip_address         = $_SERVER['REMOTE_ADDR'];
        $new_privs          = UserPrivileges::UNVERIFIED->value;
        $verification_code  = strrev(hash('sha256', session_id()));
        $verification_code .= substr(hash('sha256', strval(rand(0,100))), 0, 15);

        /* Character information */
        $char_name = preg_replace('/[^a-zA-Z0-9_-]+/', '', $_REQUEST['register-character-name']);

        $avatar = validate_avatar('avatar-' . $_REQUEST['avatar-select'] . '.webp');
        $race   = validate_race($_REQUEST['race-select']);

        $str    = $_REQUEST['str-ap'];
        $def    = $_REQUEST['def-ap'];
        $int    = $_REQUEST['int-ap'];

        if (!check_valid_email($email)) {
            header('Location: /?invalid_email');
            exit();
        }

        /* Email doesn't exist */
        if (!Account::checkIfExists($email)) {
            /* AP assigned properly */
            if ($str + $def + $int === MAX_ASSIGNABLE_AP) {
                /* ya forgin' posts I know it */

                /* Passwords match */
                if ($password === $password_confirm) {
                    $password = password_hash($password, PASSWORD_BCRYPT);

                    $account = new Account($email);
                    $account->new();
                    
                    /* Hasn't been found creating multiple accounts */
                    if (check_abuse(AbuseTypes::MULTISIGNUP, $account->get_id(), $ip_address, 3)) {
                        header('Location: /?abuse_signup');
                        exit();
                    }

                    if (($str < 10 || $def < 10 || $int < 10)) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        write_log(AbuseTypes::POSTMODIFY->name, "Sign-up attributes modified", $ip);
                        check_abuse(AbuseTypes::POSTMODIFY, $account->get_id(), $ip, 2);
                    }

                    $account->set_password($password);
                    $account->set_dateRegistered($time_sqlformat);
                    $account->set_privileges($new_privs);
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
                    $character->stats->set_maxHp(100);
                    $character->stats->set_mp(100);
                    $character->stats->set_maxMp(100);
 
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

    <body data-bs-theme="dark">


        <div class="d-flex align-items-center min-vh-100" style="min-width: 325px;">
            <div id="login-container" class="container shadow border border-secondary" style="max-width:550px; width: 100%;">
                <div class="row">
                    <div class="col p-4">
                        <img src="img/logos/logo-banner-no-bg.webp" alt="main-logo" class="w-100" #a/>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <?php require_once 'navs/nav-login.php'; ?>
                    </div>
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