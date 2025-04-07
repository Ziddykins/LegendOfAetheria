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
?>

<?php include 'html/opener.html'; ?>
    <head>
        <?php include 'html/headers.html'; ?>
    </head>
        
    <body class="main-font" data-bs-theme="<?php echo $color_mode; ?>"> 
        <div class="container-fluid overflow-hidden" style="height: 100vh;">
            <span id="terst" class="row g-0 h-100 app-wrapper layout-fixed sidebar-expand-lg ms-n3">
                <?php include $sidebar_rel_link; ?>
                <main id="main-section" class="col ps-1 border border-success overflow-hidden" style="height: 100vh; max-height: 100vh;">
                    <div class="d-grid ms-3 mt-3">
                            <?php
                                $privileges = $account->get_privileges()->value;
                                    
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
                    <div id="chatbox-bottom" name="chatbox-bottom" class="position-absolute bottom-0 text-center overflow-hidden border-top" style="transition: all 0.3s ease-in-out; height: 10px; background: rgba(5,5,5,0.6); width: calc(75% + 73px); margin-left: -4px">
                        <div id="chat-handle" class="border-bottom bg-body-tertiary" style="height: 15px;">
                            <span id="open-chat" class="material-symbols-outlined" style="margin-top: -6px;">expand_less</span>
                            <span class="btn-close mt-n1" style="float: right; "></span>
                        </div>
                        <div id="chat-content" class="h-100 pt-2">
                            <!-- Chat content goes here -->
                        </div>
                        <input type=text id="chat-input" class="invisible position-absolute bottom-50 start-0 w-100 text-white font-monospace" style="display:none; margin-bottom: -50px;" maxlength="250"></input>
                    </div>
                </main>
            </>
        </div>
            
            
            <div id="footer"> 
                <?php include 'html/footers.html'; ?>
            </>

            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id='toast-container' name='toast-container'>
                    <!-- Toast placeholder -->
                </div>
            </div>
        </div>
    </body>
</html>
