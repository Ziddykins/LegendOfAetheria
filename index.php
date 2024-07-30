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

    /* Login submitted */
    if (isset($_REQUEST['login-submit']) && $_REQUEST['login-submit'] == 1) {
        $email    = $_REQUEST['login-email'];
        $password = $_REQUEST['login-password'];

        $account = table_to_obj($email, 'account');

        if (!$account) {
            $log->error('Attempted login with a non-existing account', [ 'Email' => $email ]);
            header("Location: /?do_register&email=$email");
            exit();
        }

        /* Password for supplied email was correct */
        if (password_verify($password, $account['password'])) {
            $log->info('Account login success', [
                'Email'      => $account['email'],
                'Privileges' => $account['privileges'],
                'IpAddr'     => $account['ip_address']
            ]);

            $character = table_to_obj($account['email'], 'character');

            $_SESSION['logged-in'] = 1;
            $_SESSION['email'] = $account['email'];
            
            $db->execute_query(
                'UPDATE tbl_accounts SET session_id = ? WHERE id = ?',
                [ session_id(), $account['id'] ]
            );
            
            header('Location: /game');
            exit();
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
            $sql_query_get_count       = 'SELECT COUNT(*) AS `count` FROM ' . $_ENV['SQL_LOGS_TBL'] . ' WHERE ' .
                                            'ip = ? AND ' .
                                            '`date` BETWEEN (NOW() - INTERVAL 1 HOUR) AND NOW() AND ' .
                                            "type = 'FAILED_LOGIN'";

            $failed_login_count = $db->execute_query($sql_query_get_count, [$_SERVER['REMOTE_ADDR']])->fetch_assoc()['count'];

            if ($failed_login_count >= 4) {
                header('Location: /banned');
                exit();
            } else {
                $prepped      = NULL;
                $sql_datetime = get_mysql_datetime();
                $sql_query    = "INSERT INTO " . $_ENV['SQL_LOGS_TBL'] . " (date, type, message, ip) VALUES (?, ?, ?, ?)";
                $login_error  = 'Failed login for IP Address ' . $_SERVER['REMOTE_ADDR'] . ', trying ' . $_REQUEST['login-email'];
                $error_type   = 'FAILED_LOGIN';

                $prepped      = $db->prepare($sql_query);
                $prepped->bind_param('ssss', $sql_datetime, $error_type, $login_error, $_SERVER['REMOTE_ADDR']);

                $prepped->execute();
                $result  = $prepped->get_result();
            }
            
            $log->error('Account login FAILED', [
                'Email'      => $email,
                'IpAddr'     => $_SERVER['REMOTE_ADDR']
            ]);

            header('Location: /?failed_login');
            exit();
        }
    } else if (isset($_REQUEST['register-submit']) 
            && $_REQUEST['register-submit'] == 1) {
        $avatar           = 'avatar-' . $_REQUEST['avatar-select'] . '.webp';
        $email            = $_REQUEST['register-email'];
        $password         = $_REQUEST['register-password'];
        $password_confirm = $_REQUEST['register-password-confirm'];
        $char_name        = $_REQUEST['register-character-name'];
        $race             = $_REQUEST['race-select'];
        $str              = $_REQUEST['str-ap'];
        $def              = $_REQUEST['def-ap'];
        $intl             = $_REQUEST['int-ap'];
        
        $char_name = preg_replace('/[^a-zA-Z0-9_-]+/', '', $char_name);


        $time_sqlformat   = get_mysql_datetime();
        $ip_address       = $_SERVER['REMOTE_ADDR'];
        $new_privs        = UserPrivileges::UNVERIFIED->value;

        $prepped = $db->prepare('SELECT * FROM ' . $_ENV['SQL_ACCT_TBL'] . ' ' .
                                'WHERE email = ?');
        $prepped->bind_param('s', $email);
        $prepped->execute();
        
        $result  = $prepped->get_result();
        $account = $result->fetch_assoc();

        /* Email doesn't exist */
        if ($result->num_rows == 0) {
            /* AP assigned properly */            
            if ($str + $def + $intl === MAX_ASSIGNABLE_AP) {
                /* Passwords match */
                if ($password === $password_confirm) {
                    $verification_code  = strrev(hash('sha256', session_id())); 
                    $verification_code .= substr(hash('sha256', strval(rand(0,100))), 0, 15);
                    
                    $password = password_hash($password, PASSWORD_BCRYPT);

                    $sql_query =' INSERT INTO ' . $_ENV['SQL_ACCT_TBL'] . ' ' .
                        '(email, password, date_registered, ' .
                        'verification_code, privileges, ip_address) ' . ' ' .
                        'VALUES (?, ?, ?, ?, ?, ?)';

                    $prepped = $db->prepare($sql_query);
                    $prepped->bind_param('ssssis',
                        $email, $password, $time_sqlformat, $verification_code, 
                        $new_privs, $ip_address
                    );
                    
                    $prepped->execute();

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

                    $query = $db->query(
                        'SELECT MAX(id) AS account_id FROM ' . $_ENV['SQL_ACCT_TBL']
                    );

                    $result     = $query->fetch_assoc();
                    $account_id = $result['account_id'];

                    $sql_query = 'INSERT INTO ' . $_ENV['SQL_CHAR_TBL'] . ' ' .
                        '(`account_id`, `avatar`, `name`, `race`, ' . ' ' .
                        '`str`, `def`, `int`) VALUES (?, ?, ?, ?, ?, ?, ?)';

                    $prepped = $db->prepare($sql_query);
                    $prepped->bind_param(
                        'isssiii',
                        $account_id, $avatar, $char_name, $race, $str, $def, $intl
                    );

                    if (!$prepped->execute()) {
                        $log->critical(
                            'Couldn\'t insert user information into character table'
                        );
                    }
    
/*                    $character = new Character($account_id, $email);

                        $character->setStats('strength',     $str);
                        $character->setStats('intelligence', $intl);
                    $character->setStats('defense',      $def);
                         
                    $character->setStats('hp',     100);
                    $character->setStats('maxHp',  100);
                    $character->setStats('mp',     100);
                        $character->setStats('maxMp',  100);
            
                        $character->setStats('status', 'healthy');
                    
                    $serialized_data = serialize($character); 
                        $log->info("serdata: $serialized_data");
                    save_character($account_id, $serialized_data);
                        // Verification email
                    // send_mail($email);
 */
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
        <div class="container-sm my-5 shadow" style="width: 70%;">
            <div class="row">
                <div class="col p-4">
                    <img src="img/logos/logo-banner-no-bg.webp" alt="main-logo" style="width: 100%;"></img>
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
    </body>
</html>
