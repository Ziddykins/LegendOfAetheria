<?php
    declare(strict_types = 1);
    session_start();
    require __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    include 'logger.php';
    include 'db.php';
    include 'constants.php';
    include 'functions.php';
    
    global $log;
    
    $account   = get_user($_SESSION['email'], 'account');
    $character = get_user($account['id'], 'character');

    $char_menu_icon = $character['hp'] > 0 ? 'bi-emoji-laughing-fill' : 'bi-emoji-dizzy-fill';

    $_SESSION['name'] = $character['name'];

    if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 1) {
        if (isset($_POST['profile-apply']) && $_POST['profile-apply'] == 1) {
            $old_password     = $_POST['profile-old-password'];
            $new_password     = $_POST['profile-new-password'];
            $confirm_password = $_POST['profile-confirm-password'];
            $account_email    = $_SESSION['email'];
    
            /* Old password matches current */
            if (password_verify($old_password, $account['password'])) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $sql_query = 'UPDATE ' . $_ENV['SQL_ACCT_TBL'] . ' SET `password` = ? WHERE `email` = ?';
                    $prepped   = $db->prepare($sql_query);
                    $prepped->bind_param('ss', $hashed_password, $account_email);
                    $prepped->execute();
                    session_regenerate_id();
                    header('Location: /logout?action=pw_reset&result=pass');
                    exit();
                }
                
            } else {
                header('Location: /game?page=profile&action=pw_reset&result=fail');
                exit();
            }
        }
    } else {
        header('Location: /?no_login');
        exit();
    }
?>

<?php include('html/opener.html'); ?>
    <head>
        <?php include('html/headers.html'); ?>
        
    </head>
        
    <body> 
        <div class="container-fluid border">
            <div class="row flex-nowrap" style="min-height: 99.5vh!important;">
                <div class="col-2 px-sm-2 px-0 border border-grey">
                    <div class="d-flex flex-column align-items-center align-items-sm-start px-3">
                        <a href="/game" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                            <img src="img/logos/logo-banner-no-bg.png" class="p-3 w-100">
                        </a>

                        <hr style="width: 35%; opacity: .25; align-self: center;">
                        
                        <ul class="nav nav-pills flex-column mb-0 align-items-center align-items-sm-start mb-sm-auto" id="menu">
                            <li>
                                <a href="#sub-menu-character" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi <?php echo $char_menu_icon; ?>"></i>
                                    <span class="ms-1 d-none d-sm-inline fs-6">Character</span>
                                </a>
                                
                                <ul class="nav collapse flex-column" id="sub-menu-character" data-bs-parent="#menu" aria-expanded="false">
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <i class="bi bi-card-text ms-4"></i><span class="d-none d-sm-inline"> Sheet</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                        <i class="bi bi-box ms-4"></i><span class="d-none d-sm-inline"> Inventory</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Skills</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Spells</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Train</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="#sub-menu-travel" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi bi-signpost-split-fill"></i>
                                    <span class="ms-1 d-none d-sm-inline fs-6">Location</span>
                                </a>
                            
                                <ul class="collapse nav flex-column ms-1" id="sub-menu-travel" data-bs-parent="#menu" aria-expanded="false">
                                    <li>
                                        <a href="?page=hunt" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Hunt</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Map</span>
                                        </a>
                                    </li>
                                    <li class="w-100">
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Explore</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Change Location</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <i class="bi bi-brightness-alt-high ms-4"></i><span class="d-none d-sm-inline"> Rest</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="#sub-menu-dungeon" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                                    <i class="fs-4 bi bi-bricks"></i>
                                    <span class="ms-1 d-none d-sm-inline fs-6">Dungeon</span>
                                </a>

                                <ul class="collapse nav flex-column ms-1" id="sub-menu-dungeon" data-bs-parent="#menu" aria-expanded="false">
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Floor <?php echo $character['floor']; ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline text-danger ms-4">Reset</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="#sub-menu-quests" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi bi-clipboard-fill"></i>
                                    <span class="ms-1 d-none d-sm-inline fs-6">Quests</span>
                                </a>
                                <ul class="collapse nav flex-column ms-1" id="sub-menu-quests" data-bs-parent="#menu">
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Active</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Completed</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Achievements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline ms-4">Awards</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
    
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="img/avatars/<?php echo $character['avatar']; ?>" alt="avatar" width="50" height="50" class="rounded-circle" />
                            <span class="d-none d-sm-inline mx-1 ms-5 fs-5">Account</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu text-small shadow">
                            <li><a class="dropdown-item" href="?page=profile">Profile</a></li>
                            <li><a class="dropdown-item" href="?page=friends">Friends</a></li>
                            <li><a class="dropdown-item" href="?page=mail">Mail
                                    <span class="badge bg-danger rounded-pill"> 5</span>
                            </a></li>
                            <li><a class="dropdown-item" href="?page=settings">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php
                                $privileges = UserPrivileges::name_to_value($account['privileges']);
                                if ($privileges > UserPrivileges::MODERATOR->value) {
                                    echo '<li><a class="dropdown-item" href="?page=administrator">Administrator</a></li>';
                                }
                            ?>
                            <li><a class="dropdown-item" href="/logout">Sign out</a></li>
                        </ul>
                    </div>
                </div>

                <div id="content" name="content" class="container border border-danger" style="flex-shrink: 1;">
                    <?php
                        include('navs/nav-status.php');
                    ?>
                    <?php
                        if (isset($_GET['page'])) {
                            $requested_page = preg_replace('/[^a-z-]+/', '', $_GET['page']);
                            $page_uri = 'pages/game-' .  $requested_page . '.php';
                            include($page_uri);
                        }
                    ?>
                </div>
            </div>
            
            <script type="text/javascript" src="js/footer.js"></script>
            
            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>
            </div>
        </div>
    </body>
</html>