<?php
    declare(strict_types = 1);

    use Game\Account\Account;
    use Game\Account\Enums\Privileges;
    use Game\Character\Character;
    use Game\Components\Sidebar\Enums\SidebarType;
    use Game\System\System;
    //use Game\Familiar\Familiar;


    
    require_once "bootstrap.php";
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
        
    <body class="main-font bg-body-tertiary" data-bs-theme="<?php echo $color_mode; ?>" style="background: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2232%22 height=%2232%22 viewBox=%220 0 32 32%22><rect width=%2232%22 height=%2232%22 fill=%22%23161618%22/><rect x=%222%22 y=%222%22 width=%2212%22 height=%226%22 fill=%22%23222224%22 rx=%221%22/><rect x=%2216%22 y=%222%22 width=%2214%22 height=%226%22 fill=%22%23222224%22 rx=%221%22/><rect x=%222%22 y=%2210%22 width=%2214%22 height=%226%22 fill=%22%231d1d1f%22 rx=%221%22/><rect x=%2218%22 y=%2210%22 width=%2212%22 height=%226%22 fill=%22%231d1d1f%22 rx=%221%22/><rect x=%222%22 y=%2218%22 width=%2212%22 height=%226%22 fill=%22%23222224%22 rx=%221%22/><rect x=%2216%22 y=%2218%22 width=%2214%22 height=%226%22 fill=%22%23222224%22 rx=%221%22/><rect x=%222%22 y=%2226%22 width=%2214%22 height=%226%22 fill=%22%231d1d1f%22 rx=%221%22/><rect x=%2218%22 y=%2226%22 width=%2212%22 height=%226%22 fill=%22%231d1d1f%22 rx=%221%22/></svg>') repeat; background-size: 32px 32px;"> 
        <div class="container-fluid" style="background: rgba(0, 0, 0, 0.65); height: 100vh;">
            <div id="terst" class="row g-0 h-100 app-wrapper layout-fixed sidebar-expand-lg">
                <?php include $sidebar_rel_link; ?>
                <main id="main-section" class="col ps-3 ps-3  border border-danger overflow-hidden" style="height: 100vh; max-height: 100vh;">
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
                </main>
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
    </body>
</html>
