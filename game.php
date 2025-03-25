<?php
    declare(strict_types = 1);
    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
    use Game\Character\Character;
    use Game\Components\Sidebar\Enums\SidebarType;
      
    //use Game\Familiar\Familiar;


    session_start();
    require_once "bootstrap.php";
    $system->load_sheet();
    
    if (check_session() === true) {
        if (!isset($_SESSION['csrf-token'])) {
            $_SESSION['csrf-token'] = gen_csrf_token();
        }
        
        $account = new Account($_SESSION['email']);
        $account->load();

        $color_mode = $account->get_settings()->get_colorMode();

        $character = new Character($account->get_id());
        $character->set_id($_SESSION['character-id']);
        $character->load();

        $account->get_settings()->set_sideBar(SidebarType::LTE_DEFAULT);
        $sidebar_rel_link = $account->get_settings()->get_sideBar()->value;

        //$familiar = new Familiar($character->get_id(), $_ENV['SQL_FMLR_TBL']);
        //$familiar->loadFamiliar($character->get_id());

        $_SESSION['name'] = $character->get_name();
        $cur_floor        = $character->get_floor();
        $avatar           = $character->get_avatar();
    } else {
        header('Location: /?no_login');
        exit();
    }
?>

<?php include 'html/opener.html'; ?>
    <head>
        <?php include 'html/headers.html'; ?>
    </head>
        
    <body class="main-font bg-body-tertiary" data-bs-theme="<?php echo $color_mode; ?>"> 
        <div class="container-fluid border">
            <div class="app-wrapper layout-fixed sidebar-expand-lg">
                <div class="row flex-nowrap" style="min-height: 99.5vh!important;">
            
                    <?php include $sidebar_rel_link; ?>

                    <div id="content" name="content" class="container border border-danger" style="flex-shrink: 1;">

                    <?php
                            $privileges = Privileges::name_to_value($account->get_privileges());
                            
                            if ($privileges == Privileges::UNVERIFIED->value) {
                                include 'html/verify.html';
                                exit();
                            }
                            
                            include 'navs/nav-summary.php';

                            if (isset($_GET['page'])) {
                                $requested_page = preg_replace('/[^a-z-]+/', '', $_GET['page']);
                                if (file_exists("pages/game-$requested_page.php")) {
                                    $page_uri = "pages/game-$requested_page.php";
                                } else {
                                    $page_uri = "pages/game-sheet.php";
                                }
                                include (string) $page_uri;
                            } else {
                                $page_uri = 'pages/game-sheet.php';
                                include $page_uri;
                            }
                        ?>
                    </div>
                </div>

                <div id="footer"> 
                    <?php include 'html/footers.html'; ?>
                </div>

                <div aria-live="polite" aria-atomic="true" class="position-relative">
                    <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>
                        <!-- Toast placeholder -->
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
