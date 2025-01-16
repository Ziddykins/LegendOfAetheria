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
    require_once 'mailer.php';
    require_once 'classes/class-account.php';
    require_once 'classes/class-character.php';

    if (isset($_REQUEST['login-submit']) && $_REQUEST['login-submit'] == 1) {
        $email    = $_REQUEST['login-email'];
        $password = $_REQUEST['login-password'];

        if (!check_valid_email($email)) {
            header('Location: /?invalid_email');
            exit();
        }

        $sql_query   = "SELECT * FROM {$_ENV['SQL_ACCT_TBL']} WHERE `email` = ?";
        $tmp_account = $db->execute_query($sql_query, [ $email ])->fetch_assoc();
        $account     = null;

        if ($tmp_account) {
            $account = new Account($tmp_account['id']);
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
                    `type` = 'FAILED_LOGIN'
                SQL;

            $failed_login_count = $db->execute_query($sql_query_get_count, [$_SERVER['REMOTE_ADDR']])->fetch_assoc()['count'];

            if ($failed_login_count >= 4) {
                header('Location: /banned');
                exit();
            } else {
                $prepped      = NULL;
                $sql_datetime = get_mysql_datetime();
                $sql_query    = "INSERT INTO {$_ENV['SQL_LOGS_TBL']} (date, type, message, ip) VALUES (?, ?, ?, ?)";
                $login_error  = "Failed login for IP Address {$_SERVER['REMOTE_ADDR']} trying {$_REQUEST['login-email']}";
                $error_type   = 'FAILED_LOGIN';

                $db->execute_query($sql_query, [ $sql_datetime, $error_type, $login_error, $_SERVER['REMOTE_ADDR'] ]);
            }

            $log->error('Account login FAILED', [
                'Email'      => $email,
                'IpAddr'     => $_SERVER['REMOTE_ADDR']
            ]);

            header('Location: /?failed_login');
            exit();
        }
    } else if (isset($_REQUEST['register-submit']) && $_REQUEST['register-submit'] == 1) {
        $avatar           = 'avatar-' . $_REQUEST['avatar-select'] . '.webp';
        $email            = $_REQUEST['register-email'];
        $password         = $_REQUEST['register-password'];
        $password_confirm = $_REQUEST['register-password-confirm'];
        $char_name        = $_REQUEST['register-character-name'];
        $race             = $_REQUEST['race-select'];
        $str              = $_REQUEST['str-ap'];
        $def              = $_REQUEST['def-ap'];
        $int              = $_REQUEST['int-ap'];

        if (!check_valid_email($email)) {
            header('Location: /?invalid_email');
            exit();
        }

        $char_name = preg_replace('/[^a-zA-Z0-9_-]+/', '', $char_name);

        $time_sqlformat   = get_mysql_datetime();
        $ip_address       = $_SERVER['REMOTE_ADDR'];
        $new_privs        = UserPrivileges::UNVERIFIED->value;

        $sql_query = "SELECT * FROM {$_ENV['SQL_ACCT_TBL']} WHERE email = ?";
        $result = $db->execute_query($sql_query, [ $email ]);
        $row_count = $result->num_rows;

        /* Email doesn't exist */
        if (!$row_count) {
            /* AP assigned properly */
            if ($str + $def + $int === MAX_ASSIGNABLE_AP) {
                /* Passwords match */
                if ($password === $password_confirm) {
                    $verification_code  = strrev(hash('sha256', session_id()));
                    $verification_code .= substr(hash('sha256', strval(rand(0,100))), 0, 15);

                    $password = password_hash($password, PASSWORD_BCRYPT);

                    //$sql_query = "INSERT INTO {$_ENV['SQL_LOGS_TBL']} (`type`, `message`, `ip`) VALUES (?, ?, ?)";
                    //$db->execute_query($sql_query, [ "AccountCreate", "Account created for user {$account->get_email()}", $ip_address ]);

                    if (check_abuse(AbuseTypes::MULTISIGNUP, $ip_address)) {
                        header('Location: /?abuse_signup');
                        exit();
                    }

                    $sql_query =' INSERT INTO ' . $_ENV['SQL_ACCT_TBL'] . ' ' .
                        '(email, password, date_registered, ' .
                        'verification_code, privileges, ip_address) ' . ' ' .
                        'VALUES (?, ?, ?, ?, ?, ?)';

                    $db->execute_query($sql_query, [ $email, $password, $time_sqlformat, $verification_code, $new_privs, $ip_address ]);

                    $arr_images = scandir('img/avatars');

                    if (!array_search($avatar, $arr_images)) {
                        $avatar_now = 'avatar-unknown.jpg';
                        $log->error(
                            'Avatar wasn\'t found in our ' .
                            'accepted list of avatar choices!',
                            [
                                'Avatar' => $avatar,
                                'Avatar_now' => $avatar_now,
                            ]
                        );
                        $avatar = $avatar_now;
                    }

                    $valid_race = 0;

                    foreach (Races::cases() as $enum_race) {
                        if ($race === $enum_race->name) {
                            $valid_race = 1;
                        }
                    }

                    if (!$valid_race) {
                        $race = Races::random()->name;
                        $log->error(
                            'Race submitted wasn\'t an acceptable selection, ' .
                            'choosing random enum: ',
                            [ 'Race' => $race ]
                        );
                    }

                    $sql_query  = "SELECT MAX(`id`) AS `account_id` FROM {$_ENV['SQL_ACCT_TBL']}";
                    $result     = $db->execute_query($sql_query)->fetch_assoc();
                    $account_id = $result['account_id'];





                    if (!$db->execute_query($sql_query, [ $account_id, $avatar, $char_name, $race, $str, $def, $int ])) {
                        $log->critical('Couldn\'t insert user information into character table');
                    }

                    $character = new Character($account_id, 1);
                    $character->set_avatar($avatar);
                    $character->set_name($char_name);
                    $character->set_race($race);
                    $character->set_slot(Character)

                    $character->stats->set_str($str);
                    $character->stats->set_int($int);
                    $character->stats->set_def($def);

                    $character->stats->set_hp(100);
                    $character->stats->set_maxHp(100);
                    $character->stats->set_mp(100);
                    $character->stats->set_maxMp(100);

                    $character->stats->set_status(CharacterStatus::HEALTHY);

                    Character::save_character($character);

                    // Verification email
                    $account = table_to_obj($email, 'account');

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

    <body>
    <div class="btn-group p-3 m-3 invisible" role="group" aria-label="basic outlined example">
        <button type="button" class="btn btn-sm btn-success bg-gradient text-center text-shadow fw-bolder font-monospace border border-black border-round" style="text-shadow: black 0.45px 0.75px 0.5px; transform: skewX(9deg);">Pass</button>
        <button type="button" class="btn btn-sm text-bg-dark bg-gradient text-center fw-bolder font-monospace border border-black border-round" style="text-shadow: black 0.45px 0.75px 0.5px; transform: skewX(9deg);">Autoinstaller</button>
        <button type="button" class="btn btn-sm text-bg-danger bg-gradient text-center font-monospace border border-black border-round" style="transform: skewX(9deg);"></button>
    <div class="btn-group" role="group" aria-label="basic outlined example">
</div>
</div>

<div class="container" style="min-width: 325px;">
    <div id="login-container" class="container shadow" style="max-width:500px; width: 100%;">
        <div class="row">
            <div class="col p-4">
                <img src="img/logos/logo-banner-no-bg.webp" alt="main-logo" class="w-100"></img>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php require_once 'navs/nav-login.php'; ?>

            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>

                </div>
            </div>
        </div>
    </div>

    <?php include 'html/footers.html'; ?>
</div>
    </body>
</html>
