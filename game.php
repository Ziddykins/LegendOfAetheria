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
                <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                        <a href="/game" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                            <span class="fs-5 d-none d-sm-inline">
                                <img src="img/logos/logo-banner-no-bg.png" style="width: 100%;">
                            </span>
                        </a>
                        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                            <li class="nav-item">
                                <a href="#" class="nav-link align-middle px-0 link-underline-opacity-0 text-white">
                                    <i class="fs-4 bi-house"></i> <span class="ms-1 d-none d-sm-inline">Character</span>
                                </a>
                            </li>
                            <li>
                                <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-speedometer2"></i>
                                    <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                                </a>
                                <ul class="collapse show nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
                                    <li class="w-100">
                                        <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Item</span> 1 </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Item</span> 2 </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-table"></i> <span class="ms-1 d-none d-sm-inline">Orders</span>
                                </a>
                            </li>
                            <li>
                                <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                                    <i class="fs-4 bi-bootstrap"></i>
                                    <span class="ms-1 d-none d-sm-inline">Bootstrap</span>
                                </a>
                                <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
                                    <li class="w-100">
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline">Item</span> 1
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline">Item</span> 2
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-grid"></i>
                                    <span class="ms-1 d-none d-sm-inline">Products</span>
                                </a>
                                <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
                                    <li class="w-100">
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline">Product</span> 1
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0">
                                            <span class="d-none d-sm-inline">Product</span> 2
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Product</span> 3</a>
                                    </li>
                                    <li>
                                        <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Product</span> 4</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Customers</span> </a>
                            </li>
                        </ul>
                        <hr>
                        <div class="dropdown pb-4">
                            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="img/avatars/<?php echo $character['avatar']; ?>" alt="avatar" width="30" height="30" class="rounded-circle">
                                <span class="d-none d-sm-inline mx-1">
                                    Account
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">

                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/logout">Sign out</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col py-3">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" style="width: 5px;" style="width: 5px;"><i class="bi bi-heart-arrow"></i></a><a href="#" class="list-group-item list-group-item-action list-group-item-primary" style="width: 5px;"><i class="bi bi-magic"></i></a><a href="#" class="list-group-item list-group-item-action list-group-item-secondary" style="width: 5px;"><i class="bi bi-shield-fill"></i></a><br />
                        <a href="#" class="list-group-item list-group-item-action list-group-item-success" style="width: 5px;"><i class="bi bi-shield-fill"></i></a><a href="#" class="list-group-item list-group-item-action list-group-item-danger" style="width: 5px;"><i class="bi bi-magic"></i></a><a href="#" class="list-group-item list-group-item-action list-group-item-warning" style="width: 5px;"><i class="bi bi-heart-arrow"></i></a><br />
                        <a href="#" class="list-group-item list-group-item-action list-group-item-info" style="width: 5px;"><i class="bi bi-magic"></i></a><a href="#" class="list-group-item list-group-item-action list-group-item-light" style="width: 5px;"><i class="bi bi-heart-arrow"></i></a><a href="#" class="list-group-item list-group-item-action list-group-item-dark" style="width: 5px;"><i class="bi bi-magic"></i></a><br />
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>