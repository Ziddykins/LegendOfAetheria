<?php
    require_once 'bootstrap.php';

    use Game\Bank\BankManager;
    use Game\LoASys\Enums\AbuseType;
    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
    use Game\Character\Character;
        
    if (isset($_POST['login-submit']) && $_POST['login-submit'] == 1) {
        $email = $_POST['login-email'];
        $password = $_POST['login-password'];
        $ip = $_SERVER['REMOTE_ADDR'];

        // Rate limiting check
        $sql_query = <<<SQL
            SELECT COUNT(*) as attempt_count 
            FROM {$t['logs']} 
            WHERE `ip` = ? 
            AND `type` = 'LOGIN_ATTEMPT'
            AND `date` > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        SQL;
        
        $attempts = $db->execute_query($sql_query, [$ip])->fetch_assoc()['attempt_count'];
        
        if ($attempts >= 5) {
            $log->warning('Rate limit exceeded for IP', ['ip' => $ip]);
            header('Location: /?rate_limited');
            exit();
        }

        if (!check_valid_email($email)) {
            write_log('LOGIN_ATTEMPT', 'Invalid email format', $ip);
            header('Location: /?invalid_email');
            exit();
        }

        $account_id = Account::checkIfExists('email', $email, $_ENV['SQL_ACCT_TBL']);

        if ($account_id > 0) {
            $account = new Account($email);
            
            if (password_verify($password, $account->get_password())) {
                // Reset failed attempts on successful login
                $account->set_failedLogins(0);

                $_SESSION['logged-in'] = 1;
                $_SESSION['email'] = $account->get_email();
                $_SESSION['account-id'] = $account->get_id();
                $_SESSION['selected-slot'] = -1;
                $_SESSION['ip'] = $ip;
                $_SESSION['last_activity'] = time();
                
                $account->set_sessionID(session_id());
                $account->set_lastLogin(date('Y-m-d H:i:s'));

                header('Location: /select');
                exit();
            } else {
                $failed_attempts = $account->get_failedLogins() + 1;
                $account->set_failedLogins($failed_attempts);
                
                write_log('LOGIN_ATTEMPT', 'Failed login attempt', $ip);
                
                if ($failed_attempts >= 10) {
                    $account->set_banned('True');
                    $log->alert('Account locked due to excessive failed attempts', 
                        ['email' => $email, 'ip' => $ip]);
                }
                
                header('Location: /?failed_login');
                exit();
            }
        } else {
            $log->warning('Attempted login with a non-existing account', [ 'Email' => $email ]);
            header("Location: /?do_register&email=$email");
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
        if ($account_id > 0) {
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
            </div>
        </div>

        <?php if (isset($_COOKIES['email'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let up = new URLSearchParams(window.location.search);

                if (up.has('failed_login')) {
                    document.getElementById('login-email').value = decodeURIComponent(document.cookie).split(';')[0].split('=')[1];
                }
            });
        </script>
        <?php endif; ?>
        <?php include 'html/footers.html'; ?>
    </body>
</html>
