<?php
    declare(strict_types = 1);
    require_once "bootstrap.php";

    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
    use Game\Character\Character;
    use Game\Components\Sidebar\Enums\SidebarType;
    use Game\System\System;
    use Game\Account\Settings;
    //use Game\Familiar\Familiar;

    $system = new System(0);
    $system->load_sheet();

    $account = new Account($_SESSION['email']);
    $character = new Character($account->get_id(), $_SESSION['character-id']);
    
    $color_mode = $account->get_settings()->get_colorMode();
    
    $account->get_settings()->set_sideBar(SidebarType::LTE_DEFAULT);
    $sidebar_rel_link = $account->get_settings()->get_sideBar()->value;

    //$familiar = new Familiar($character->get_id(), $_ENV['SQL_FMLR_TBL']);
    //$familiar->loadFamiliar($character->get_id());

    $_SESSION['name'] = $character->get_name();
    $cur_floor        = $character->get_floor();
    $avatar           = $character->get_avatar();
?>

<?php include 'html/opener.html'; ?>
    <head>
        <?php include 'html/headers.html'; ?>
    </head>
    <script>loa.u_name = <?php echo $_SESSION['name']; ?>;</script>
        
    <body class="main-font" data-bs-theme="<?php echo $color_mode; ?>" data-overlayscrollbars-initialize> 
        <div class="container-fluid overflow-hidden" style="height: 100vh;">
            <span id="terst" class="row g-0 h-100 app-wrapper layout-fixed sidebar-expand-lg ms-n3">
                <?php include $sidebar_rel_link; ?>
                <main id="main-section" class="col ps-1 border border-success overflow-hidden" style="height: 100vh; max-height: 100vh; background: rgba(15,15,15,0.6);" data-overlayscrollbars-initialize>
                    <div class="d-grid ms-3 mt-3">
                            <?php
                                $privileges = $account->get_privileges()->value;
                                    
                                if ($privileges == Privileges::UNVERIFIED->value) {
                                    include 'html/verify.html';
                                    exit();
                                }

                                if (isset($_GET['page'])) {
                                    $requested_page = null;
                                    $requestion_sub = null;
                                    $page_string    = null;
                                    
                                    $requested_page = preg_replace('/[^a-z-]+/', '', $_GET['page']);
                                    $page_string = "pages/";

                                    if (isset($_GET['sub'])) {
                                        $requested_sub = preg_replace('/[^a-z-]+/', '', $_GET['sub']);
                                        $page_string .= "$requested_sub/$requested_page.php";
                                    } else {
                                        $page_string .= "pages/character/sheet.php";
                                    }

                                    if (file_exists($page_string)) {
                                        include "$page_string";
                                    } else {
                                        include 'pages/character/sheet.php';
                                    }
                                }
                            ?>                        
                    </div>

                    

                </main>
                <?php include 'html/chat.html'; ?>
            </span>
        </div>
            
            
            <div id="footer"> 
                <?php include 'html/footers.html'; ?>
            </s>

            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>
                    <!-- Toast placeholder -->
                </div>
            </div>
        </div>
        <script>OverlayScrollbars(document.querySelector('#main-section'), {  overflow: { x: 'hidden' }, scrollbars: { theme: 'os-theme-light'}});</script>
    </body>
</html>
