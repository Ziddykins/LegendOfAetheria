<?php
    declare(strict_types = 1);
    session_start();
    
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    include('db.php');
    include('constants.php');
    include('functions.php');

    if (isset($_POST['login-submit'])) {
        
    } else if (isset($_POST['register-submit']) && $_POST['register-submit'] == 1) {
        $email            = $_POST['register-email'];
        $password         = $_POST['register-password'];
        $password_confirm = $_POST['register-password-confirm'];
        $char_name        = $_POST['register-character-name'];
        $race             = $_POST['race-select'];
        $avatar           = $_POST['avatar-select'];
        $str              = $_POST['str-ap'];
        $def              = $_POST['def-ap'];
        $int              = $_POST['int-ap'];

        $time_sqlformat = get_mysql_datetime();
        $ip_address     = $_SERVER['REMOTE_ADDR'];
        $new_privs      = UserPrivileges::UNVERIFIED->value;

        $prepped = $db->prepare('SELECT COUNT(*) FROM ' . $_ENV['SQL_ACCT_TBL'] . ' WHERE email = ?');
        $prepped->bind_param('s', $email);
        $prepped->execute();
        $result = $prepped->get_result();

        /* Email doesn't exist */
        if ($prepped->num_rows() === 0) {
            /* AP Assigned properly */
            if ($str + $def + $int === MAX_ASSIGNABLE_AP) {
                /* Passwords match */
                if ($password === $password_confirm) {
                    $verification_code = strrev(sha1(session_id()));
                    $password = password_hash($password, PASSWORD_BCRYPT);

                    /* TODO: Actually implement */
                    // gen_verification_email(); // :D

                    $sql_query =' INSERT INTO ' . $_ENV['SQL_ACCT_TBL'] . 
                                ' (email, password, date_registered, verification_code, privileges, ip_address) ' .
                                ' VALUES (?, ?, ?, ?, ?, ?)';

                    $prepped = $db->prepare($sql_query);
                    $prepped->bind_param('ssssis', $email, $password, $time_sqlformat, $verification_code, $new_privs, $ip_address);
                    $prepped->execute();
                }
            }
        }
    }
?>

<?php include('html/opener.html'); ?>

    <head>
        <?php include('html/headers.html'); ?><!-- :D -->
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
                        <?php include('html/nav-login.html'); ?>
                </div>
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

    dunugeon
    <i class="bi bi-bricks"></i>

    char sheet
    <i class="bi bi-card-heading"></i>

    compl quests
    <i class="bi bi-clipboard-check-fill"></i>

    in prog quests
    <i class="bi bi-clipboard-fill"></i>

    weather - rain
    <i class="bi bi-cloud-drizzle-fill"></i>

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