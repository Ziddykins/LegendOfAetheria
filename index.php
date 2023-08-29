<?php
    declare(strict_types = 1);
    session_start();

    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    
    include('logger.php');
    include('db.php');
    include('constants.php');
    include('functions.php');
    include('mailer.php');

    $log->info('Session started: ', [ 'Session' => session_id() ]);

    /* Login submitted */
    if (isset($_POST['login-submit']) && $_POST['login-submit'] == 1) {
        $email    = $_POST['login-email'];
        $password = $_POST['login-password'];

        $account = get_user($email, 'account');

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

            $character = get_user($account['email'], 'character');

            $_SESSION['logged-in'] = 1;
            $_SESSION['email'] = $account['email'];
            $_SESSION['account_id'] = $character['account_id'];
            $_SESSION['name'] = $character['name'];
            header('Location: /game');
            exit();
        } else {
            $log->error('Account login FAILED', [
                'Email'      => $email,
                'IpAddr'     => $_SERVER['REMOTE_ADDR']
            ]);
            header('Location: /?failed_login');
            exit();
        }
    } else if (isset($_POST['register-submit']) && $_POST['register-submit'] == 1) {
        $avatar           = 'avatar-' . $_POST['avatar-select'] . '.png';
        $email            = $_POST['register-email'];
        $password         = $_POST['register-password'];
        $password_confirm = $_POST['register-password-confirm'];
        $char_name        = $_POST['register-character-name'];
        $race             = $_POST['race-select'];
        $str              = $_POST['str-ap'];
        $def              = $_POST['def-ap'];
        $intl             = $_POST['int-ap'];

        $time_sqlformat   = get_mysql_datetime();
        $ip_address       = $_SERVER['REMOTE_ADDR'];
        $new_privs        = UserPrivileges::UNVERIFIED->value;

        $prepped = $db->prepare('SELECT * FROM ' . $_ENV['SQL_ACCT_TBL'] . ' WHERE email = ?');
        $prepped->bind_param('s', $email);
        $prepped->execute();
        $result = $prepped->get_result();
        $account = $result->fetch_assoc();

        /* Email doesn't exist */
        if ($result->num_rows == 0) {
            /* AP assigned properly */            
            if ($str + $def + $intl === MAX_ASSIGNABLE_AP) {
                /* Passwords match */
                if ($password === $password_confirm) {
                    $verification_code = strrev(sha1(session_id())) . substr(md5(strval(rand(0,100))), 0, 15);
                    $password = password_hash($password, PASSWORD_BCRYPT);

                    $sql_query =' INSERT INTO ' . $_ENV['SQL_ACCT_TBL'] . 
                                ' (email, password, date_registered, verification_code, privileges, ip_address) ' .
                                ' VALUES (?, ?, ?, ?, ?, ?)';

                    $prepped = $db->prepare($sql_query);
                    $prepped->bind_param('ssssis', $email, $password, $time_sqlformat, $verification_code, $new_privs, $ip_address);
                    $prepped->execute();

                    $arr_images = scandir('img/avatars');
                    
                    if (!array_search($avatar, $arr_images)) {
                        $avatar_now = 'avatar-tetra-dechahedron.png';
                        $log->error('Avatar wasn\'t found in our accepted list of avatar choices!',
                                        [ 'Avatar' => $avatar, 'Avatar_now' => $avatar_now ] );
                        $avatar = $avatar_now;
                    }

                    $valid_race = 0;
                    foreach(Races::cases() as $enum_race) {
                        if ($race === $enum_race->name) {
                            $valid_race = 1;
                        }
                    }

                    if (!$valid_race) {
                        $race = Races::random()->name;
                        $log->error('Race submitted wasn\'t an acceptable selection, ' .
                                        'choosing random enum: ', [ 'Race' => $race ] );
                    }

                    $query = $db->query('SELECT MAX(id) AS account_id FROM ' . $_ENV['SQL_ACCT_TBL']);
                    $result = $query->fetch_assoc();
                    $account_id = $result['account_id'];

                    $sql_query = 'INSERT INTO ' . $_ENV['SQL_CHAR_TBL'] . 
                                    ' (`account_id`, `avatar`, `name`, `race`, `str`, `def`, `int`)' .
                                    ' VALUES (?, ?, ?, ?, ?, ?, ?)';

                    $prepped = $db->prepare($sql_query);
                    $prepped->bind_param('isssiii', $account_id, $avatar, $char_name, $race, $str, $def, $intl);
                    
                    if (!$prepped->execute()) {
                        $log->critical('Couldn\'t insert user information into character table');
                    }

                    
                    // Verification email
                    send_mail($email);

                    header('Location: /?register_success');
                    exit();
                }
            }
        } else {
            header('Location: /?account_exists');
            exit();
        }
    }
?>

<?php include('html/opener.html'); ?>

    <head>
        <?php include('html/headers.html'); ?>
    </head>
    
    <body class="">    
        <div class="container-sm my-5 shadow" style="width: 50%;">
            <div class="row">
                <div class="col p-4">
                    <img src="img/logos/logo-banner-no-bg.png" style="width: 100%;"></img>
                </div>
            </div>
            <div class="row">
                <div class="col">
                        <?php include('navs/nav-login.php'); ?>

            </div>
        </div>
    </body>
</html>
                
<!--
    vitality or hp - change color
    <i class="bi bi-activity"></i>

    badge / alert
    <i class="bi bi-bookmark-fill"></i>

    storage
    <i class="bi bi-box2-heart-fill"></i>

    char sheet
    <i class="bi bi-card-heading"></i>

    compl quests
    <i class="bi bi-clipboard-check-fill"></i>

    in prog quests
    <i class="bi bi-clipboard-fill"></i>

    weather - rain
    

    weather - snow
    <i class="bi bi-cloud-snow-fill"></i>

    account 
    <i class="bi bi-credit-card-2-front-fill"></i>

    map
    <i class="bi bi-cursor-fill"></i>
    <i class="bi bi-map-fill"></i>

    community - wide
    <i class="bi bi-diagram-3-fill"></i>

    community - near
    <i class="bi bi-diagram-2-fill"></i>

    discord
    <i class="bi bi-discord"></i>

    status - ded
    <i class="bi bi-emoji-dizzy-fill"></i>

    status - alive
    <i class="bi bi-emoji-laughing-fill"></i>

    mail - impertern
    <i class="bi bi-envelope-exclamation-fill"></i>

    mail - read
    <i class="bi bi-envelope-open-fill"></i>

    mail - unread
    <i class="bi bi-envelope-fill"></i>

    dungeon - ladder
    <i class="bi bi-ladder"></i>

    dungeon? - key
    <i class="bi bi-key-fill"></i>

    academy
    <i class="bi bi-mortarboard-fill"></i>

    profile icon
    <i class="bi bi-person-circle"></i>

    monies
    <i class="bi bi-currency-exchange"></i>
-->