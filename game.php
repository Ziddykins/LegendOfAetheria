<?php
    declare(strict_types = 1);
    session_start();
    require __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    include 'logger.php';
    require_once 'db.php';
    include 'constants.php';
    include 'functions.php';

    $account   = get_user($_SESSION['email'], 'account');
    $character = get_user($account['id'], 'character');

    $char_menu_icon = $character['hp'] > 0 ? 'bi-emoji-laughing-fill' : 'bi-emoji-dizzy-fill';

    $_SESSION['name'] = $character['name'];

    /* First make sure the user is logged in before doing anything */
    if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 1) {
        /* Check if the user has clicked the apply button on the profile tab */
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
                            <img src="img/logos/logo-banner-no-bg.png" class="mt-4 w-100">
                        </a>

                        <hr style="width: 35%; opacity: .25; align-self: center;">

                        <div class="w-100 pb-3 text-center">
                            <ul class="nav flex-column mb-0 align-items-center align-items-sm-start mb-sm-auto" id="menu">
                                <li class="border rounded w-100">
                                    <a href="#menu-header-character" id="menu-anchor-character" name="menu-anchor-character" class="nav-link px-0 align-middle bg-primary text-white" data-bs-toggle="collapse">
                                        <i class="fs-4 bi <?php echo $char_menu_icon; ?>"></i>
                                        <span class="ms-1 d-none d-md-inline fs-6 text-start">Character</span>
                                    </a>
                                    
                                    <ul id="menu-header-character" class="nav collapse flex-column text-start" data-bs-parent="#menu" aria-expanded="false">
                                        <li>
                                            <a href="?page=sheet" id="menu-sub-sheet" name="menu-sub-sheet" class="nav-link px-0">
                                                <i class="bi bi-card-text ms-2"></i>
                                                <span class="d-none d-lg-inline"> Sheet</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-inventory" name="menu-sub-inventory" class="nav-link px-0">
                                                <i class="bi bi-box ms-2"></i>
                                                <span class="d-none d-lg-inline"> Inventory</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-skills" name="menu-sub-skills" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">book_2</span>
                                                <span class="d-none d-lg-inline">Skills</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-spells" name="menu-sub-spells" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">book</span>
                                                <span class="d-none d-lg-inline">Spells</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-train" name="menu-sub-trail" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">fitness_center</span>
                                                <span class="d-none d-lg-inline">Train</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="border w-100">
                                    <a href="#menu-header-travel" id="menu-anchor-location" name="menu-anchor-location" class="nav-link px-0 align-middle" data-bs-toggle="collapse">
                                        <i class="fs-4 bi bi-signpost-split-fill"></i>
                                        <span class="ms-1 d-none d-md-inline fs-6">Location</span>
                                    </a>
                                
                                    <ul class="collapse nav flex-column ms-1 text-start" id="menu-header-travel" data-bs-parent="#menu" aria-expanded="false">
                                        <li>
                                            <a href="?page=hunt" id="menu-sub-hunt" name="menu-sub-hunt" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">cruelty_free</span>
                                                <span class="d-none d-lg-inline">Hunt</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-map" name="menu-sub-map" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">map</span>
                                                <span class="d-none d-lg-inline">Map</span>
                                            </a>
                                        </li>
                                        <li class="w-100">
                                            <a href="?page=" id="menu-sub-explore" name="menu-sub-explore" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">travel_explore</span>
                                                <span class="d-none d-lg-inline">Explore</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-zone" name="menu-sub-zone" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">move_location</span>
                                                <span class="d-none d-lg-inline">Zone</span>
                                            </a>
                                        </li>
                                        <li>
                                            <?php
                                                $rest_disabled = '';
                                                if ($character['hp'] === $character['max_hp']) {
                                                    $rest_disabled = 'disabled';
                                                }
                                            ?>
                                            <a href="?page=" id="menu-sub-rest" name="menu-sub-rest" class="nav-link d-flex align-items-center px-0 <?php echo $rest_disabled; ?>">
                                                <span class="material-symbols-sharp ms-2 me-2">nights_stay</span>
                                                <span class="d-none d-lg-inline"> Rest</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="border w-100">
                                    <a href="#menu-header-dungeon" id="menu-anchor-dungeon" name="menu-anchor-dungeon" class="nav-link px-0 align-middle" data-bs-toggle="collapse">
                                        <i class="fs-4 bi bi-bricks"></i>
                                        <span class="ms-1 d-none d-md-inline fs-6">Dungeon</span>
                                    </a>

                                    <ul id="menu-header-dungeon" class="collapse nav flex-column ms-1 text-start" data-bs-parent="#menu" aria-expanded="false">
                                        <li>
                                            <a href="?page=" id="menu-sub-floor" name="menu-sub-floor" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">floor</span>
                                                <span class="d-none d-lg-inline">Floor <?php echo $character['floor']; ?></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" id="menu-sub-reset" name="menu-sub-reset" class="nav-link px-0" data-bs-toggle="modal" data-bs-target="#reset-modal" >
                                                <span class="material-symbols-sharp ms-2 text-danger">restart_alt</span>
                                                <span class="d-none d-lg-inline text-danger">Reset</span>
                                            </a>
                                            <div class="modal fade" id="reset-modal" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-bg-danger">
                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Reset Dungeon Progress</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            This will reset your dungeon progress from floor <?php echo $character['floor']; ?> to floor 1 and return your dungeon multiplier back to 1x<br /><br />
                                                            <strong>Are you sure? This cannot be reversed!</strong>
                                                        </div>
                                                        <form id="modal-dungeon-reset" name="modal-dungeon-reset" action="?page=dungeon&action=reset" method="POST">
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <input type="submit" class="btn btn-danger" value="Reset">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>


                                        </li>
                                    </ul>
                                </li>

                                <li class="border w-100">
                                    <a href="#menu-header-quests" id="menu-anchor-quests" name="menu-anchor-quests" class="nav-link px-0" data-bs-toggle="collapse">
                                        <i class="fs-4 bi bi-clipboard-fill"></i>
                                        <span class="ms-1 d-none d-md-inline fs-6">Quests</span>
                                    </a>

                                    <ul id="menu-header-quests" name="menu-header-quests" class="collapse nav flex-column ms-1 text-start" data-bs-parent="#menu">
                                        <li>
                                            <a href="?page=" id="menu-sub-active" name="menu-sub-active" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">pending_actions</span>
                                                <span class="d-none d-lg-inline">Active</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-completed" name="menu-sub-completed" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">inventory</span>
                                                <span class="d-none d-lg-inline">Completed</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-achievements" name="menu-sub-achievements" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">military_tech</span>
                                                <span class="d-none d-lg-inline">Achievements</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?page=" id="menu-sub-awards" name="menu-sub-awards" class="nav-link px-0">
                                                <span class="material-symbols-sharp ms-2">trophy</span>
                                                <span class="d-none d-lg-inline">Awards</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>    
                        
                        <hr style="width: 35%; opacity: .25; align-self: center;">

                        <div id="bottom-menu" name="bottom-menu" class="ms-3">
                            <a href="#" 
                                class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                                id="dropdownUser1"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                
                                <img src="img/avatars/<?php echo $character['avatar']; ?>"
                                     alt="avatar"
                                     width="50"
                                     height="50"
                                     class="rounded-circle"
                                />
                                <span class="d-none d-xl-inline mx-1 ms-3 fs-6">Account</span>
                            </a>
                        
                            <ul class="dropdown-menu dropdown-menu text-small shadow">
                                <li>
                                    <a class="dropdown-item" href="?page=profile">Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?page=friends">Friends
                                    <?php
                                        $requests = get_friend_counts('requests');
                                        $pill_bg  = 'bg-danger';

                                        if (!$requests) {
                                            $pill_bg = 'bg-primary';
                                        }
                                    ?>
                                    <span class="badge <?php echo $pill_bg; ?> rounded-pill"> <?php echo $requests; ?></span>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?page=mail">Mail
                                    <?php
                                        $unread_mail = check_mail('unread', $account['id']);
                                        $pill_bg = 'bg-danger';
    
                                        if ($unread_mail == 0) {
                                                $pill_bg = 'bg-primary';
                                        }
                                    ?>
                                        <span class="badge <?php echo $pill_bg; ?> rounded-pill"> <?php echo $unread_mail; ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?page=settings">Settings</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <?php
                                    $privileges = UserPrivileges::name_to_value($account['privileges']);
                                    
                                    if ($privileges > UserPrivileges::MODERATOR->value) {
                                        echo "<li>\n\t\t\t\t\t\t\t\t\t";
                                        echo '<a class="dropdown-item" href="?page=administrator">Administrator</a>';
                                        echo "\n\t\t\t\t\t\t\t\t</li>\n";
                                    }
                                ?>
                                <li>
                                    <a class="dropdown-item" href="/logout">Sign out</a>
                                </li>
                            </ul>
                        </div>
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
                            include "$page_uri";
                        }
                    ?>
                </div>
            </div>
            
            <?php include 'html/footer.html'; ?>
            
            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>
            </div>
        </div>
    </body>
</html>
