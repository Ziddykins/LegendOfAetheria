<?php
    declare(strict_types = 1);
    use Game\Account\Account;
    use Game\Character\Character;
 
     
    require_once '../../bootstrap.php';
    require_once 'system/functions.php';

    $account = null;
    $character = null;

    if (check_session()) {
        $account = new Account($_SESSION['email']);
        $character = new Character($account->get_id(), $_SESSION['character-id']);
        $character->load();
    } else {
        header('Location: /?no_login');
    }
?>

<?php include WEBROOT . '/html/opener.html'; ?>
    <head>
        <?php include PATH_ADMINROOT . '/html/headers.html'; ?>
        
    </head>

    <body class="main-font layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="app-wrapper">
            <nav class="app-header navbar navbar-expand bg-body">
                <div class="container-fluid">

                    <?php include PATH_ADMINROOT . '/components/nav-links.html'; ?>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                                <i class="bi bi-search"></i>
                            </a>
                        </li>

                        <?php include PATH_ADMINROOT . '/components/messages.html'; ?>

                        <?php include PATH_ADMINROOT . '/components/notifications.html'; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick=flipmode();>
                                <i class="bi bi-cloud-sun"></i>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-envelope-open"></i>
                            </a>
                        </li>

                        <?php include PATH_ADMINROOT . '/components/usermenu.html'; ?>
                    </ul>
                </div>
            </nav>

            <?php include PATH_ADMINROOT . '/components/sidebar.html'; ?>

            <main class="app-main">
                <div class="app-content-header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3 class="mb-0">Dashboard</h3>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="app-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon text-bg-primary shadow-sm">
                                        <i class="bi bi-gear-fill"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">CPU Traffic</span>
                                        <span class="info-box-number">10<small>%</small></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon text-bg-danger shadow-sm">
                                        <i class="bi bi-hand-thumbs-up-fill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Likes</span>
                                        <span class="info-box-number">41,410</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon text-bg-success shadow-sm">
                                        <i class="bi bi-cart-fill"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Sales</span>
                                        <span class="info-box-number">760</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon text-bg-warning shadow-sm">
                                        <i class="bi bi-people-fill"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">New Members</span>
                                        <span class="info-box-number"><?php echo new_members_count(); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <div class="dropdown">
                                <a class="btn btn-tool fs-3" href="#" role="button" id="widget-add" data-bs-toggle="dropdown" aria-expanded="false">
                                    +
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="widget-add">
                                    <li><a class="dropdown-item" href="#">Action 1</a></li>
                                    <li><a class="dropdown-item" href="#">Action 2</a></li>
                                    <li><a class="dropdown-item" href="#">Action 3</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-evenly">
                            <div id="direct-chat-container" class="mb-3 ms-3">
                                <?php include PATH_ADMINROOT . '/widgets/globalchat.php' ?>
                            </div>

                            <div class="mb-3 ms-3">
                                <?php include PATH_ADMINROOT . '/widgets/browserstats.html'; ?>
                            </div>

                            <div class="mb-3 ms-3 w-50">
                                <?php include PATH_ADMINROOT . '/widgets/newmembers.html'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">

                    </div>
                </div>
            </main>
            <?php include PATH_ADMINROOT . '/html/footers.html'; ?>
        </div>
    </body>
</html>
