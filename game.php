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

    if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 1) {
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_ACCT_TBL'] . ' WHERE email = ?';
        $prepped   = $db->prepare($sql_query);
        $prepped->bind_param('s', $_SESSION['email']);
        $prepped->execute();
        $result = $prepped->get_result();

        $account = $result->fetch_assoc();

        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_CHAR_TBL'] . ' WHERE account_id = ' . $account['id'];
        $result  = $db->query($sql_query);

        $character = $result->fetch_assoc();
    } else {
        header('Location: /?failed_login');
        exit();
    }
?>

<?php include('html/opener.html'); ?>
    <head>
        <?php include('html/headers.html'); ?><!-- :D -->
        <style>
            body {
                min-height: 100vh;
                min-height: -webkit-fill-available;
            }
            html {
                height: -webkit-fill-available;
            }
            main {
                height: 100vh;
                height: -webkit-fill-available;
                max-height: 100vh;
                overflow-x: auto;
                overflow-y: hidden;
            }
        </style>
    </head>
        
    <body class="bg-dark"> 
        <div class="container-fluid">
            <div class="row flex-nowrap">
                <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark border border-top-0 border-bottom-0 border-dark-subtle">
                    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                        <a href="/game" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                            <span class="fs-6 d-none d-sm-inline mt-3">
                                <img src="img/logos/logo-banner-no-bg.png" style="width: 100%;">
                            </span>
                        </a>

                        <hr style="width: 35%; opacity: .25; align-self: center;">
                        
                        <ul class="nav nav-pills flex-column mb-0 align-items-center align-items-sm-start" id="menu-character">
                            <a href="#sub-character-menu" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-house-fill"></i>
                                <span class="ms-1 d-none d-sm-inline fs-6">Character</span>
                            </a>
                            <ul class="nav collapse flex-column ms-1" id="sub-character-menu" data-bs-parent="#menu-character" aria-expanded="false">
                                <li>
                                    <a href="#" class="nav-link px-0">
                                        <span class="d-none d-sm-inline ms-4">Character Sheet</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0">
                                        <span class="d-none d-sm-inline ms-4">Inventory</span>
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
                        </ul>

                        <ul class="nav nav-pills flex-column mb-0 align-items-center align-items-sm-start" id="menu-travel">
                            <a href="#sub-travel-menu" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi bi-signpost-split-fill"></i>
                                <span class="ms-1 d-none d-sm-inline fs-6">Travel</span>
                            </a>
                            <ul class="collapse nav flex-column ms-1" id="sub-travel-menu" data-bs-parent="#menu-travel" aria-expanded="false">
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
                            </ul>
                        </ul>

                        <ul class="nav nav-pills flex-column mb-0 align-items-center align-items-sm-start" id="menu-dungeon">
                            <a href="#sub-menu-dungeon" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                                <i class="fs-4 bi bi-bricks"></i>
                                <span class="ms-1 d-none d-sm-inline fs-6">Dungeon</span>
                            </a>
                            <ul class="collapse nav flex-column ms-1" id="sub-menu-dungeon" data-bs-parent="#menu-dungeon" aria-expanded="false">
                                <li>
                                    <a href="#" class="nav-link px-0">
                                        <!-- TODO: Replace number with tbl_character.dungeon_level -->
                                        <span class="d-none d-sm-inline ms-4">Floor 1</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0">
                                        <span class="d-none d-sm-inline text-danger ms-4">Reset</span>
                                    </a>
                                </li>
                            </ul>
                        </ul>

                        <ul class="nav nav-pills flex-column mb-0 align-items-center align-items-sm-start" id="menu-quests">
                            <a href="#sub-menu-quests" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi bi-clipboard-fill"></i>
                                <span class="ms-1 d-none d-sm-inline fs-6">Quests</span>
                            </a>
                            <ul class="collapse nav flex-column ms-1" id="sub-menu-quests" data-bs-parent="#menu-quests">
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
                        </ul>
                    </div>

                    <div class="dropdown pb-4 fixed-bottom ms-4">
                        <hr style="width: 100%; border-style: dashed; opacity: .25">

                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="img/avatars/<?php echo $character['avatar']; ?>" alt="avatar" width="50" height="50" class="rounded-circle" />
                            <span class="d-none d-sm-inline mx-1 ms-3 fs-6">
                                Account
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                            <li>
                                <a class="dropdown-item" href="?page=profile">Profile</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="?page=settings">Settings</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="/logout">Sign out</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col text-dark-emphasis">
                    <div class="row bg-black justify-content-center row-cols-auto">
                        <div class="col"><i class="bi bi-currency-exchange text-warning"></i> 9,894</div> |
                        <div class="col text-danger"><i class="bi bi-emoji-dizzy-fill"></i> Dead</div> |
                        <div class="col text-primary"><i class="bi bi-cloud-drizzle-fill"></i> Raining</div> |
                        <div class="col"><i class="bi bi-ladder"></i> 42</div> |
                        <div class="col"><i class="bi bi-envelope-fill"></i> 0</div> |
                        <div class="col"><i class="bi bi-clipboard-fill"></i> 5</div> |
                        <div class="col"><i class="bi bi-box2-heart-fill"></i><span class="text-warning"> 42/50</span></div> |
                        <div class="col"><i class="bi bi-bookmark-fill"></i> 7</div> |
                        <div class="col text-white"><i class="bi bi-hourglass-split"></i><span id="tick-left"></span></div> |
                        <div class="col" id="ep-status" name="ep-status">
                            <span id="ep-icon"></span>
                            <span id="ep-value">20</span>/<span id="ep-max" class="text-success">100</span>
                        </div>

                    </div>
                    <div id="content" name="content" class="container text-center">
                        <?php
                            if (isset($_GET['page'])) {
                                $requested_page = preg_replace('/[^a-z-]+/', '', $_GET['page']);
                                $page_uri = 'pages/game-' .  $requested_page . '.php';
                                include($page_uri);
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            let tick_counter = setInterval(function() {
                let tick = new Date().getTime();
                let out_string = new String();
                let cur_energy = parseInt(document.getElementById('ep-value').innerHTML);
                let max_energy = parseInt(document.getElementById('ep-max').innerHTML);
                let percent_full = Math.ceil(cur_energy / max_energy * 100);
                let icon = 'bi bi-battery-full';
                let txt_color = 'text-success';

                tick = (60 - Math.ceil(tick/1000) % 60) - 1;
                
                if (tick < 10) {
                    out_string = '0:0' + tick.toString();
                } else if (tick == 60) {
                    out_string = '1:00';
                } else {
                    out_string = '0:' + tick.toString();
                }

                document.getElementById('tick-left').innerHTML = out_string;

                if (cur_energy >= max_energy) {
                    document.getElementById('ep-value').innerHTML = Math.random(max_energy - 1);
                }

                if (percent_full > 0 && percent_full < 49) {
                    icon = 'bi bi-battery';
                    txt_color = 'text-danger';
                } else if (percent_full > 49 && percent_full < 75) {
                    icon = 'bi bi-battery-half';
                    txt_color = 'text-warning';
                }
                
                document.getElementById('ep-icon').innerHTML = '<i class="' + icon + '"></i>';
                document.getElementById('ep-status').classList.add(txt_color);

                if (tick < 1) {
                    document.getElementById('ep-icon').innerHTML = '<i class="' + icon + '"></i>';
                    document.getElementById('ep-value').innerHTML = cur_energy + 1;
                    document.getElementById('ep-status').classList.add(txt_color);
                    console.log('updaterated');
                }
                console.log(`tick ${tick} - cur_energy ${cur_energy} - max_energy ${max_energy} - %_full ${percent_full} - icon ${icon} - txt_color ${txt_color}`);
            }, 1000);
        </script>
    </body>
</html>